<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Quiz routes with controller
Route::get('/quiz/step1', [QuizController::class, 'showStep1'])->name('quiz.step1');
Route::post('/quiz/step1', [QuizController::class, 'processStep1'])->name('quiz.process.step1');

Route::get('/quiz/step2', [QuizController::class, 'showStep2'])->name('quiz.step2');
Route::post('/quiz/step2', [QuizController::class, 'processStep2'])->name('quiz.process.step2');

Route::get('/quiz/step3', [QuizController::class, 'showStep3'])->name('quiz.step3');
Route::post('/quiz/step3', [QuizController::class, 'processStep3'])->name('quiz.process.step3');

Route::get('/quiz/step4', [QuizController::class, 'showStep4'])->name('quiz.step4');
Route::post('/quiz/step4', [QuizController::class, 'processStep4'])->name('quiz.process.step4');

// Update the register route to point to the quiz
Route::get('/register', function () {
    return redirect()->route('quiz.step1');
})->name('register');

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// Profile route
Route::get('/profile', function () {
    return view('profile');
})->name('profile')->middleware('auth');