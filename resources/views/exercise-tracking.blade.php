<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi d'Exercice</title>
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
        exerciseName: '',
        duration: 30,
        caloriesBurned: 150,
        entryDate: '{{ now()->format('Y-m-d') }}',
        entryTime: '{{ now()->format('H:i') }}',
        notes: '',
        loading: false,
        
        logExercise() {
            this.loading = true;
            axios.post('{{ route('exercise-tracking.store') }}', {
                exercise_name: this.exerciseName,
                duration: this.duration,
                calories_burned: this.caloriesBurned,
                entry_date: this.entryDate,
                entry_time: this.entryTime,
                notes: this.notes,
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
                        <h1 class="page-title">Suivi d'Exercice</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showExerciseModal = true">
                        + Ajouter un exercice
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <!-- Exercise Summary -->
                    <div class="summary-cards-horizontal" style="grid-template-columns: repeat(2, 1fr);">
                        <!-- Today's Calories Burned -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning);">🔥</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Calories brûlées aujourd'hui</div>
                                    <div class="summary-card-value">{{ $totalCaloriesBurned }} kcal</div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Exercices aujourd'hui</div>
                                <div style="font-size: 18px; font-weight: 600;">{{ count($exerciseEntries) }}</div>
                            </div>
                        </div>
                        
                        <!-- Weekly Summary -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);">📊</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Cette semaine</div>
                                    <div class="summary-card-value">
                                        {{ $totalCaloriesBurned * 3 }} kcal
                                    </div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Temps d'exercice</div>
                                <div style="font-size: 18px; font-weight: 600;">
                                    {{ $exerciseEntries->sum('duration') }} min
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Exercise Chart -->
                    <div class="weight-chart-container">
                        <div class="weight-chart-header">
                            <div class="weight-chart-title">
                                <span class="weight-chart-icon">🏋️</span>
                                Calories brûlées (7 derniers jours)
                            </div>
                        </div>
                        <canvas id="exerciseChart" class="weight-chart-canvas" style="height: 300px;"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('exerciseChart').getContext('2d');
                            
                            // Données pour les 7 derniers jours (à remplacer par des données réelles)
                            const dates = [];
                            const caloriesBurned = [];
                            
                            // Générer des dates pour les 7 derniers jours
                            for (let i = 6; i >= 0; i--) {
                                const date = new Date();
                                date.setDate(date.getDate() - i);
                                dates.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }));
                                
                                // Données fictives pour l'exemple
                                const calories = {{ $exerciseEntries->count() > 0 ? 'Math.floor(Math.random() * 300) + 100' : '0' }};
                                caloriesBurned.push(calories);
                            }
                            
                            const exerciseChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        label: 'Calories brûlées (kcal)',
                                        data: caloriesBurned,
                                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                                        borderColor: '#f59e0b',
                                        borderWidth: 1
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
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    
                    <!-- Exercise History -->
                    <div class="card">
                        <div class="card-title">Historique des exercices</div>
                        @if(count($exerciseEntries) > 0)
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--gray-light); text-align: left;">
                                        <th style="padding: 10px;">Date</th>
                                        <th style="padding: 10px;">Heure</th>
                                        <th style="padding: 10px;">Exercice</th>
                                        <th style="padding: 10px;">Durée</th>
                                        <th style="padding: 10px;">Calories</th>
                                        <th style="padding: 10px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exerciseEntries as $entry)
                                        <tr style="border-bottom: 1px solid var(--gray-light);">
                                            <td style="padding: 10px;">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</td>
                                            <td style="padding: 10px;">{{ \Carbon\Carbon::parse($entry->entry_time)->format('H:i') }}</td>
                                            <td style="padding: 10px; font-weight: 600;">{{ $entry->exercise_name }}</td>
                                            <td style="padding: 10px;">{{ $entry->duration }} min</td>
                                            <td style="padding: 10px;">{{ $entry->calories_burned }} kcal</td>
                                            <td style="padding: 10px;">
                                                <button 
                                                    class="btn-icon" 
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet exercice?')) { 
                                                        axios.delete('{{ route('exercise-tracking.destroy', $entry->id) }}')
                                                        .then(() => window.location.reload())
                                                        .catch(err => console.error(err));
                                                    }"
                                                >
                                                    🗑️
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>Aucun historique disponible.</p>
                        @endif
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
                                <label for="entryDate">Date</label>
                                <input type="date" id="entryDate" class="form-control" x-model="entryDate" max="{{ now()->format('Y-m-d') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="entryTime">Heure</label>
                                <input type="time" id="entryTime" class="form-control" x-model="entryTime">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (optionnel)</label>
                            <textarea id="notes" class="form-control" placeholder="Ex: Haute intensité" x-model="notes" rows="2"></textarea>
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
        </div>
    </div>
    
    <style>
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        
        .btn-icon:hover {
            background-color: var(--gray-light);
        }

        .weight-chart-container {
            position: relative;
            height: 300px; /* Fixed height */
            margin-bottom: 20px;
        }

        .weight-chart-canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</body>
</html>