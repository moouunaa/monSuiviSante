<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi d'Exercices</title>
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
        showExerciseModal: false,
        showCustomExerciseModal: false,
        showDeleteConfirmModal: false,
        
        // New properties for exercise selection
        selectedExercise: '',
        selectedExerciseType: '',
        exerciseDuration: 30,
        exerciseSets: '',
        exerciseReps: '',
        
        // Initialize exercises arrays
        exercises: {{ Illuminate\Support\Js::from($exercises) }},
        customExercises: {{ Illuminate\Support\Js::from($customExercises) }},
        
        // Exercise tracking
        exerciseEntryDate: '{{ now()->format('Y-m-d') }}',
        exerciseEntryTime: '{{ now()->format('H:i') }}',
        exerciseNotes: '',
        selectedWorkoutTemplate: '',
        
        // Custom exercise
        customExerciseName: '',
        customExerciseCaloriesPerMinute: '',
        customExerciseCategory: 'cardio',
        
        // Workout items
        workoutItems: [],
        
        // Delete confirmation
        workoutToDelete: null,
        
        // Pagination
        currentPage: 1,
        totalPages: {{ $workouts->lastPage() }},
        
        loading: false,
        
        // Methods for workout building
        addWorkoutItem() {
            if (!this.selectedExercise) {
                alert('Veuillez sélectionner un exercice');
                return;
            }
            
            const duration = parseInt(this.exerciseDuration);
            if (!duration) {
                alert('Veuillez spécifier une durée');
                return;
            }
            
            // Get the exercise name from the select element
            const exerciseSelect = document.getElementById('exerciseSelect');
            const selectedOption = exerciseSelect.options[exerciseSelect.selectedIndex];
            const exerciseName = selectedOption.text;
            
            // Calculate calories based on exercise type and duration
            let caloriesPerMinute = 0;
            if (this.selectedExerciseType === 'exercise') {
                const exercise = this.exercises.find(e => e.id == this.selectedExercise);
                if (exercise) {
                    caloriesPerMinute = exercise.calories_per_minute;
                }
            } else {
                const exercise = this.customExercises.find(e => e.id == this.selectedExercise);
                if (exercise) {
                    caloriesPerMinute = exercise.calories_per_minute;
                }
            }
            
            const caloriesBurned = Math.round(caloriesPerMinute * duration);
            
            this.workoutItems.push({
                exercise_type: this.selectedExerciseType,
                exercise_id: this.selectedExercise,
                exercise_name: exerciseName,
                duration: duration,
                sets: this.exerciseSets ? parseInt(this.exerciseSets) : null,
                reps: this.exerciseReps ? parseInt(this.exerciseReps) : null,
                calories_burned: caloriesBurned
            });
            
            // Reset selection
            this.exerciseDuration = 30;
            this.exerciseSets = '';
            this.exerciseReps = '';
            this.selectedExercise = '';
            this.selectedExerciseType = '';
        },
        
        removeWorkoutItem(index) {
            this.workoutItems.splice(index, 1);
        },
        
        getTotalWorkoutCalories() {
            return this.workoutItems.reduce((total, item) => total + item.calories_burned, 0);
        },
        
        getTotalWorkoutDuration() {
            return this.workoutItems.reduce((total, item) => total + item.duration, 0);
        },
        
        cloneWorkout(workout) {
            this.showExerciseModal = true;
            this.workoutItems = [];
            this.exerciseNotes = workout.notes || '';
            
            // Clone workout items
            workout.workout_items.forEach(item => {
                this.workoutItems.push({
                    exercise_type: item.exercise_type,
                    exercise_id: item.exercise_id,
                    exercise_name: item.exercise.name,
                    duration: item.duration,
                    sets: item.sets,
                    reps: item.reps,
                    calories_burned: item.calories_burned
                });
            });
        },
        
        confirmDelete(workout) {
            this.workoutToDelete = workout;
            this.showDeleteConfirmModal = true;
        },
        
        deleteWorkout() {
            if (!this.workoutToDelete) return;
            
            this.loading = true;
            axios.delete(`/workouts/${this.workoutToDelete.id}`)
                .then(response => {
                    if (response.data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression de l\'entraînement:', error);
                    alert('Une erreur est survenue lors de la suppression de l\'entraînement');
                })
                .finally(() => {
                    this.loading = false;
                    this.showDeleteConfirmModal = false;
                });
        },
        
        // Save methods
        logWorkout() {
            if (this.workoutItems.length === 0) {
                alert('Veuillez ajouter au moins un exercice à votre entraînement');
                return;
            }
            
            this.loading = true;
            axios.post('{{ route('workouts.store') }}', {
                entry_date: this.exerciseEntryDate,
                entry_time: this.exerciseEntryTime,
                notes: this.exerciseNotes,
                workout_items: this.workoutItems,
                total_duration: this.getTotalWorkoutDuration(),
                total_calories_burned: this.getTotalWorkoutCalories(),
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement de l\'entraînement:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'entraînement');
            })
            .finally(() => {
                this.loading = false;
                this.showExerciseModal = false;
            });
        },
        
        saveCustomExercise() {
            this.loading = true;
            axios.post('{{ route('custom-exercises.store') }}', {
                name: this.customExerciseName,
                calories_per_minute: this.customExerciseCaloriesPerMinute,
                category: this.customExerciseCategory,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                if (response.data.success) {
                    // Add the new custom exercise to the list
                    this.customExercises.push(response.data.customExercise);
                    this.showCustomExerciseModal = false;
                    
                    // Reset form
                    this.customExerciseName = '';
                    this.customExerciseCaloriesPerMinute = '';
                    this.customExerciseCategory = 'cardio';
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement de l\'exercice personnalisé:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'exercice personnalisé');
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            window.location.href = `{{ route('exercise-tracking') }}?page=${page}`;
        }
    }">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}"><i>📊</i> Tableau de bord</a></li>
                    <li><a href="{{ route('food-tracking') }}"><i>🍽️</i> Repas</a></li>
                    <li><a href="{{ route('exercise-tracking') }}" class="active"><i>🏋️</i> Exercices</a></li>
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
                        <h1 class="page-title">Suivi d'Exercices</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <div>
                        <button class="btn btn-primary" @click="showExerciseModal = true; workoutItems = [];">
                        + Ajouter un entraînement
                    </button>
                    </div>
                </div>
                
                <div class="dashboard-content">
                    <!-- Exercise Summary -->
                    <div class="card">
                        <div class="card-title">Résumé d'Activité (7 derniers jours)</div>
                        <div class="exercise-summary">
                            <div class="exercise-item">
                                <div class="exercise-value">{{ $weeklyStats['totalWorkouts'] }}</div>
                                <div class="exercise-label">Entraînements</div>
                                </div>
                            <div class="exercise-item">
                                <div class="exercise-value">{{ $weeklyStats['totalDuration'] }} min</div>
                                <div class="exercise-label">Durée totale</div>
                            </div>
                            <div class="exercise-item">
                                <div class="exercise-value">{{ $weeklyStats['avgDurationPerDay'] }} min</div>
                                <div class="exercise-label">Durée/jour</div>
                        </div>
                            <div class="exercise-item">
                                <div class="exercise-value">{{ $weeklyStats['totalCaloriesBurned'] }}</div>
                                <div class="exercise-label">Calories brûlées</div>
                                </div>
                            </div>
                        </div>
                        
                    <!-- Calories Burned Chart -->
                    <div class="card">
                        <div class="card-title">Calories brûlées par jour (7 derniers jours)</div>
                        <div class="chart-container">
                            <canvas id="caloriesBurnedChart"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('caloriesBurnedChart').getContext('2d');
                                
                                const caloriesBurnedChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                        labels: [
                                            @foreach($dailyCaloriesBurned as $date => $calories)
                                                '{{ \Carbon\Carbon::parse($date)->format('d/m') }}',
                                            @endforeach
                                        ],
                                        datasets: [{
                                            label: 'Calories brûlées',
                                            data: [
                                                @foreach($dailyCaloriesBurned as $calories)
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
                    
                    <!-- Exercise Duration Chart -->
                    <div class="card">
                        <div class="card-title">Durée d'exercice par jour (7 derniers jours)</div>
                        <div class="chart-container">
                            <canvas id="durationChart"></canvas>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('durationChart').getContext('2d');
                                
                                const durationChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: [
                                            @foreach($dailyDuration as $date => $duration)
                                                '{{ \Carbon\Carbon::parse($date)->format('d/m') }}',
                                            @endforeach
                                        ],
                                        datasets: [{
                                            label: 'Durée (minutes)',
                                            data: [
                                                @foreach($dailyDuration as $duration)
                                                    {{ $duration }},
                                                @endforeach
                                            ],
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                                                beginAtZero: true,
                                            title: {
                                                display: true,
                                                    text: 'Durée (minutes)'
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
                    
                    <!-- Workout History -->
                    <div class="card">
                        <div class="card-title">Historique des Entraînements</div>
                        
                        @if(count($workouts) > 0)
                            <div class="workout-history">
                                @php
                                    $currentDate = null;
                                @endphp
                                
                                @foreach($workouts as $workout)
                                    @php
                                        $workoutDate = \Carbon\Carbon::parse($workout->entry_date);
                                    @endphp
                                    
                                    @if($currentDate !== $workoutDate->format('Y-m-d'))
                                        @php
                                            $currentDate = $workoutDate->format('Y-m-d');
                                        @endphp
                                        <div class="workout-date-header">
                                            {{ $workoutDate->format('l, d F Y') }}
                                            </div>
                                    @endif
                                    
                                    <div class="workout-item-card">
                                        <div class="workout-item-header">
                                            <div class="workout-item-title">
                                                Entraînement - {{ \Carbon\Carbon::parse($workout->entry_time)->format('H:i') }}
                                        </div>
                                            <div class="workout-item-stats">
                                                <span class="workout-duration">{{ $workout->total_duration }} min</span>
                                                <span class="workout-calories">{{ $workout->total_calories_burned }} kcal</span>
                                        </div>
                                        </div>
                                        
                                        <div class="workout-item-content">
                                            <ul class="workout-exercises-list">
                                                @foreach($workout->workoutItems as $item)
                                                    <li>
                                                        {{ $item->exercise->name }} - {{ $item->duration }} min
                                                    @if($item->sets && $item->reps)
                                                            ({{ $item->sets }} x {{ $item->reps }})
                                                    @endif
                                                        - {{ $item->calories_burned }} kcal
                                                    </li>
                                            @endforeach
                                            </ul>
                                            
                                            @if($workout->notes)
                                                <div class="workout-item-notes">
                                                    <strong>Notes:</strong> {{ $workout->notes }}
                                        </div>
                                            @endif
                                        </div>
                                        
                                        <div class="workout-item-actions">
                                            <button class="btn btn-sm btn-secondary" @click="cloneWorkout({{ json_encode($workout) }})">
                                                Cloner
                                            </button>
                                            <button class="btn btn-sm btn-danger" @click="confirmDelete({{ json_encode($workout) }})">
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Pagination -->
                                @if($workouts->lastPage() > 1)
                                    <div class="pagination">
                                        <button 
                                            class="pagination-btn" 
                                            @click="goToPage(currentPage - 1)" 
                                            :disabled="currentPage === 1"
                                        >
                                            &laquo; Précédent
                                        </button>
                                        
                                        <div class="pagination-info">
                                            Page {{ $workouts->currentPage() }} sur {{ $workouts->lastPage() }}
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
                                <div class="empty-state-icon">🏋️</div>
                                <div class="empty-state-text">Vous n'avez pas encore enregistré d'entraînement.</div>
                                <button class="btn btn-primary" @click="showExerciseModal = true; workoutItems = [];">
                                    + Ajouter votre premier entraînement
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Exercise Logging Modal -->
            <div class="modal-overlay" x-show="showExerciseModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showExerciseModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Composer un entraînement</h3>
                        <button class="modal-close" @click="showExerciseModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Workout details -->
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
                        
                        <hr>
                        
                        <!-- Add exercise items section -->
                        <div class="form-group">
                            <label>Ajouter des exercices</label>
                            <div class="form-row">
                                <div class="form-group" style="flex-grow: 2;">
                                    <select id="exerciseSelect" class="form-control" x-model="selectedExercise" @change="selectedExerciseType = $event.target.options[$event.target.selectedIndex].dataset.type">
                                        <option value="">Sélectionner un exercice</option>
                                        <optgroup label="Exercices standards">
                                            @foreach($exercises as $exercise)
                                                <option value="{{ $exercise->id }}" data-type="exercise">{{ $exercise->name }} ({{ $exercise->calories_per_minute }} kcal/min)</option>
                                            @endforeach
                                        </optgroup>
                                        @if(count($customExercises) > 0)
                                            <optgroup label="Exercices personnalisés">
                                                @foreach($customExercises as $exercise)
                                                    <option value="{{ $exercise->id }}" data-type="custom_exercise">{{ $exercise->name }} ({{ $exercise->calories_per_minute }} kcal/min)</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                        </div>
                                <div class="form-group">
                                    <input type="number" id="exerciseDuration" class="form-control" placeholder="Durée (min)" x-model="exerciseDuration" min="1">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" @click="addWorkoutItem()">Ajouter</button>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 10px;">
                                    <div class="form-group">
                                        <label for="exerciseSets">Séries (optionnel)</label>
                                    <input type="number" id="exerciseSets" class="form-control" placeholder="Ex: 3" x-model="exerciseSets" min="1">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="exerciseReps">Répétitions (optionnel)</label>
                                    <input type="number" id="exerciseReps" class="form-control" placeholder="Ex: 12" x-model="exerciseReps" min="1">
                                    </div>
                                </div>
                                
                            <div style="text-align: right; margin-top: 5px;">
                                <button type="button" class="btn btn-link" @click="showCustomExerciseModal = true">+ Ajouter un exercice personnalisé</button>
                                </div>
                            </div>
                            
                        <!-- Workout items list -->
                        <div class="form-group" x-show="workoutItems.length > 0">
                            <label>Exercices de l'entraînement</label>
                                <div class="workout-items-list">
                                    <template x-for="(item, index) in workoutItems" :key="index">
                                        <div class="workout-item">
                                        <div class="workout-item-info">
                                            <span x-text="item.exercise_name"></span>
                                            <span x-text="item.duration + ' min'"></span>
                                            <span x-text="(item.sets && item.reps) ? item.sets + ' x ' + item.reps : ''"></span>
                                            <span x-text="item.calories_burned + ' kcal'"></span>
                                            </div>
                                        <button type="button" class="workout-item-remove" @click="removeWorkoutItem(index)">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            <div class="workout-total">
                                <span>Total:</span>
                                <span x-text="getTotalWorkoutDuration() + ' min, ' + getTotalWorkoutCalories() + ' kcal'"></span>
                            </div>
                            </div>
                            
                            <div class="form-group">
                            <label for="exerciseNotes">Notes (optionnel)</label>
                            <textarea id="exerciseNotes" class="form-control" placeholder="Ex: Haute intensité" x-model="exerciseNotes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showExerciseModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logWorkout()" :disabled="loading || workoutItems.length === 0">
                            <span x-show="!loading">Enregistrer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Custom Exercise Modal -->
            <div class="modal-overlay" x-show="showCustomExerciseModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showCustomExerciseModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter un exercice personnalisé</h3>
                        <button class="modal-close" @click="showCustomExerciseModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customExerciseName">Nom de l'exercice</label>
                            <input type="text" id="customExerciseName" class="form-control" placeholder="Ex: Mon exercice" x-model="customExerciseName">
                        </div>
                        
                        <div class="form-row">
                        <div class="form-group">
                                <label for="customExerciseCaloriesPerMinute">Calories par minute</label>
                                <input type="number" id="customExerciseCaloriesPerMinute" class="form-control" placeholder="Ex: 8" x-model="customExerciseCaloriesPerMinute" min="1">
                        </div>
                        
                        <div class="form-group">
                            <label for="customExerciseCategory">Catégorie</label>
                            <select id="customExerciseCategory" class="form-control" x-model="customExerciseCategory">
                                <option value="cardio">Cardio</option>
                                <option value="strength">Musculation</option>
                                <option value="flexibility">Flexibilité</option>
                                <option value="other">Autre</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showCustomExerciseModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="saveCustomExercise()" :disabled="loading || !customExerciseName || !customExerciseCaloriesPerMinute">
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
                        <p>Êtes-vous sûr de vouloir supprimer cet entraînement ? Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showDeleteConfirmModal = false">Annuler</button>
                        <button class="btn btn-danger" @click="deleteWorkout()" :disabled="loading">
                            <span x-show="!loading">Supprimer</span>
                            <span x-show="loading">Chargement...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Exercise Summary */
        .exercise-summary {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .exercise-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .exercise-value {
            font-size: 24px;
            font-weight: bold;
            color: #4034e4;
            margin-bottom: 5px;
        }
        
        .exercise-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        /* Chart Container */
        .chart-container {
            height: 300px;
            margin-top: 15px;
        }
        
        /* Workout History */
        .workout-history {
            margin-top: 15px;
        }
        
        .workout-date-header {
            font-weight: bold;
            padding: 10px 0;
            margin-top: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .workout-item-card {
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .workout-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .workout-item-title {
            font-weight: bold;
            font-size: 16px;
        }
        
        .workout-item-stats {
            display: flex;
            gap: 15px;
        }
        
        .workout-duration {
            color: #10b981;
            font-weight: bold;
        }
        
        .workout-calories {
            color: #4034e4;
            font-weight: bold;
        }
        
        .workout-exercises-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 10px;
        }
        
        .workout-exercises-list li {
            padding: 5px 0;
            border-bottom: 1px dashed #e9ecef;
        }
        
        .workout-exercises-list li:last-child {
            border-bottom: none;
        }
        
        .workout-item-notes {
            font-style: italic;
            margin-top: 10px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .workout-item-actions {
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
