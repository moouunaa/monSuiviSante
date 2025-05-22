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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div x-data="{ 
        showFoodModal: false,
        showCustomFoodModal: false,
        showDeleteConfirmModal: false,
        
        // New properties for food selection
        selectedFood: '',
        selectedFoodType: '',
        foodQuantity: 100,
        foodUnit: 'g',
        
        // Initialize foods arrays
        foods: {{ Illuminate\Support\Js::from($foods) }},
        customFoods: {{ Illuminate\Support\Js::from($customFoods) }},
        
        // Food tracking
        foodEntryDate: '{{ now()->format('Y-m-d') }}',
        foodEntryTime: '{{ now()->format('H:i') }}',
        foodNotes: '',
        selectedMealTemplate: '',
        
        // Custom food
        customFoodName: '',
        customFoodCaloriesPer100g: '',
        customFoodProteinPer100g: '',
        customFoodCarbsPer100g: '',
        customFoodFatPer100g: '',
        customFoodServingSize: '',
        customFoodCaloriesPerServing: '',
        
        // Meal items
        mealItems: [],
        
        // Delete confirmation
        mealToDelete: null,
        
        // Pagination
        currentPage: 1,
        totalPages: {{ $meals->lastPage() }},
        
        loading: false,
        
        // Methods for meal building
        addMealItem() {
            console.log('addMealItem called');
            console.log('selectedFood:', this.selectedFood);
            console.log('selectedFoodType:', this.selectedFoodType);
            console.log('foodQuantity:', this.foodQuantity);
            console.log('foodUnit:', this.foodUnit);
            
            if (!this.selectedFood) {
                alert('Veuillez sélectionner un aliment');
                return;
            }
            
            const quantity = parseFloat(this.foodQuantity);
            if (!quantity) {
                alert('Veuillez spécifier une quantité');
                return;
            }
            
            // Get the food name from the select element
            const foodSelect = document.getElementById('foodSelect');
            console.log('foodSelect element:', foodSelect);
            const selectedOption = foodSelect.options[foodSelect.selectedIndex];
            console.log('selectedOption:', selectedOption);
            const foodName = selectedOption.text;
            
            // Calculate calories based on food type and quantity
            let caloriesPerUnit = 0;
            if (this.selectedFoodType === 'food') {
                const food = this.foods.find(f => f.id == this.selectedFood);
                console.log('found food:', food);
                if (food) {
                    caloriesPerUnit = food.calories_per_100g;
                }
            } else {
                const food = this.customFoods.find(f => f.id == this.selectedFood);
                console.log('found custom food:', food);
                if (food) {
                    caloriesPerUnit = food.calories_per_100g;
                }
            }
            
            const calories = Math.round(caloriesPerUnit * quantity / 100);
            console.log('calculated calories:', calories);
            
            this.mealItems.push({
                food_type: this.selectedFoodType,
                food_id: this.selectedFood,
                food_name: foodName,
                quantity: quantity,
                unit: this.foodUnit,
                calories: calories
            });
            
            console.log('mealItems after push:', this.mealItems);
            
            // Reset selection
            this.foodQuantity = 100;
            this.selectedFood = '';
            this.selectedFoodType = '';
        },
        
        removeMealItem(index) {
            this.mealItems.splice(index, 1);
        },
        
        getTotalMealCalories() {
            return this.mealItems.reduce((total, item) => total + item.calories, 0);
        },
        
        cloneMeal(meal) {
            this.showFoodModal = true;
            this.mealItems = [];
            this.foodNotes = meal.notes || '';
            
            // Clone meal items
            meal.meal_items.forEach(item => {
                this.mealItems.push({
                    food_type: item.food_type,
                    food_id: item.food_id,
                    food_name: item.food.name,
                    quantity: item.quantity,
                    unit: item.unit,
                    calories: item.calories
                });
            });
        },
        
        confirmDelete(meal) {
            this.mealToDelete = meal;
            this.showDeleteConfirmModal = true;
        },
        
        deleteMeal() {
            if (!this.mealToDelete) return;
            
            this.loading = true;
            axios.delete(`/meals/${this.mealToDelete.id}`)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression du repas:', error);
                    alert('Une erreur est survenue lors de la suppression du repas');
                })
                .finally(() => {
                    this.loading = false;
                    this.showDeleteConfirmModal = false;
                });
        },
        
        // Save methods
        logMeal() {
            if (this.mealItems.length === 0) {
                alert('Veuillez ajouter au moins un aliment à votre repas');
                return;
            }
            
            this.loading = true;
            axios.post('{{ route('meals.store') }}', {
                entry_date: this.foodEntryDate,
                entry_time: this.foodEntryTime,
                notes: this.foodNotes,
                meal_items: this.mealItems,
                total_calories: this.getTotalMealCalories(),
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement du repas:', error);
                alert('Une erreur est survenue lors de l\'enregistrement du repas');
            })
            .finally(() => {
                this.loading = false;
                this.showFoodModal = false;
            });
        },
        
        saveCustomFood() {
            this.loading = true;
            axios.post('{{ route('custom-foods.store') }}', {
                name: this.customFoodName,
                calories_per_100g: this.customFoodCaloriesPer100g,
                protein_per_100g: this.customFoodProteinPer100g,
                carbs_per_100g: this.customFoodCarbsPer100g,
                fat_per_100g: this.customFoodFatPer100g,
                serving_size: this.customFoodServingSize,
                calories_per_serving: this.customFoodCaloriesPerServing,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    // Add the new custom food to the list
                    this.customFoods.push(response.data.customFood);
                    this.showCustomFoodModal = false;
                    
                    // Reset form
                    this.customFoodName = '';
                    this.customFoodCaloriesPer100g = '';
                    this.customFoodProteinPer100g = '';
                    this.customFoodCarbsPer100g = '';
                    this.customFoodFatPer100g = '';
                    this.customFoodServingSize = '';
                    this.customFoodCaloriesPerServing = '';
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement de l\'aliment personnalisé:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'aliment personnalisé');
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            window.location.href = `{{ route('food-tracking') }}?page=${page}`;
        }
    }">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}"><i>📊</i> Tableau de bord</a></li>
                    <li><a href="{{ route('food-tracking') }}" class="active"><i>🍽️</i> Repas</a></li>
                    <li><a href="{{ route('exercise-tracking') }}"><i>🏋️</i> Exercices</a></li>
                    <li><a href="{{ route('water-tracking') }}"><i>💧</i> Hydratation</a></li>
                    <li><a href="{{ route('sleep-tracking') }}"><i>😴</i> Sommeil</a></li>
                    <li><a href="{{ route('weight-tracking') }}"><i>⚖️</i> Poids</a></li>
                    <li><a href="{{ route('goals.index') }}"><i>🎯</i> Objectifs</a></li>
                    <li><a href="{{ route('profile') }}"><i>⚙️</i> Paramètres</a></li>
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
                    <div>
                        <button class="btn btn-primary" @click="showFoodModal = true; mealItems = [];">
                            + Ajouter un repas
                        </button>
                    </div>
                </div>
                
                <div class="dashboard-content">
                    <!-- Nutrition Summary -->
                    <div class="card">
                        <div class="card-title">Résumé Nutritionnel (7 derniers jours)</div>
                        <div class="nutrition-summary">
                            <div class="nutrition-item">
                                <div class="nutrition-value">{{ $weeklyStats['avgCalories'] }}</div>
                                <div class="nutrition-label">Calories/jour</div>
                            </div>
                            <div class="nutrition-item">
                                <div class="nutrition-value">{{ $weeklyStats['totalMeals'] }}</div>
                                <div class="nutrition-label">Repas</div>
                            </div>
                            <div class="nutrition-item">
                                <div class="nutrition-value">{{ $weeklyStats['avgMealsPerDay'] }}</div>
                                <div class="nutrition-label">Repas/jour</div>
                            </div>
                            <div class="nutrition-item">
                                <div class="nutrition-value">{{ $weeklyStats['mostCommonMealType'] }}</div>
                                <div class="nutrition-label">Type le plus fréquent</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calorie Chart -->
                    <div class="card">
                        <div class="card-title">Calories par jour (7 derniers jours)</div>
                        <div class="chart-container">
                            <canvas id="calorieChart"></canvas>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('calorieChart').getContext('2d');
                                
                                const calorieChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: [
                                            @foreach($dailyCalories as $date => $calories)
                                                '{{ \Carbon\Carbon::parse($date)->format('d/m') }}',
                                            @endforeach
                                        ],
                                        datasets: [{
                                            label: 'Calories',
                                            data: [
                                                @foreach($dailyCalories as $calories)
                                                    {{ $calories }},
                                                @endforeach
                                            ],
                                            backgroundColor: '#4034e4',
                                            borderRadius: 5
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: 'Calories (kcal)'
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Date'
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                    </div>
                    
                    <!-- Meal History -->
                    <div class="card">
                        <div class="card-title">Historique des Repas</div>
                        
                        @if(count($meals) > 0)
                            <div class="meal-history">
                                @php
                                    $currentDate = null;
                                @endphp
                                
                                @foreach($meals as $meal)
                                    @php
                                        $mealDate = \Carbon\Carbon::parse($meal->entry_date);
                                    @endphp
                                    
                                    @if($currentDate !== $mealDate->format('Y-m-d'))
                                        @php
                                            $currentDate = $mealDate->format('Y-m-d');
                                        @endphp
                                        <div class="meal-date-header">
                                            {{ $mealDate->format('l, d F Y') }}
                                        </div>
                                    @endif
                                    
                                    <div class="meal-item-card">
                                        <div class="meal-item-header">
                                            <div class="meal-item-title">
                                                {{ ucfirst($meal->meal_type) }} - {{ \Carbon\Carbon::parse($meal->entry_time)->format('H:i') }}
                                            </div>
                                            <div class="meal-item-calories">
                                                {{ $meal->total_calories }} kcal
                                            </div>
                                        </div>
                                        
                                        <div class="meal-item-content">
                                            <ul class="meal-foods-list">
                                                @foreach($meal->mealItems as $item)
                                                    <li>
                                                        {{ $item->food->name }} ({{ $item->quantity }}{{ $item->unit }}) - {{ $item->calories }} kcal
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            @if($meal->notes)
                                                <div class="meal-item-notes">
                                                    <strong>Notes:</strong> {{ $meal->notes }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="meal-item-actions">
                                            <button class="btn btn-sm btn-secondary" @click="cloneMeal({{ json_encode($meal) }})">
                                                Cloner
                                            </button>
                                            <button class="btn btn-sm btn-danger" @click="confirmDelete({{ json_encode($meal) }})">
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Pagination -->
                                @if($meals->lastPage() > 1)
                                    <div class="pagination">
                                        <button 
                                            class="pagination-btn" 
                                            @click="goToPage(currentPage - 1)" 
                                            :disabled="currentPage === 1"
                                        >
                                            &laquo; Précédent
                                        </button>
                                        
                                        <div class="pagination-info">
                                            Page {{ $meals->currentPage() }} sur {{ $meals->lastPage() }}
                                        </div>
                                        
                                        <button 
                                            class="pagination-btn" 
                                            @click="goToPage(currentPage + 1)" 
                                            :disabled="currentPage === totalPages"
                                        >
                                            Suivant &raquo;
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">🍽️</div>
                                <div class="empty-state-text">Vous n'avez pas encore enregistré de repas.</div>
                                <button class="btn btn-primary" @click="showFoodModal = true; mealItems = [];">
                                    + Ajouter votre premier repas
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Food Logging Modal -->
            <div class="modal-overlay" x-show="showFoodModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showFoodModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Composer un repas</h3>
                        <button class="modal-close" @click="showFoodModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Meal details -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="foodEntryDate">Date</label>
                                <input type="date" id="foodEntryDate" class="form-control" x-model="foodEntryDate" max="{{ now()->format('Y-m-d') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="foodEntryTime">Heure</label>
                                <input type="time" id="foodEntryTime" class="form-control" x-model="foodEntryTime">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Add food items section -->
                        <div class="form-group">
                            <label>Ajouter des aliments</label>
                            <div class="form-row">
                                <div class="form-group" style="flex-grow: 2;">
                                    <select id="foodSelect" class="form-control" x-model="selectedFood" @change="selectedFoodType = $event.target.options[$event.target.selectedIndex].dataset.type">
                                        <option value="">Sélectionner un aliment</option>
                                        <optgroup label="Aliments standards">
                                            @foreach($foods as $food)
                                                <option value="{{ $food->id }}" data-type="food">{{ $food->name }} ({{ $food->calories_per_100g }} kcal/100g)</option>
                                            @endforeach
                                        </optgroup>
                                        @if(count($customFoods) > 0)
                                            <optgroup label="Aliments personnalisés">
                                                @foreach($customFoods as $food)
                                                    <option value="{{ $food->id }}" data-type="custom_food">{{ $food->name }} ({{ $food->calories_per_100g }} kcal/100g)</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="number" id="foodQuantity" class="form-control" placeholder="Quantité" x-model="foodQuantity" min="1">
                                </div>
                                <div class="form-group">
                                    <select id="foodUnit" class="form-control" x-model="foodUnit">
                                        <option value="g">g</option>
                                        <option value="ml">ml</option>
                                        <option value="portion">portion</option>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" x-on:click="addMealItem()">Ajouter</button>
                                </div>
                            </div>
                            <div style="text-align: right; margin-top: 5px;">
                                <button type="button" class="btn btn-link" @click="showCustomFoodModal = true">+ Ajouter un aliment personnalisé</button>
                            </div>
                        </div>
                        
                        <!-- Meal items list -->
                        <div class="form-group" x-show="mealItems.length > 0">
                            <label>Aliments du repas</label>
                            <div class="meal-items-list">
                                <template x-for="(item, index) in mealItems" :key="index">
                                    <div class="meal-item">
                                        <div class="meal-item-info">
                                            <span x-text="item.food_name"></span>
                                            <span x-text="item.quantity + item.unit"></span>
                                            <span x-text="item.calories + ' kcal'"></span>
                                        </div>
                                        <button type="button" class="meal-item-remove" @click="removeMealItem(index)">&times;</button>
                                    </div>
                                </template>
                            </div>
                            <div class="meal-total">
                                <span>Total:</span>
                                <span x-text="getTotalMealCalories() + ' kcal'"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="foodNotes">Notes (optionnel)</label>
                            <textarea id="foodNotes" class="form-control" placeholder="Ex: Fait maison" x-model="foodNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showFoodModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logMeal()" :disabled="loading || mealItems.length === 0">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Custom Food Modal -->
            <div class="modal-overlay" x-show="showCustomFoodModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showCustomFoodModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter un aliment personnalisé</h3>
                        <button class="modal-close" @click="showCustomFoodModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customFoodName">Nom de l'aliment</label>
                            <input type="text" id="customFoodName" class="form-control" placeholder="Ex: Mon plat maison" x-model="customFoodName">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customFoodCaloriesPer100g">Calories (pour 100g)</label>
                                <input type="number" id="customFoodCaloriesPer100g" class="form-control" placeholder="Ex: 250" x-model="customFoodCaloriesPer100g" min="1">
                            </div>
                            
                            <div class="form-group">
                                <label for="customFoodProteinPer100g">Protéines (g)</label>
                                <input type="number" id="customFoodProteinPer100g" class="form-control" placeholder="Ex: 15" x-model="customFoodProteinPer100g" min="0" step="0.1">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customFoodCarbsPer100g">Glucides (g)</label>
                                <input type="number" id="customFoodCarbsPer100g" class="form-control" placeholder="Ex: 30" x-model="customFoodCarbsPer100g" min="0" step="0.1">
                            </div>
                            
                            <div class="form-group">
                                <label for="customFoodFatPer100g">Lipides (g)</label>
                                <input type="number" id="customFoodFatPer100g" class="form-control" placeholder="Ex: 10" x-model="customFoodFatPer100g" min="0" step="0.1">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customFoodServingSize">Taille de portion</label>
                                <input type="text" id="customFoodServingSize" class="form-control" placeholder="Ex: 1 assiette (250g)" x-model="customFoodServingSize">
                            </div>
                            
                            <div class="form-group">
                                <label for="customFoodCaloriesPerServing">Calories par portion</label>
                                <input type="number" id="customFoodCaloriesPerServing" class="form-control" placeholder="Ex: 625" x-model="customFoodCaloriesPerServing" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showCustomFoodModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="saveCustomFood()" :disabled="loading || !customFoodName || !customFoodCaloriesPer100g">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal-overlay" x-show="showDeleteConfirmModal" x-transition style="display: none;">
                <div class="modal modal-sm" @click.outside="showDeleteConfirmModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Confirmer la suppression</h3>
                        <button class="modal-close" @click="showDeleteConfirmModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce repas ? Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showDeleteConfirmModal = false">Annuler</button>
                        <button class="btn btn-danger" @click="deleteMeal()" :disabled="loading">
                            <span x-show="!loading">Supprimer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Nutrition Summary */
        .nutrition-summary {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .nutrition-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .nutrition-value {
            font-size: 24px;
            font-weight: bold;
            color: #4034e4;
            margin-bottom: 5px;
        }
        
        .nutrition-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        /* Chart Container */
        .chart-container {
            height: 300px;
            margin-top: 15px;
        }
        
        /* Meal History */
        .meal-history {
            margin-top: 15px;
        }
        
        .meal-date-header {
            font-weight: bold;
            padding: 10px 0;
            margin-top: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .meal-item-card {
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .meal-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .meal-item-title {
            font-weight: bold;
            font-size: 16px;
        }
        
        .meal-item-calories {
            font-weight: bold;
            color: #4034e4;
        }
        
        .meal-foods-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 10px;
        }
        
        .meal-foods-list li {
            padding: 5px 0;
            border-bottom: 1px dashed #e9ecef;
        }
        
        .meal-foods-list li:last-child {
            border-bottom: none;
        }
        
        .meal-item-notes {
            font-style: italic;
            margin-top: 10px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .meal-item-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 0;
        }
        
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .empty-state-text {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .pagination-btn {
            padding: 8px 16px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination-info {
            font-size: 14px;
            color: #6c757d;
        }
        
        /* Meal and workout items styling */
        .meal-items-list,
        .workout-items-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .meal-item,
        .workout-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .meal-item:last-child,
        .workout-item:last-child {
            border-bottom: none;
        }
        
        .meal-item-info,
        .workout-item-info {
            display: flex;
            gap: 10px;
            flex-grow: 1;
        }
        
        .meal-item-remove,
        .workout-item-remove {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 18px;
            cursor: pointer;
        }
        
        .meal-total,
        .workout-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding: 8px 12px;
            background-color: var(--light);
            border-radius: 8px;
        }
    </style>
</body>
</html>
