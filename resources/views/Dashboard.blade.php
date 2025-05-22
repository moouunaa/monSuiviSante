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
        showCustomFoodModal: false,
        showCustomExerciseModal: false,
        
        // New properties for food selection
        selectedFood: '',
        selectedFoodType: '',
        foodQuantity: 100,
        foodUnit: 'g',
        
        // New properties for exercise selection
        selectedExercise: '',
        selectedExerciseType: '',
        exerciseDuration: 30,
        exerciseSets: '',
        exerciseReps: '',
        
        // Initialize foods arrays
        foods: {{ Illuminate\Support\Js::from($foods) }},
        customFoods: {{ Illuminate\Support\Js::from($customFoods) }},
        
        // Initialize exercises arrays
        exercises: {{ Illuminate\Support\Js::from($exercises) }},
        customExercises: {{ Illuminate\Support\Js::from($customExercises) }},
        
        // Weight tracking
        weight: '',
        weightEntryDate: '{{ now()->format('Y-m-d') }}',
        weightNotes: '',
        
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
        
        cloneMeal() {
            if (!this.selectedMealTemplate) {
                alert('Veuillez sélectionner un repas à cloner');
                return;
            }
            
            const mealId = this.selectedMealTemplate;
            const meal = this.recentMeals.find(m => m.id == mealId);
            
            if (!meal) return;
            
            this.foodNotes = meal.notes || '';
            
            // Clone meal items
            this.mealItems = [];
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
        
        cloneWorkout() {
            if (!this.selectedWorkoutTemplate) {
                alert('Veuillez sélectionner un entraînement à cloner');
                return;
            }
            
            const workoutId = this.selectedWorkoutTemplate;
            const workout = this.recentWorkouts.find(w => w.id == workoutId);
            
            if (!workout) return;
            
            this.exerciseNotes = workout.notes || '';
            
            // Clone workout items
            this.workoutItems = [];
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
        
        // Save methods
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
    }"
    x-init="
        foods = {{ Illuminate\Support\Js::from($foods) }};
        customFoods = {{ Illuminate\Support\Js::from($customFoods) }};
        exercises = {{ Illuminate\Support\Js::from($exercises) }};
        customExercises = {{ Illuminate\Support\Js::from($customExercises) }};
        recentMeals = {{ Illuminate\Support\Js::from($recentMeals) }};
        recentWorkouts = {{ Illuminate\Support\Js::from($recentWorkouts) }};
    ">
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
                            <button class="summary-card-action" @click="showFoodModal = true; mealItems = [];">
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
                            <button class="summary-card-action" @click="showExerciseModal = true; workoutItems = [];">
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
                                @foreach($todayMeals as $meal)
                                    <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;">
                                        <div style="font-weight: 600;">
                                            {{ $meal->name ?: 'Repas de ' . ucfirst($meal->meal_type) }}
                                            <span style="float: right; font-weight: normal;">{{ $meal->total_calories }} kcal</span>
                                        </div>
                                        <div style="color: #6b7280; font-size: 14px;">
                                            {{ \Carbon\Carbon::parse($meal->entry_time)->format('H:i') }} - {{ ucfirst($meal->meal_type) }}
                                        </div>
                                        <div style="margin-top: 5px;">
                                            <ul style="list-style: none; padding-left: 10px; margin-top: 5px;">
                                                @foreach($meal->mealItems as $item)
                                                    <li style="font-size: 14px; margin-bottom: 3px;">
                                                        • {{ $item->food->name }} ({{ $item->quantity }}{{ $item->unit }}) - {{ $item->calories }} kcal
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Vous n'avez pas encore enregistré de repas aujourd'hui.</p>
                            <p style="margin-top: 15px;">
                                <button class="btn btn-primary" @click="showFoodModal = true; mealItems = [];">Ajouter un repas</button>
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
                        
                        <!-- Clone meal section -->
                        <div class="form-group" x-show="recentMeals && recentMeals.length > 0">
                            <label for="selectedMealTemplate">Cloner un repas récent</label>
                            <div class="form-row">
                                <div class="form-group" style="flex-grow: 1;">
                                    <select id="selectedMealTemplate" class="form-control" x-model="selectedMealTemplate">
                                        <option value="">Sélectionner un repas</option>
                                        <template x-for="meal in recentMeals" :key="meal.id">
                                            <option :value="meal.id" x-text="meal.name ? meal.name + ' (' + meal.total_calories + ' kcal)' : 'Repas du ' + meal.entry_date + ' (' + meal.total_calories + ' kcal)'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" @click="cloneMeal()">Cloner</button>
                                </div>
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
                        
                        <!-- Clone workout section -->
                        <div class="form-group" x-show="recentWorkouts && recentWorkouts.length > 0">
                            <label for="selectedWorkoutTemplate">Cloner un entraînement récent</label>
                            <div class="form-row">
                                <div class="form-group" style="flex-grow: 1;">
                                    <select id="selectedWorkoutTemplate" class="form-control" x-model="selectedWorkoutTemplate">
                                        <option value="">Sélectionner un entraînement</option>
                                        <template x-for="workout in recentWorkouts" :key="workout.id">
                                            <option :value="workout.id" x-text="workout.name ? workout.name + ' (' + workout.total_calories_burned + ' kcal)' : 'Entraînement du ' + workout.entry_date + ' (' + workout.total_calories_burned + ' kcal)'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary" @click="cloneWorkout()">Cloner</button>
                                </div>
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
