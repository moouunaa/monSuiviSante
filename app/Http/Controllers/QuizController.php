<?php

namespace App\Http\Controllers;

use App\Builders\UserProfileBuilder;
use App\Services\ProfileService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    public function showStep1()
    {
        Session::forget('quiz_data');
        
        return view('quiz.step1');
    }
    
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        // Stocker dans la session
        Session::put('quiz_data.name', $validated['name']);
        
        return redirect()->route('quiz.step2');
    }
    
    public function showStep2()
    {
        return view('quiz.step2');
    }
    
    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required|in:male,female',
            'age' => 'required|integer|min:16|max:100',
            'weight' => 'required|numeric|min:30|max:300',
            'height' => 'required|integer|min:100|max:250',
        ]);
        
        // Stocker dans la session
        Session::put('quiz_data.gender', $validated['gender']);
        Session::put('quiz_data.age', $validated['age']);
        Session::put('quiz_data.weight', $validated['weight']);
        Session::put('quiz_data.height', $validated['height']);
        
        return redirect()->route('quiz.step3');
    }
    
    public function showStep3()
    {
        return view('quiz.step3');
    }
    
    public function processStep3(Request $request)
    {
        $validated = $request->validate([
            'goal' => 'required|in:lose,maintain,gain',
        ]);
        
        // Stocker dans la session
        Session::put('quiz_data.goal', $validated['goal']);
        
        return redirect()->route('quiz.step4');
    }
    
    public function showStep4()
    {
        return view('quiz.step4');
    }
    
    public function processStep4(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:free,premium',
        ]);
        
        // Store in session
        Session::put('quiz_data.plan', $validated['plan']);
        
        return redirect()->route('quiz.step5');
    }
    
    public function showStep5()
    {
        return view('quiz.step5');
    }
    
    public function processStep5(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Get all data from session
        $userData = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'name' => session('quiz_data.name'),
            'gender' => session('quiz_data.gender'),
            'age' => session('quiz_data.age'),
            'weight' => session('quiz_data.weight'),
            'height' => session('quiz_data.height'),
            'goal' => session('quiz_data.goal'),
            'plan' => session('quiz_data.plan'),
        ];
        
        // Use the Builder pattern to create the user profile
        $profileBuilder = new UserProfileBuilder();
        $profileService = new ProfileService($profileBuilder);
        
        $result = $profileService->createUserProfile($userData);
        
        // Log the user in
        Auth::login($result['user']);
        
        // Clear quiz data from session
        Session::forget('quiz_data');
        
        return redirect()->route('dashboard');
    }
}