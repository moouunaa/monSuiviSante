<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi d'Hydratation</title>
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
        showWaterModal: false,
        amount: 250,
        entryDate: '{{ now()->format('Y-m-d') }}',
        entryTime: '{{ now()->format('H:i') }}',
        notes: '',
        loading: false,
        
        logWater() {
            this.loading = true;
            axios.post('{{ route('water-tracking.store') }}', {
                amount: this.amount,
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
                console.error('Erreur lors de l\'enregistrement de l\'eau:', error);
                alert('Une erreur est survenue lors de l\'enregistrement de l\'eau');
            })
            .finally(() => {
                this.loading = false;
                this.showWaterModal = false;
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
                    <li><a href="{{ route('water-tracking') }}" class="active"><i>💧</i> Hydratation</a></li>
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
                        <h1 class="page-title">Suivi d'Hydratation</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showWaterModal = true">
                        + Ajouter de l'eau
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <!-- Water Summary -->
                    <div class="summary-cards-horizontal" style="grid-template-columns: repeat(2, 1fr);">
                        <!-- Today's Water -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">💧</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Eau consommée aujourd'hui</div>
                                    <div class="summary-card-value">{{ $totalWaterConsumed }} ml</div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Objectif quotidien</div>
                                <div style="font-size: 18px; font-weight: 600;">{{ $waterGoal->target_value ?? 2000 }} ml</div>
                            </div>
                        </div>
                        
                        <!-- Progress -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);">📊</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Progression</div>
                                    <div class="summary-card-value">
                                        {{ $waterGoal && $waterGoal->target_value > 0 ? round(($totalWaterConsumed / $waterGoal->target_value) * 100) : 0 }}%
                                    </div>
                                </div>
                            </div>
                            <div class="progress-bar" style="margin-top: 10px;">
                                <div class="progress-bar-fill" style="width: {{ $waterGoal && $waterGoal->target_value > 0 ? min(100, ($totalWaterConsumed / $waterGoal->target_value) * 100) : 0 }}%; background-color: #3b82f6;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Water Chart -->
                    <div class="weight-chart-container">
                        <div class="weight-chart-header">
                            <div class="weight-chart-title">
                                <span class="weight-chart-icon">💧</span>
                                Consommation d'eau (7 derniers jours)
                            </div>
                        </div>
                        <canvas id="waterChart" class="weight-chart-canvas" style="height: 300px;"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('waterChart').getContext('2d');
                            
                            // Données pour les 7 derniers jours (à remplacer par des données réelles)
                            const dates = [];
                            const waterAmounts = [];
                            const targetLine = [];
                            
                            // Générer des dates pour les 7 derniers jours
                            for (let i = 6; i >= 0; i--) {
                                const date = new Date();
                                date.setDate(date.getDate() - i);
                                dates.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }));
                                
                                // Données fictives pour l'exemple
                                const amount = {{ $waterEntries->count() > 0 ? 'Math.floor(Math.random() * 1000) + 500' : '0' }};
                                waterAmounts.push(amount);
                                targetLine.push({{ $waterGoal->target_value ?? 2000 }});
                            }
                            
                            const waterChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: dates,
                                    datasets: [
                                        {
                                            label: 'Eau consommée (ml)',
                                            data: waterAmounts,
                                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                            borderColor: '#3b82f6',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Objectif',
                                            data: targetLine,
                                            type: 'line',
                                            borderColor: '#ef4444',
                                            borderWidth: 2,
                                            borderDash: [5, 5],
                                            fill: false,
                                            pointRadius: 0
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Quantité (ml)'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    
                    <!-- Water History -->
                    <div class="card">
                        <div class="card-title">Historique d'hydratation</div>
                        @if(count($waterEntries) > 0)
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--gray-light); text-align: left;">
                                        <th style="padding: 10px;">Date</th>
                                        <th style="padding: 10px;">Heure</th>
                                        <th style="padding: 10px;">Quantité (ml)</th>
                                        <th style="padding: 10px;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($waterEntries as $entry)
                                        <tr style="border-bottom: 1px solid var(--gray-light);">
                                            <td style="padding: 10px;">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</td>
                                            <td style="padding: 10px;">{{ \Carbon\Carbon::parse($entry->entry_time)->format('H:i') }}</td>
                                            <td style="padding: 10px; font-weight: 600;">{{ $entry->amount }} ml</td>
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
            
            <!-- Water Logging Modal -->
            <div class="modal-overlay" x-show="showWaterModal" x-transition style="display: none;">
                <div class="modal" @click.outside="showWaterModal = false">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter de l'eau</h3>
                        <button class="modal-close" @click="showWaterModal = false">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">Quantité (ml)</label>
                            <div class="water-amount-selector">
                                <button class="water-amount-btn" @click="amount = 100">100ml</button>
                                <button class="water-amount-btn" @click="amount = 250">250ml</button>
                                <button class="water-amount-btn" @click="amount = 500">500ml</button>
                                <button class="water-amount-btn" @click="amount = 750">750ml</button>
                            </div>
                            <input type="number" id="amount" class="form-control" placeholder="Ex: 250" x-model="amount" min="1" max="2000">
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
                            <textarea id="notes" class="form-control" placeholder="Ex: Après le sport" x-model="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showWaterModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="logWater()" :disabled="loading || !amount">
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

        .weight-chart-container {
            position: relative;
            height: 300px; /* Fixed height */
            margin-bottom: 20px;
        }

        .weight-chart-canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
            box-sizing: border-box;
        }
    </style>
</body>
</html>