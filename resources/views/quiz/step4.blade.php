@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 80%"></div>
</div>

<h1>Choisissez votre plan</h1>
<p>Sélectionnez le plan qui correspond le mieux à vos besoins.</p>

<form action="{{ route('quiz.process.step4') }}" method="POST">
    @csrf
    <input type="hidden" name="name" value="{{ session('quiz_data.name') }}">
    <input type="hidden" name="gender" value="{{ session('quiz_data.gender') }}">
    <input type="hidden" name="age" value="{{ session('quiz_data.age') }}">
    <input type="hidden" name="weight" value="{{ session('quiz_data.weight') }}">
    <input type="hidden" name="height" value="{{ session('quiz_data.height') }}">
    <input type="hidden" name="goal" value="{{ session('quiz_data.goal') }}">
    <input type="hidden" name="plan" id="selected-plan" value="free">

    <div class="plan-options">
        <div class="plan-option" data-value="free">
            <div class="plan-header">
                <h3>Plan Gratuit</h3>
                <div class="plan-price">0€</div>
            </div>
            <ul class="plan-features">
                <li>Suivi de 3 objectifs maximum</li>
                <li>Statistiques basiques</li>
                <li>Historique limité à 7 jours</li>
                <li>Plan de repas basique</li>
            </ul>
            <button type="button" class="btn btn-select-plan" data-plan="free">Sélectionner</button>
        </div>
        
        <div class="plan-option" data-value="premium">
            <div class="plan-header">
                <h3>Plan Premium</h3>
                <div class="plan-price">9,99€ <span>/mois</span></div>
            </div>
            <ul class="plan-features">
                <li>Suivi de 10 objectifs</li>
                <li>Statistiques détaillées</li>
                <li>Historique illimité</li>
                <li>Plan de repas personnalisé</li>
                <li>Recommandations avancées</li>
                <li>Support prioritaire</li>
            </ul>
            <button type="button" class="btn btn-select-plan" data-plan="premium">Sélectionner</button>
        </div>
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step3') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Suivant</button>
    </div>
</form>

<style>
    .plan-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .plan-option {
        background-color: rgba(255, 255, 255, 0.1);
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .plan-option:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .plan-option.selected {
        border-color: white;
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .plan-header {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .plan-price {
        font-size: 24px;
        font-weight: bold;
    }
    
    .plan-price span {
        font-size: 14px;
        font-weight: normal;
    }
    
    .plan-features {
        list-style-type: none;
        padding: 0;
        margin: 0 0 20px 0;
    }
    
    .plan-features li {
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .plan-features li:last-child {
        border-bottom: none;
    }
    
    .btn-select-plan {
        width: 100%;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-select-plan:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }
    
    .selected .btn-select-plan {
        background-color: white;
        color: #4034e4;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const planOptions = document.querySelectorAll('.plan-option');
        const selectedPlanInput = document.getElementById('selected-plan');
        
        // Sélectionner le plan gratuit par défaut
        planOptions[0].classList.add('selected');
        
        planOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Retirer la classe selected de toutes les options
                planOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Ajouter la classe selected à l'option cliquée
                this.classList.add('selected');
                
                // Mettre à jour la valeur du champ caché
                selectedPlanInput.value = this.dataset.value;
            });
        });
        
        const selectButtons = document.querySelectorAll('.btn-select-plan');
        selectButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const planValue = this.dataset.plan;
                
                // Retirer la classe selected de toutes les options
                planOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Ajouter la classe selected à l'option parent
                this.closest('.plan-option').classList.add('selected');
                
                // Mettre à jour la valeur du champ caché
                selectedPlanInput.value = planValue;
            });
        });
    });
</script>
@endsection