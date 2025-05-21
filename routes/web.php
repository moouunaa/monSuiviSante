<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodTrackingController;
use App\Http\Controllers\ExerciseTrackingController;
use App\Http\Controllers\WaterTrackingController;
use App\Http\Controllers\SleepTrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\WeightTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Quiz routes with controller
Route::get('/quiz/step1', [QuizController::class, 'showStep1'])->name('quiz.step1');
Route::post('/quiz/step1', [QuizController::class, 'processStep1'])->name('quiz.process.step1');

Route::get('/quiz/step2', [QuizController::class, 'showStep2'])->name('quiz.step2');
Route::post('/quiz/step2', [QuizController::class, 'processStep2'])->name('quiz.process.step2');

Route::get('/quiz/step3', [QuizController::class, 'showStep3'])->name('quiz.step3');
Route::post('/quiz/step3', [QuizController::class, 'processStep3'])->name('quiz.process.step3');

Route::get('/quiz/step4', [QuizController::class, 'showStep4'])->name('quiz.step4');
Route::post('/quiz/step4', [QuizController::class, 'processStep4'])->name('quiz.process.step4');

Route::get('/quiz/step5', [QuizController::class, 'showStep5'])->name('quiz.step5');
Route::post('/quiz/step5', [QuizController::class, 'processStep5'])->name('quiz.process.step5');

// Update the register route to point to the quiz
Route::get('/register', function () {
    return redirect()->route('quiz.step1');
})->name('register');

// Profile route
Route::get('/profile', function () {
    return view('profile');
})->name('profile')->middleware('auth');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Suivi alimentaire
    Route::get('/food-tracking', [FoodTrackingController::class, 'index'])->name('food-tracking');
    Route::post('/food-tracking', [FoodTrackingController::class, 'store'])->name('food-tracking.store');
    Route::delete('/food-tracking/{id}', [FoodTrackingController::class, 'destroy'])->name('food-tracking.destroy');
    Route::post('/food-tracking/{id}/clone', [FoodTrackingController::class, 'clone'])->name('food-tracking.clone');
        
    // Suivi d'exercice
    Route::get('/exercise-tracking', [ExerciseTrackingController::class, 'index'])->name('exercise-tracking');
    Route::post('/exercise-tracking', [ExerciseTrackingController::class, 'store'])->name('exercise-tracking.store');
    Route::delete('/exercise-tracking/{id}', [ExerciseTrackingController::class, 'destroy'])->name('exercise-tracking.destroy');
    
    // Suivi d'hydratation
    Route::get('/water-tracking', [WaterTrackingController::class, 'index'])->name('water-tracking');
    Route::post('/water-tracking', [WaterTrackingController::class, 'store'])->name('water-tracking.store');
    
    // Suivi de sommeil
    Route::get('/sleep-tracking', [SleepTrackingController::class, 'index'])->name('sleep-tracking');
    Route::post('/sleep-tracking', [SleepTrackingController::class, 'store'])->name('sleep-tracking.store');
    
    // Suivi de poids
    Route::get('/weight-tracking', [WeightTrackingController::class, 'index'])->name('weight-tracking');
    Route::post('/weight-tracking', [WeightTrackingController::class, 'store'])->name('weight-tracking.store');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/calories', [GoalController::class, 'getCalorieGoal'])->name('goals.calories');
    Route::post('/goals/update', [GoalController::class, 'update'])->name('goals.update');
    Route::post('/goals/water/update', [GoalController::class, 'updateWaterGoal'])->name('goals.water.update');
    Route::post('/goals/sleep/update', [GoalController::class, 'updateSleepGoal'])->name('goals.sleep.update');
    Route::post('/goals/weight/update', [GoalController::class, 'updateWeightGoal'])->name('goals.weight.update');
});