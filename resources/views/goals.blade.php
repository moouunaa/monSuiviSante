<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Objectifs</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Styles -->
    <style>
        /* Styles de base (repris du dashboard) */
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
        
        .goals-content {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--gray-light);
            margin-bottom: 30px;
        }
        
        .tab {
            padding: 15px 20px;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
        
        /* Goal Type Selector */
        .goal-type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .goal-type-card {
            flex: 1;
            background-color: var(--light);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }
        
        .goal-type-card:hover {
            background-color: #e5e7eb;
        }
        
        .goal-type-card.selected {
            border-color: var(--primary);
            background-color: rgba(64, 52, 228, 0.05);
        }
        
        .goal-type-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .goal-type-name {
            font-weight: 600;
        }
        
        .goal-type-description {
            color: var(--gray);
            font-size: 14px;
            margin-top: 5px;
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
            
            .goal-type-selector {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div x-data="{ 
        activeTab: 'calories',
        calculationMethod: '{{ $calorieGoal->calculation_method ?? 'mifflin_st_jeor' }}',
        customCalories: {{ $calorieGoal->custom_value ?? 2000 }},
        calculationMethods: {{ json_encode($calculationMethods ?? []) }},
        weightGoalType: '{{ $user->profile->goal ?? 'maintain' }}',
        waterTarget: {{ $waterGoal->target_value ?? 2000 }},
        sleepHours: {{ floor(($sleepGoal->target_value ?? 480) / 60) }},
        sleepMinutes: {{ ($sleepGoal->target_value ?? 480) % 60 }},
        targetWeight: {{ $weightGoal->target_value ?? ($latestWeight->weight ?? 70) }},
        targetDate: '{{ $weightGoal->target_date ?? now()->addMonths(3)->format('Y-m-d') }}',
        loading: false,
        
        updateCalorieGoal() {
            this.loading = true;
            axios.post('{{ route('goals.update') }}', {
                calculation_method: this.calculationMethod,
                custom_value: this.customCalories,
                weight_goal_type: this.weightGoalType,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    alert('Objectif calorique mis à jour avec succès!');
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
        },
        
        updateWaterGoal() {
            this.loading = true;
            axios.post('{{ route('goals.water.update') }}', {
                target_value: this.waterTarget,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    alert('Objectif d\'hydratation mis à jour avec succès!');
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
        },
        
        updateSleepGoal() {
            this.loading = true;
            axios.post('{{ route('goals.sleep.update') }}', {
                hours: this.sleepHours,
                minutes: this.sleepMinutes,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    alert('Objectif de sommeil mis à jour avec succès!');
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
        },
        
        updateWeightGoal() {
            this.loading = true;
            axios.post('{{ route('goals.weight.update') }}', {
                target_weight: this.targetWeight,
                target_date: this.targetDate,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    alert('Objectif de poids mis à jour avec succès!');
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
    }">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}"><i>📊</i> Tableau de bord</a></li>
                    <li><a href="{{ route('food-tracking') }}"><i>🍽️</i> Repas</a></li>
                    <li><a href="{{ route('exercise-tracking') }}"><i>🏋️</i> Exercices</a></li>
                    <li><a href="{{ route('water-tracking') }}"><i>💧</i> Hydratation</a></li>
                    <li><a href="{{ route('sleep-tracking') }}"><i>😴</i> Sommeil</a></li>
                    <li><a href="{{ route('weight-tracking') }}"><i>⚖️</i> Poids</a></li>
                    <li><a href="{{ route('goals.index') }}" class="active"><i>🎯</i> Objectifs</a></li>
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
                        <h1 class="page-title">Définir vos objectifs</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                </div>
                
                <div class="goals-content">
                    <!-- Tabs -->
                    <div class="tabs">
                        <div class="tab" :class="{ 'active': activeTab === 'calories' }" @click="activeTab = 'calories'">
                            🔥 Calories
                        </div>
                        <div class="tab" :class="{ 'active': activeTab === 'water' }" @click="activeTab = 'water'">
                            💧 Hydratation
                        </div>
                        <div class="tab" :class="{ 'active': activeTab === 'sleep' }" @click="activeTab = 'sleep'">
                            😴 Sommeil
                        </div>
                        <div class="tab" :class="{ 'active': activeTab === 'weight' }" @click="activeTab = 'weight'">
                            ⚖️ Poids
                        </div>
                    </div>
                    
                    <!-- Calories Tab -->
                    <div class="tab-content" :class="{ 'active': activeTab === 'calories' }">
                        <h2 style="margin-bottom: 20px;">Objectif calorique</h2>
                        
                        <div class="goal-type-selector">
                            <div class="goal-type-card" :class="{ 'selected': weightGoalType === 'lose' }" @click="weightGoalType = 'lose'">
                                <div class="goal-type-icon">📉</div>
                                <div class="goal-type-name">Perdre du poids</div>
                                <div class="goal-type-description">Déficit calorique pour perdre du poids</div>
                            </div>
                            <div class="goal-type-card" :class="{ 'selected': weightGoalType === 'maintain' }" @click="weightGoalType = 'maintain'">
                                <div class="goal-type-icon">⚖️</div>
                                <div class="goal-type-name">Maintenir le poids</div>
                                <div class="goal-type-description">Équilibre calorique pour maintenir le poids</div>
                            </div>
                            <div class="goal-type-card" :class="{ 'selected': weightGoalType === 'gain' }" @click="weightGoalType = 'gain'">
                                <div class="goal-type-icon">📈</div>
                                <div class="goal-type-name">Prendre du poids</div>
                                <div class="goal-type-description">Surplus calorique pour prendre du poids</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
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
                        
                        <div style="margin-top: 30px; text-align: right;">
                            <button class="btn btn-primary" @click="updateCalorieGoal()" :disabled="loading">
                                <span x-show="!loading">Enregistrer</span>
                                <span x-show="loading">Chargement...</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Water Tab -->
                    <div class="tab-content" :class="{ 'active': activeTab === 'water' }">
                        <h2 style="margin-bottom: 20px;">Objectif d'hydratation</h2>
                        
                        <div class="form-group">
                            <label for="waterTarget">Quantité d'eau quotidienne (ml)</label>
                            <input type="number" id="waterTarget" class="form-control" placeholder="Ex: 2000" x-model="waterTarget" min="500" max="5000" step="100">
                            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                La recommandation générale est de boire environ 2000 ml (2 litres) d'eau par jour.
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: right;">
                            <button class="btn btn-primary" @click="updateWaterGoal()" :disabled="loading">
                                <span x-show="!loading">Enregistrer</span>
                                <span x-show="loading">Chargement...</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Sleep Tab -->
                    <div class="tab-content" :class="{ 'active': activeTab === 'sleep' }">
                        <h2 style="margin-bottom: 20px;">Objectif de sommeil</h2>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sleepHours">Heures</label>
                                <input type="number" id="sleepHours" class="form-control" x-model="sleepHours" min="4" max="12">
                            </div>
                            <div class="form-group">
                                <label for="sleepMinutes">Minutes</label>
                                <input type="number" id="sleepMinutes" class="form-control" x-model="sleepMinutes" min="0" max="59" step="5">
                            </div>
                        </div>
                        
                        <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                            La recommandation générale est de dormir entre 7 et 9 heures par nuit pour un adulte.
                        </div>
                        
                        <div style="margin-top: 30px; text-align: right;">
                            <button class="btn btn-primary" @click="updateSleepGoal()" :disabled="loading">
                                <span x-show="!loading">Enregistrer</span>
                                <span x-show="loading">Chargement...</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Weight Tab -->
                    <div class="tab-content" :class="{ 'active': activeTab === 'weight' }">
                        <h2 style="margin-bottom: 20px;">Objectif de poids</h2>
                        
                        <div class="form-group">
                            <label for="targetWeight">Poids cible (kg)</label>
                            <input type="number" id="targetWeight" class="form-control" placeholder="Ex: 70" x-model="targetWeight" min="20" max="500" step="0.1">
                        </div>
                        
                        <div class="form-group">
                            <label for="targetDate">Date cible</label>
                            <input type="date" id="targetDate" class="form-control" x-model="targetDate" min="{{ now()->addDays(1)->format('Y-m-d') }}">
                            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                Une perte de poids saine est d'environ 0.5 à 1 kg par semaine.
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: right;">
                            <button class="btn btn-primary" @click="updateWeightGoal()" :disabled="loading">
                                <span x-show="!loading">Enregistrer</span>
                                <span x-show="loading">Chargement...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>