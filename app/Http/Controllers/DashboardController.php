<?php

namespace App\Http\Controllers;

use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $planService;
    
    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }
    
    public function index()
    {
        $user = Auth::user();
        $planType = $user->profile->plan;
        
        // Obtenir les fonctionnalités basées sur le plan de l'utilisateur
        $planFactory = $this->planService->getPlanFactory($planType);
        
        $goalTracker = $planFactory->createGoalTracker();
        $statistics = $planFactory->createStatistics();
        $mealPlanner = $planFactory->createMealPlanner();
        
        // Récupérer les données pour le dashboard
        $goalProgress = $goalTracker->getGoalProgress($user->id);
        $stats = $statistics->generateStats($user->id);
        $availableCharts = $statistics->getAvailableCharts();
        $suggestedMeals = $mealPlanner->getSuggestedMeals($user->id);
        
        return view('dashboard', compact(
            'user',
            'planType',
            'goalProgress',
            'stats',
            'availableCharts',
            'suggestedMeals'
        ));
    }
}