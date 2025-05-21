<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi de Poids</title>
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
        weight: '',
        entryDate: '{{ now()->format('Y-m-d') }}',
        notes: '',
        loading: false,
        
        logWeight() {
            this.loading = true;
            axios.post('{{ route('weight-tracking.store') }}', {
                weight: this.weight,
                entry_date: this.entryDate,
                notes: this.notes,
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
                    <li><a href="{{ route('weight-tracking') }}" class="active"><i>⚖️</i> Poids</a></li>
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
                        <h1 class="page-title">Suivi de Poids</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showWeightModal = true">
                        Enregistrer le poids
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <!-- Weight Summary -->
                    <div class="summary-cards-horizontal" style="grid-template-columns: repeat(2, 1fr);">
                        <!-- Current Weight -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(79, 70, 229, 0.1); color: var(--primary);">⚖️</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Poids actuel</div>
                                    <div class="summary-card-value">{{ $latestWeight->weight ?? 'N/A' }} kg</div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Dernière mise à jour</div>
                                <div style="font-size: 14px; color: var(--gray);">
                                    {{ $latestWeight ? $latestWeight->entry_date->format('d/m/Y') : 'Jamais' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Weight Goal -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);">🎯</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Objectif de poids</div>
                                    <div class="summary-card-value">{{ $weightGoal->target_value ?? 'Non défini' }} kg</div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Date cible</div>
                                <div style="font-size: 14px; color: var(--gray);">
                                    {{ $weightGoal && $weightGoal->target_date ? \Carbon\Carbon::parse($weightGoal->target_date)->format('d/m/Y') : 'Non définie' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Weight Chart -->
                    @if(count($chartEntries) > 0)
                    <div class="weight-chart-container">
                        <div class="weight-chart-header">
                            <div class="weight-chart-title">
                                <span class="weight-chart-icon">📈</span>
                                Évolution du poids (30 derniers jours)
                            </div>
                        </div>
                        <canvas id="weightChart" class="weight-chart-canvas" style="height: 300px;"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('weightChart').getContext('2d');
                            
                            const weightChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: [
                                        @foreach($chartEntries as $entry)
                                            '{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}',
                                        @endforeach
                                    ],
                                    datasets: [{
                                        label: 'Poids (kg)',
                                        data: [
                                            @foreach($chartEntries as $entry)
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
                                    }
                                }
                            });
                        });
                    </script>
                    @else
                    <div class="card">
                        <div class="card-title">Aucune donnée de poids</div>
                        <p>Vous n'avez pas encore enregistré de poids. Cliquez sur le bouton "Enregistrer le poids" pour commencer à suivre votre évolution.</p>
                    </div>
                    @endif
                    
                    <!-- Weight History -->
                    <div class="card">
                        <div class="card-title">Historique des poids</div>
                        @if(count($weightEntries) > 0)
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--gray-light); text-align: left;">
                                        <th style="padding: 10px;">Date</th>
                                        <th style="padding: 10px;">Poids (kg)</th>
                                        <th style="padding: 10px;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weightEntries as $entry)
                                        <tr style="border-bottom: 1px solid var(--gray-light);">
                                            <td style="padding: 10px;">{{ $entry->entry_date->format('d/m/Y') }}</td>
                                            <td style="padding: 10px; font-weight: 600;">{{ $entry->weight }} kg</td>
                                            <td style="padding: 10px;">{{ $entry->notes ?? '-' }}</td>
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
                            <label for="entryDate">Date</label>
                            <input type="date" id="entryDate" class="form-control" x-model="entryDate" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (optionnel)</label>
                            <textarea id="notes" class="form-control" placeholder="Ex: Après le sport" x-model="notes" rows="3"></textarea>
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
        </div>
    </div>
    <style>
        /* Fix for chart container */
        .weight-chart-container {
            position: relative;
            height: 300px; /* Fixed height */
            width: 100%;
            margin-bottom: 20px;
        }
        
        .weight-chart-canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
    </style>
</body>
</html>