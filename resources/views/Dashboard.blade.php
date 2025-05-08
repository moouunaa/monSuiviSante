<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Tableau de Bord</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Styles -->
    <style>
        /* Styles inchangés */
        :root {
            --primary: #4034e4;
            --primary-light: #6a61ff;
            --secondary: #105cec;
            --secondary-light: #4c8aff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --light: #f3f4f6;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f9fafb;
            color: var(--dark);
            min-height: 100vh;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
        }
        
        .sidebar-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .sidebar-menu {
            list-style: none;
            margin-bottom: auto;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a, .sidebar-menu button {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Nunito', sans-serif;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active, 
        .sidebar-menu button:hover, .sidebar-menu button.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu a i, .sidebar-menu button i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .sidebar-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        .user-name {
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
        }
        
        .date {
            color: var(--gray);
        }
        
        .dashboard-content {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .welcome-message {
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card-title {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        /* Goals Card */
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .goal-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }
        
        .goal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .goal-icon {
            font-size: 24px;
            margin-right: 15px;
        }
        
        .goal-title {
            font-weight: 600;
            font-size: 18px;
        }
        
        .goal-value {
            font-size: 24px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .goal-progress {
            width: 100%;
            height: 8px;
            background-color: var(--gray-light);
            border-radius: 4px;
            margin-top: auto;
            overflow: hidden;
        }
        
        .goal-progress-bar {
            height: 100%;
            background-color: var(--primary);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        
        .modal {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 700;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid var(--gray-light);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Nunito', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(64, 52, 228, 0.2);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-family: 'Nunito', sans-serif;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
        }
        
        .btn-secondary {
            background-color: var(--gray-light);
            color: var(--dark);
        }
        
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        
        /* Calculation Methods */
        .calculation-methods {
            margin-top: 20px;
        }
        
        .method-card {
            background-color: var(--light);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .method-card:hover {
            background-color: #e5e7eb;
        }
        
        .method-card.selected {
            border-color: var(--primary);
            background-color: rgba(64, 52, 228, 0.05);
        }
        
        .method-name {
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        
        .method-description {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .method-calories {
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 15px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .goals-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div x-data="{ 
        showGoalModal: false,
        calculationMethod: '{{ $calorieGoal->calculation_method ?? 'mifflin_st_jeor' }}',
        customCalories: {{ $calorieGoal->custom_value ?? 2000 }},
        calculationMethods: [],
        loading: false,
        
        fetchCalorieGoal() {
            this.loading = true;
            axios.get('{{ route('goals.calories') }}')
                .then(response => {
                    if (response.data.success) {
                        this.calculationMethods = response.data.calculationMethods;
                        const goal = response.data.goal;
                        this.calculationMethod = goal.calculation_method;
                        this.customCalories = goal.custom_value || 2000;
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des objectifs:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        
        updateGoal() {
            this.loading = true;
            axios.post('{{ route('goals.update') }}', {
                calculation_method: this.calculationMethod,
                custom_value: this.customCalories,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour des objectifs:', error);
                alert('Une erreur est survenue lors de la mise à jour des objectifs');
            })
            .finally(() => {
                this.loading = false;
            });
        }
    }" x-init="fetchCalorieGoal()">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}" class="active"><i>📊</i> Tableau de bord</a></li>
                    <li><a href="{{ route('food-tracking') }}"><i>🍽️</i> Repas</a></li>
                    <li><a href="{{ route('exercise-tracking') }}"><i>🏋️</i> Exercices</a></li>
                    <li><a href="{{ route('water-tracking') }}"><i>💧</i> Hydratation</a></li>
                    <li><a href="{{ route('sleep-tracking') }}"><i>😴</i> Sommeil</a></li>
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
                        <h1 class="page-title">Tableau de bord</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showGoalModal = true">
                        Définir un objectif
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <div class="welcome-message">
                        Bienvenue, {{ $user->name }} ! Voici votre suivi quotidien.
                    </div>
                    
                    <!-- Goals Section -->
                    <div class="goals-grid">
                        <!-- Calorie Goal -->
                        <div class="goal-card">
                            <div class="goal-header">
                                <div class="goal-icon">🔥</div>
                                <div class="goal-title">Calories</div>
                            </div>
                            <div class="goal-value">
                                {{ $caloriesConsumed }} / {{ $calorieGoal->target_value ?? 2000 }} kcal
                            </div>
                            <div class="goal-progress">
                                <div class="goal-progress-bar" style="width: {{ min(100, ($caloriesConsumed / ($calorieGoal->target_value ?? 2000)) * 100) }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Water Goal -->
                        <div class="goal-card">
                            <div class="goal-header">
                                <div class="goal-icon">💧</div>
                                <div class="goal-title">Hydratation</div>
                            </div>
                            <div class="goal-value">
                                0 / {{ $waterGoal->target_value ?? 2000 }} ml
                            </div>
                            <div class="goal-progress">
                                <div class="goal-progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Sleep Goal -->
                        <div class="goal-card">
                            <div class="goal-header">
                                <div class="goal-icon">😴</div>
                                <div class="goal-title">Sommeil</div>
                            </div>
                            <div class="goal-value">
                                0 / {{ ($sleepGoal->target_value ?? 480) / 60 }} heures
                            </div>
                            <div class="goal-progress">
                                <div class="goal-progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Today's Meals -->
                    <div class="card">
                        <div class="card-title">Repas d'aujourd'hui</div>
                        @if(isset($todayMeals) && count($todayMeals) > 0)
                            <ul style="list-style: none; padding: 0;">
                                @foreach($todayMeals as $entry)
                                    <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;">
                                        <div style="font-weight: 600;">{{ $entry->food_name }}</div>
                                        <div style="color: #6b7280; font-size: 14px;">{{ \Carbon\Carbon::parse($entry->entry_time)->format('H:i') }} - {{ ucfirst($entry->meal_type) }}</div>
                                        <div style="margin-top: 5px;">
                                            {{ $entry->calories }} kcal - {{ $entry->portion_size }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Vous n'avez pas encore enregistré de repas aujourd'hui.</p>
                            <p style="margin-top: 15px;">
                                <a href="{{ route('food-tracking') }}" class="btn btn-primary">Ajouter un repas</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Goal Setting Modal -->
            <div class="modal-overlay" x-show="showGoalModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showGoalModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Définir un objectif calorique</h3>
                        <button class="modal-close" @click="showGoalModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Votre objectif calorique actuel est de <strong>{{ $calorieGoal->target_value ?? 2000 }} kcal</strong> par jour.</p>
                        
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="calculationMethod">Méthode de calcul</label>
                            <select id="calculationMethod" class="form-control" x-model="calculationMethod">
                                <option value="mifflin_st_jeor">Mifflin-St Jeor (Recommandée)</option>
                                <option value="harris_benedict">Harris-Benedict</option>
                                <option value="katch_mcardle">Katch-McArdle</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>
                        
                        <div class="form-group" x-show="calculationMethod === 'custom'">
                            <label for="customCalories">Calories personnalisées</label>
                            <input type="number" id="customCalories" class="form-control" placeholder="Ex: 2000" x-model="customCalories" min="1000" max="5000">
                            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                Entrez une valeur entre 1000 et 5000 calories.
                            </div>
                        </div>
                        
                        <div class="calculation-methods" x-show="calculationMethod !== 'custom' && calculationMethods.length > 0">
                            <template x-for="method in calculationMethods" :key="method.id">
                                <div 
                                    class="method-card" 
                                    :class="{ 'selected': calculationMethod === method.id }"
                                    @click="calculationMethod = method.id"
                                >
                                    <div class="method-name">
                                        <span x-text="method.name"></span>
                                        <span x-text="method.calories + ' kcal'" class="method-calories"></span>
                                    </div>
                                    <div class="method-description" x-text="method.description"></div>
                                </div>
                            </template>
                        </div>
                        
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                            <p><strong>Note:</strong> Les objectifs d'hydratation (2L) et de sommeil (7-8h) sont fixés par défaut et ne peuvent pas être modifiés pour le moment.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showGoalModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="updateGoal()" :disabled="loading">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>