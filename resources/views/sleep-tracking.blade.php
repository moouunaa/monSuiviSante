<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Suivi de Sommeil</title>
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
        showSleepModal: false,
        sleepDate: '{{ now()->subDay()->format('Y-m-d') }}',
        sleepTime: '22:30',
        wakeTime: '07:00',
        quality: 3,
        notes: '',
        loading: false,
        
        logSleep() {
            this.loading = true;
            axios.post('{{ route('sleep-tracking.store') }}', {
                sleep_date: this.sleepDate,
                sleep_time: this.sleepTime,
                wake_time: this.wakeTime,
                quality: this.quality,
                notes: this.notes,
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
                    <li><a href="{{ route('dashboard') }}"><i>📊</i> Tableau de bord</a></li>
                    <li><a href="{{ route('food-tracking') }}"><i>🍽️</i> Repas</a></li>
                    <li><a href="{{ route('exercise-tracking') }}"><i>🏋️</i> Exercices</a></li>
                    <li><a href="{{ route('water-tracking') }}"><i>💧</i> Hydratation</a></li>
                    <li><a href="{{ route('sleep-tracking') }}" class="active"><i>😴</i> Sommeil</a></li>
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
                        <h1 class="page-title">Suivi de Sommeil</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showSleepModal = true">
                        + Enregistrer le sommeil
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <!-- Sleep Summary -->
                    <div class="summary-cards-horizontal" style="grid-template-columns: repeat(3, 1fr);">
                        <!-- Last Night -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">😴</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Dernière nuit</div>
                                    <div class="summary-card-value">
                                        @if($lastNight)
                                            {{ floor($lastNight->duration / 60) }}h {{ $lastNight->duration % 60 }}min
                                        @else
                                            Non enregistré
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($lastNight)
                                <div class="sleep-quality">
                                    <div class="sleep-quality-label">Qualité:</div>
                                    <div class="sleep-quality-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $lastNight->quality ? 'star-filled' : 'star-empty' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Average -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--success);">📊</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Moyenne (7 jours)</div>
                                    <div class="summary-card-value">
                                        @if(count($sleepEntries) > 0)
                                            {{ floor($sleepEntries->avg('duration') / 60) }}h {{ round($sleepEntries->avg('duration') % 60) }}min
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="summary-card-text" style="text-align: right;">
                                <div class="summary-card-label">Objectif</div>
                                <div style="font-size: 18px; font-weight: 600;">
                                    {{ floor(($sleepGoal->target_value ?? 480) / 60) }}h {{ ($sleepGoal->target_value ?? 480) % 60 }}min
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quality -->
                        <div class="summary-card">
                            <div class="summary-card-content">
                                <div class="summary-card-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning);">⭐</div>
                                <div class="summary-card-text">
                                    <div class="summary-card-label">Qualité moyenne</div>
                                    <div class="summary-card-value">
                                        @if(count($sleepEntries) > 0)
                                            {{ number_format($sleepEntries->avg('quality'), 1) }}/5
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="sleep-quality">
                                <div class="sleep-quality-stars">
                                    @php
                                        $avgQuality = count($sleepEntries) > 0 ? $sleepEntries->avg('quality') : 0;
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= round($avgQuality) ? 'star-filled' : 'star-empty' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sleep Chart -->
                    <div class="weight-chart-container">
                        <div class="weight-chart-header">
                            <div class="weight-chart-title">
                                <span class="weight-chart-icon">😴</span>
                                Durée de sommeil (7 derniers jours)
                            </div>
                        </div>
                        <canvas id="sleepChart" class="weight-chart-canvas" style="height: 300px;"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('sleepChart').getContext('2d');
                            
                            // Données pour les 7 derniers jours (à remplacer par des données réelles)
                            const dates = [];
                            const sleepDurations = [];
                            const targetLine = [];
                            
                            // Générer des dates pour les 7 derniers jours
                            for (let i = 6; i >= 0; i--) {
                                const date = new Date();
                                date.setDate(date.getDate() - i);
                                dates.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }));
                                
                                // Données fictives pour l'exemple
                                const duration = {{ $sleepEntries->count() > 0 ? 'Math.floor(Math.random() * 120) + 360' : '0' }};
                                sleepDurations.push(duration / 60); // Convertir en heures
                                targetLine.push({{ ($sleepGoal->target_value ?? 480) / 60 }});
                            }
                            
                            const sleepChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: dates,
                                    datasets: [
                                        {
                                            label: 'Durée de sommeil (heures)',
                                            data: sleepDurations,
                                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                                            borderColor: '#8b5cf6',
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
                                                text: 'Durée (heures)'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    
                    <!-- Sleep History -->
                    <div class="card">
                        <div class="card-title">Historique de sommeil</div>
                        @if(count($sleepEntries) > 0)
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--gray-light); text-align: left;">
                                        <th style="padding: 10px;">Date</th>
                                        <th style="padding: 10px;">Coucher</th>
                                        <th style="padding: 10px;">Réveil</th>
                                        <th style="padding: 10px;">Durée</th>
                                        <th style="padding: 10px;">Qualité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sleepEntries as $entry)
                                        <tr style="border-bottom: 1px solid var(--gray-light);">
                                            <td style="padding: 10px;">{{ \Carbon\Carbon::parse($entry->sleep_date)->format('d/m/Y') }}</td>
                                            <td style="padding: 10px;">{{ $entry->sleep_time }}</td>
                                            <td style="padding: 10px;">{{ $entry->wake_time }}</td>
                                            <td style="padding: 10px; font-weight: 600;">
                                                {{ floor($entry->duration / 60) }}h {{ $entry->duration % 60 }}min
                                            </td>
                                            <td style="padding: 10px;">
                                                <div class="sleep-quality-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= $entry->quality ? 'star-filled' : 'star-empty' }}">★</span>
                                                    @endfor
                                                </div>
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
                            <label for="notes">Notes (optionnel)</label>
                            <textarea id="notes" class="form-control" placeholder="Ex: Réveillé plusieurs fois" x-model="notes" rows="2"></textarea>
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
        .sleep-quality {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        
        .sleep-quality-label {
            margin-right: 10px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .sleep-quality-stars {
            display: flex;
        }
        
        .star-filled {
            color: #f59e0b;
        }
        
        .star-empty {
            color: var(--gray-light);
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