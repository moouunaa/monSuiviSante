<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi Alimentaire</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Styles (même CSS que dashboard.blade.php) -->
    <style>
        /* Copiez le CSS du dashboard.blade.php ici */
        
        /* Styles spécifiques pour le suivi alimentaire */
        .food-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .food-table th, .food-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .food-table th {
            font-weight: 600;
            color: var(--gray);
        }
        
        .food-table tr:last-child td {
            border-bottom: none;
        }
        
        .food-table tr:hover {
            background-color: #f9fafb;
        }
        
        .meal-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .meal-type.breakfast {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .meal-type.lunch {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .meal-type.dinner {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .meal-type.snack {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray);
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            color: var(--primary);
        }
        
        .action-btn.delete:hover {
            color: var(--danger);
        }
    </style>
</head>
<body>
    <div x-data="{ 
        showFoodModal: false,
        mealType: 'breakfast',
        foodName: '',
        calories: '',
        portionSize: '',
        foodEntries: {{ Illuminate\Support\Js::from($foodEntries) }},
        recentEntries: {{ Illuminate\Support\Js::from($recentEntries) }},
        
        addFoodEntry() {
            if (!this.foodName || !this.calories || !this.portionSize) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            axios.post('{{ route('food-tracking.store') }}', {
                food_name: this.foodName,
                meal_type: this.mealType,
                calories: this.calories,
                portion_size: this.portionSize,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    this.foodEntries.push(response.data.entry);
                    this.resetForm();
                    this.showFoodModal = false;
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'ajout du repas:', error);
                alert('Une erreur est survenue lors de l\'ajout du repas');
            });
        },
        
        deleteFoodEntry(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?')) {
                axios.delete(`/food-tracking/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        this.foodEntries = this.foodEntries.filter(entry => entry.id !== id);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression:', error);
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        },
        
        cloneFoodEntry(id) {
            axios.post(`/food-tracking/${id}/clone`, {
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    this.foodEntries.push(response.data.entry);
                }
            })
            .catch(error => {
                console.error('Erreur lors du clonage:', error);
                alert('Une erreur est survenue lors du clonage');
            });
        },
        
        cloneFromRecent(entry) {
            this.foodName = entry.food_name;
            this.calories = entry.calories;
            this.portionSize = entry.portion_size;
            this.mealType = entry.meal_type;
        },
        
        resetForm() {
            this.foodName = '';
            this.calories = '';
            this.portionSize = '';
            this.mealType = 'breakfast';
        },
        
        getMealTypeLabel(type) {
            const types = {
                'breakfast': 'Petit-déjeuner',
                'lunch': 'Déjeuner',
                'dinner': 'Dîner',
                'snack': 'Collation'
            };
            return types[type] || type;
        },
        
        getTotalCalories() {
            return this.foodEntries.reduce((total, entry) => total + parseInt(entry.calories), 0);
        }
    }">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}"><i>📊</i> Tableau de bord</a></li>
                    <li><button @click="showFoodModal = true" class="active"><i>🍽️</i> Ajouter un repas</button></li>
                    <li><a href="#"><i>🏋️</i> Exercices</a></li>
                    <li><a href="#"><i>💧</i> Hydratation</a></li>
                    <li><a href="#"><i>😴</i> Sommeil</a></li>
                    <li><a href="#"><i>⚙️</i> Paramètres</a></li>
                </ul>
                
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i>👤</i>
                        </div>
                        <div>
                            <div class="user-name">{{ $user->name }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="color: white; opacity: 0.8; text-decoration: none; display: block; text-align: center; margin-top: 10px; background: none; border: none; width: 100%; cursor: pointer;">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Suivi Alimentaire</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showFoodModal = true">
                        Ajouter un repas
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <div class="card">
                        <div class="card-title">Repas d'aujourd'hui</div>
                        <div x-show="foodEntries.length === 0">
                            <p>Vous n'avez pas encore enregistré de repas aujourd'hui.</p>
                        </div>
                        <div x-show="foodEntries.length > 0">
                            <p>Total des calories: <strong x-text="getTotalCalories() + ' kcal'"></strong></p>
                            <table class="food-table">
                                <thead>
                                    <tr>
                                        <th>Repas</th>
                                        <th>Aliment</th>
                                        <th>Calories</th>
                                        <th>Portion</th>
                                        <th>Heure</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="entry in foodEntries" :key="entry.id">
                                        <tr>
                                            <td>
                                                <span :class="'meal-type ' + entry.meal_type" x-text="getMealTypeLabel(entry.meal_type)"></span>
                                            </td>
                                            <td x-text="entry.food_name"></td>
                                            <td x-text="entry.calories + ' kcal'"></td>
                                            <td x-text="entry.portion_size"></td>
                                            <td x-text="entry.entry_time"></td>
                                            <td>
                                                <button class="action-btn" @click="cloneFoodEntry(entry.id)" title="Cloner">🔄</button>
                                                <button class="action-btn delete" @click="deleteFoodEntry(entry.id)" title="Supprimer">🗑️</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Food Modal -->
            <div class="modal-overlay" x-show="showFoodModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showFoodModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter un repas</h3>
                        <button class="modal-close" @click="showFoodModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="foodForm" @submit.prevent="addFoodEntry">
                            <div class="form-group">
                                <label for="mealType">Type de repas</label>
                                <select id="mealType" class="form-control" x-model="mealType">
                                    <option value="breakfast">Petit-déjeuner</option>
                                    <option value="lunch">Déjeuner</option>
                                    <option value="dinner">Dîner</option>
                                    <option value="snack">Collation</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="foodName">Nom de l'aliment</label>
                                <input type="text" id="foodName" class="form-control" placeholder="Ex: Poulet grillé" x-model="foodName" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="calories">Calories (kcal)</label>
                                    <input type="number" id="calories" class="form-control" placeholder="Ex: 350" x-model="calories" required>
                                </div>
                                <div class="form-group">
                                    <label for="portionSize">Taille de la portion</label>
                                    <input type="text" id="portionSize" class="form-control" placeholder="Ex: 100g" x-model="portionSize" required>
                                </div>
                            </div>
                        </form>
                        
                        <div class="saved-meals" x-show="recentEntries.length > 0">
                            <h4 class="saved-meals-title">Repas récents</h4>
                            <div class="meal-list">
                                <template x-for="entry in recentEntries" :key="entry.id">
                                    <div class="meal-item" @click="cloneFromRecent(entry)">
                                        <div class="meal-name" x-text="entry.food_name"></div>
                                        <div class="meal-calories" x-text="entry.calories + ' kcal'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showFoodModal = false">Annuler</button>
                        <button type="button" class="btn btn-primary" @click="addFoodEntry()">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>