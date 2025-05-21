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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div x-data="{ 
        showWeightModal: false,
        showFoodModal: false,
        showExerciseModal: false,
        showWaterModal: false,
        showSleepModal: false,
        
        // Weight tracking
        weight: '',
        weightEntryDate: '{{ now()->format('Y-m-d') }}',
        weightNotes: '',
        
        // Food tracking
        foodName: '',
        calories: '',
        portionSize: '',
        mealType: 'breakfast',
        foodEntryDate: '{{ now()->format('Y-m-d') }}',
        foodEntryTime: '{{ now()->format('H:i') }}',
        foodNotes: '',
        
        // Exercise tracking
        exerciseName: '',
        duration: 30,
        caloriesBurned: 150,
        exerciseEntryDate: '{{ now()->format('Y-m-d') }}',
        exerciseEntryTime: '{{ now()->format('H:i') }}',
        exerciseNotes: '',
        
        // Water tracking
        waterAmount: 250,
        waterEntryDate: '{{ now()->format('Y-m-d') }}',
        waterEntryTime: '{{ now()->format('H:i') }}',
        waterNotes: '',
        
        // Sleep tracking
        sleepDate: '{{ now()->subDay()->format('Y-m-d') }}',
        sleepTime: '22:30',
        wakeTime: '07:00',
        quality: 3,
        sleepNotes: '',
        
        loading: false,
        
        logWeight() {
            this.loading = true;
            axios.post('{{ route('weight-tracking.store') }}', {
                weight: this.weight,
                entry_date: this.weightEntryDate,
                notes: this.weightNotes,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement du poids:', error);
                alert('Une erreur est survenue lors de l\'enregistrement du poids');
            })
            .finally(() => {
                this.loading = false;
                this.showWeightModal = false;
            });
        },
        
        logFood() {
            this.loading = true;
            axios.post('{{ route('food-tracking.store') }}', {
                food_name: this.foodName,
                calories: this.calories,
                portion_size: this.portionSize,
                meal_type: this.mealType,
                entry_date: this.foodEntryDate,
                entry_time: this.foodEntryTime,
                notes: this.foodNotes,
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
        
        logExercise() {
            this.loading = true;
            axios.post('{{ route('exercise-tracking.store') }}', {
                exercise_name: this.exerciseName,
                duration: this.duration,
                calories_burned: this.caloriesBurned,
                entry_date: this.exerciseEntryDate,
                entry_time: this.exerciseEntryTime,
                notes: this.exerciseNotes,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement de l\'exercice:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'exercice');
            })
            .finally(() => {
                this.loading = false;
                this.showExerciseModal = false;
            });
        },
        
        logWater() {
            this.loading = true;
            axios.post('{{ route('water-tracking.store') }}', {
                amount: this.waterAmount,
                entry_date: this.waterEntryDate,
                entry_time: this.waterEntryTime,
                notes: this.waterNotes,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement de l\'eau:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'eau');
            })
            .finally(() => {
                this.loading = false;
                this.showWaterModal = false;
            });
        },
        
        logSleep() {
            this.loading = true;
            axios.post('{{ route('sleep-tracking.store') }}', {
                sleep_date: this.sleepDate,
                sleep_time: this.sleepTime,
                wake_time: this.wakeTime,
                quality: this.quality,
                notes: this.sleepNotes,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement du sommeil:', error);
                alert('Une erreur est survenue lors de l\'enregistrement du sommeil');
            })
            .finally(() => {
                this.loading = false;
                this.showSleepModal = false;
            });
        }
    }">
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
                        <h1 class="page-title">Tableau de bord</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-primary" @click="showWeightModal = true">
                            Enregistrer le poids
                        </button>
                        <a href="{{ route('goals.index') }}" class="btn btn-primary">
                            Définir des objectifs
                        </a>
                    </div>
                </div>
                
                <div class="dashboard-content">
                    <div class="welcome-message">
                        Bienvenue, {{ $user->name }} ! Voici votre suivi quotidien.
                    </div>
                    
                    <!-- Summary Cards - Horizontally Arranged -->
                    <div class="summary-cards-horizontal">
                        <!-- Calories Consumed -->
                        <div class="summary-card calories-consumed">
                            <div class="summary-card-content">
                                <div class="summary-card-icon">🍽️</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Calories consommées</div>
                                    <div class="summary-card-value">{{ $caloriesConsumed }} kcal</div>
                                </div>
                            </div>
                            <button class="summary-card-action" @click="showFoodModal = true">
                                + Ajouter un repas
                            </button>
                        </div>
                        
                        <!-- Calories Burned -->
                        <div class="summary-card calories-burned">
                            <div class="summary-card-content">
                                <div class="summary-card-icon">🏋️</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Calories brûlées</div>
                                    <div class="summary-card-value">{{ $caloriesBurned }} kcal</div>
                                </div>
                            </div>
                            <button class="summary-card-action" @click="showExerciseModal = true">
                                + Ajouter un exercice
                            </button>
                        </div>
                        
                        <!-- Calories Remaining -->
                        <div class="summary-card calories-remaining">
                            <div class="summary-card-content">
                                <div class="summary-card-icon">🔥</div>
                                <div class="summary-card-text">                                
                                    <div class="summary-card-label">Calories restantes</div>
                                    <div class="summary-card-value">{{ $caloriesRemaining }} kcal</div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Objectif</div>
                                <div class="summary-card-value" style="font-size: 18px;">{{ $calorieGoal->target_value ?? 2000 }} kcal</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bars - Horizontally Arranged -->
                    <div class="progress-bars-horizontal">
                        <!-- Water Intake -->
                        <div class="progress-bar-container water-bar">
                            <div class="progress-bar-header">
                                <div class="progress-bar-title">
                                    <span class="progress-bar-icon">💧</span>
                                    Hydratation
                                </div>
                                <div class="progress-bar-value">
                                    {{ $waterConsumed ?? 0 }} / {{ $waterGoal->target_value ?? 2000 }} ml
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $waterGoal && $waterGoal->target_value > 0 ? min(100, (($waterConsumed ?? 0) / $waterGoal->target_value) * 100) : 0 }}%;"></div>
                            </div>
                            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                                <button class="summary-card-action" @click="showWaterModal = true">
                                    + Ajouter de l'eau
                                </button>
                            </div>
                        </div>
                        
                        <!-- Sleep Time -->
                        <div class="progress-bar-container sleep-bar">
                            <div class="progress-bar-header">
                                <div class="progress-bar-title">
                                    <span class="progress-bar-icon">😴</span>
                                    Sommeil
                                </div>
                                <div class="progress-bar-value">
                                    {{ floor(($sleepDuration ?? 0) / 60) }}h {{ ($sleepDuration ?? 0) % 60 }}min / {{ floor(($sleepGoal->target_value ?? 480) / 60) }}h {{ ($sleepGoal->target_value ?? 480) % 60 }}min
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $sleepGoal && $sleepGoal->target_value > 0 ? min(100, (($sleepDuration ?? 0) / $sleepGoal->target_value) * 100) : 0 }}%;"></div>
                            </div>
                            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                                <button class="summary-card-action" @click="showSleepModal = true">
                                    + Enregistrer le sommeil
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Weight Chart -->
                    @if(count($weightEntries) > 0)
                    <div class="weight-chart-container">
                        <div class="weight-chart-header">
                            <div class="weight-chart-title">
                                <span class="weight-chart-icon">⚖️</span>
                                Évolution du poids
                            </div>
                            <a href="{{ route('weight-tracking') }}" class="summary-card-action">
                                Voir l'historique
                            </a>
                        </div>
                        <canvas id="weightChart" class="weight-chart-canvas"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('weightChart').getContext('2d');
                            
                            const weightChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: [
                                        @foreach($weightEntries as $entry)
                                            '{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m') }}',
                                        @endforeach
                                    ],
                                    datasets: [{
                                        label: 'Poids (kg)',
                                        data: [
                                            @foreach($weightEntries as $entry)
                                                {{ $entry->weight }},
                                            @endforeach
                                        ],
                                        borderColor: '#4034e4',
                                        backgroundColor: 'rgba(64, 52, 228, 0.1)',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: false,
                                            ticks: {
                                                precision: 1
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
                    @endif
                    
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
                                <button class="btn btn-primary" @click="showFoodModal = true">Ajouter un repas</button>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Weight Logging Modal -->
            <div class="modal-overlay" x-show="showWeightModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showWeightModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Enregistrer votre poids</h3>
                        <button class="modal-close" @click="showWeightModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="weight">Poids (kg)</label>
                            <input type="number" id="weight" class="form-control" placeholder="Ex: 70.5" x-model="weight" step="0.1" min="20" max="500">
                        </div>
                        
                        <div class="form-group">
                            <label for="weightEntryDate">Date</label>
                            <input type="date" id="weightEntryDate" class="form-control" x-model="weightEntryDate" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="weightNotes">Notes (optionnel)</label>
                            <textarea id="weightNotes" class="form-control" placeholder="Ex: Après le sport" x-model="weightNotes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showWeightModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logWeight()" :disabled="loading || !weight">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Food Logging Modal -->
            <div class="modal-overlay" x-show="showFoodModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showFoodModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter un repas</h3>
                        <button class="modal-close" @click="showFoodModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="foodName">Nom de l'aliment</label>
                            <input type="text" id="foodName" class="form-control" placeholder="Ex: Poulet grillé" x-model="foodName">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="calories">Calories</label>
                                <input type="number" id="calories" class="form-control" placeholder="Ex: 350" x-model="calories" min="1" max="5000">
                            </div>
                            
                            <div class="form-group">
                                <label for="portionSize">Portion</label>
                                <input type="text" id="portionSize" class="form-control" placeholder="Ex: 200g" x-model="portionSize">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="mealType">Type de repas</label>
                            <select id="mealType" class="form-control" x-model="mealType">
                                <option value="breakfast">Petit-déjeuner</option>
                                <option value="lunch">Déjeuner</option>
                                <option value="dinner">Dîner</option>
                                <option value="snack">Collation</option>
                            </select>
                        </div>
                        
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
                        
                        <div class="form-group">
                            <label for="foodNotes">Notes (optionnel)</label>
                            <textarea id="foodNotes" class="form-control" placeholder="Ex: Fait maison" x-model="foodNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showFoodModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logFood()" :disabled="loading || !foodName || !calories">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Exercise Logging Modal -->
            <div class="modal-overlay" x-show="showExerciseModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showExerciseModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter un exercice</h3>
                        <button class="modal-close" @click="showExerciseModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exerciseName">Nom de l'exercice</label>
                            <input type="text" id="exerciseName" class="form-control" placeholder="Ex: Course à pied" x-model="exerciseName">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="duration">Durée (minutes)</label>
                                <input type="number" id="duration" class="form-control" x-model="duration" min="1" max="300">
                            </div>
                            
                            <div class="form-group">
                                <label for="caloriesBurned">Calories brûlées</label>
                                <input type="number" id="caloriesBurned" class="form-control" x-model="caloriesBurned" min="1" max="2000">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="exerciseEntryDate">Date</label>
                                <input type="date" id="exerciseEntryDate" class="form-control" x-model="exerciseEntryDate" max="{{ now()->format('Y-m-d') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="exerciseEntryTime">Heure</label>
                                <input type="time" id="exerciseEntryTime" class="form-control" x-model="exerciseEntryTime">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="exerciseNotes">Notes (optionnel)</label>
                            <textarea id="exerciseNotes" class="form-control" placeholder="Ex: Haute intensité" x-model="exerciseNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showExerciseModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logExercise()" :disabled="loading || !exerciseName">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Water Logging Modal -->
            <div class="modal-overlay" x-show="showWaterModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showWaterModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter de l'eau</h3>
                        <button class="modal-close" @click="showWaterModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="waterAmount">Quantité (ml)</label>
                            <div class="water-amount-selector">
                                <button class="water-amount-btn" @click="waterAmount = 100">100ml</button>
                                <button class="water-amount-btn" @click="waterAmount = 250">250ml</button>
                                <button class="water-amount-btn" @click="waterAmount = 500">500ml</button>
                                <button class="water-amount-btn" @click="waterAmount = 750">750ml</button>
                            </div>
                            <input type="number" id="waterAmount" class="form-control" placeholder="Ex: 250" x-model="waterAmount" min="1" max="2000">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="waterEntryDate">Date</label>
                                <input type="date" id="waterEntryDate" class="form-control" x-model="waterEntryDate" max="{{ now()->format('Y-m-d') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="waterEntryTime">Heure</label>
                                <input type="time" id="waterEntryTime" class="form-control" x-model="waterEntryTime">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="waterNotes">Notes (optionnel)</label>
                            <textarea id="waterNotes" class="form-control" placeholder="Ex: Après le sport" x-model="waterNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showWaterModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logWater()" :disabled="loading || !waterAmount">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sleep Logging Modal -->
            <div class="modal-overlay" x-show="showSleepModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showSleepModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Enregistrer le sommeil</h3>
                        <button class="modal-close" @click="showSleepModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sleepDate">Date de sommeil</label>
                            <input type="date" id="sleepDate" class="form-control" x-model="sleepDate" max="{{ now()->format('Y-m-d') }}">
                            <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                La date correspond au jour où vous vous êtes couché.
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sleepTime">Heure de coucher</label>
                                <input type="time" id="sleepTime" class="form-control" x-model="sleepTime">
                            </div>
                            
                            <div class="form-group">
                                <label for="wakeTime">Heure de réveil</label>
                                <input type="time" id="wakeTime" class="form-control" x-model="wakeTime">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="quality">Qualité du sommeil</label>
                            <div class="sleep-quality-input">
                                <template x-for="i in 5">
                                    <span 
                                        class="sleep-quality-star" 
                                        :class="i <= quality ? 'active' : ''"
                                        @click="quality = i"
                                    >★</span>
                                </template>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="sleepNotes">Notes (optionnel)</label>
                            <textarea id="sleepNotes" class="form-control" placeholder="Ex: Réveillé plusieurs fois" x-model="sleepNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showSleepModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logSleep()" :disabled="loading">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .water-amount-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .water-amount-btn {
            flex: 1;
            padding: 8px;
            border: 1px solid var(--gray-light);
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .water-amount-btn:hover {
            background-color: var(--light);
        }
        
        .sleep-quality-input {
            display: flex;
            gap: 5px;
            font-size: 24px;
            margin-top: 5px;
        }
        
        .sleep-quality-star {
            cursor: pointer;
            color: var(--gray-light);
            transition: color 0.2s ease;
        }
        
        .sleep-quality-star:hover,
        .sleep-quality-star.active {
            color: #f59e0b;
        }

        .weight-chart-container {
            position: relative;
            height: 300px; /* Fixed height for the container */
        }

        .weight-chart-canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</body>
</html>