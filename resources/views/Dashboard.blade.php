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
    <!-- Styles -->
    <style>
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
        
        /* Saved Meals */
        .saved-meals {
            margin-top: 20px;
        }
        
        .saved-meals-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .meal-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .meal-item {
            background-color: var(--light);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .meal-item:hover {
            background-color: #e5e7eb;
        }
        
        .meal-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .meal-calories {
            color: var(--gray);
            font-size: 14px;
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
        savedMeals: [
            { id: 1, name: 'Petit-déjeuner habituel', type: 'breakfast', calories: 450, items: ['Avoine', 'Banane', 'Lait'] },
            { id: 2, name: 'Salade poulet', type: 'lunch', calories: 550, items: ['Poulet grillé', 'Salade verte', 'Tomates'] },
            { id: 3, name: 'Dîner léger', type: 'dinner', calories: 400, items: ['Soupe', 'Pain complet'] }
        ],
        cloneMeal(meal) {
            this.foodName = meal.name;
            this.calories = meal.calories;
            this.portionSize = '1 portion';
            this.mealType = meal.type;
        }
    }">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-logo">MonSuiviSanté</div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('dashboard') }}" class="active"><i>📊</i> Tableau de bord</a></li>
                    <li><button @click="showFoodModal = true"><i>🍽️</i> Ajouter un repas</button></li>
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
                        <h1 class="page-title">Tableau de bord</h1>
                        <div class="date">{{ now()->format('l, d F Y') }}</div>
                    </div>
                    <button class="btn btn-primary" @click="showFoodModal = true">
                        Ajouter un repas
                    </button>
                </div>
                
                <div class="dashboard-content">
                    <div class="welcome-message">
                        Bienvenue, {{ $user->name }} ! Voici votre suivi alimentaire.
                    </div>
                    
                    <div class="card">
                        <div class="card-title">Repas d'aujourd'hui</div>
                        <p>Vous n'avez pas encore enregistré de repas aujourd'hui.</p>
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
                        <form id="foodForm">
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
                                <input type="text" id="foodName" class="form-control" placeholder="Ex: Poulet grillé" x-model="foodName">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="calories">Calories (kcal)</label>
                                    <input type="number" id="calories" class="form-control" placeholder="Ex: 350" x-model="calories">
                                </div>
                                <div class="form-group">
                                    <label for="portionSize">Taille de la portion</label>
                                    <input type="text" id="portionSize" class="form-control" placeholder="Ex: 100g" x-model="portionSize">
                                </div>
                            </div>
                        </form>
                        
                        <div class="saved-meals">
                            <h4 class="saved-meals-title">Repas enregistrés</h4>
                            <div class="meal-list">
                                <template x-for="meal in savedMeals" :key="meal.id">
                                    <div class="meal-item" @click="cloneMeal(meal)">
                                        <div class="meal-name" x-text="meal.name"></div>
                                        <div class="meal-calories" x-text="meal.calories + ' kcal'"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="showFoodModal = false">Annuler</button>
                        <button class="btn btn-primary" @click="showFoodModal = false">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>