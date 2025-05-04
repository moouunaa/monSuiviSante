@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 75%"></div>
</div>

<h1>Quel est votre objectif ?</h1>
<p>Choisissez l'objectif qui correspond le mieux à vos attentes.</p>

<form action="{{ route('quiz.process.step3') }}" method="POST">
    @csrf
    <input type="hidden" name="name" value="{{ session('quiz_data.name') }}">
    <input type="hidden" name="gender" value="{{ session('quiz_data.gender') }}">
    <input type="hidden" name="age" value="{{ session('quiz_data.age') }}">
    <input type="hidden" name="weight" value="{{ session('quiz_data.weight') }}">
    <input type="hidden" name="height" value="{{ session('quiz_data.height') }}">
    <input type="hidden" name="goal" id="goal-input" value="{{ session('quiz_data.goal', '') }}">

    <div class="goal-options">
        <div class="goal-option" data-value="lose">
            <div class="goal-icon">⬇️</div>
            <div>Perdre du poids</div>
        </div>
        <div class="goal-option" data-value="maintain">
            <div class="goal-icon">⚖️</div>
            <div>Maintenir mon poids</div>
        </div>
        <div class="goal-option" data-value="gain">
            <div class="goal-icon">⬆️</div>
            <div>Prendre du poids</div>
        </div>
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step2') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Suivant</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const goalOptions = document.querySelectorAll('.goal-option');
    const goalInput = document.getElementById('goal-input');
    
    // Set initial selected goal if exists in session
    const currentGoal = "{{ session('quiz_data.goal', '') }}";
    if (currentGoal) {
        goalOptions.forEach(option => {
            if (option.dataset.value === currentGoal) {
                option.classList.add('selected');
                goalInput.value = currentGoal;
            }
        });
    }
    
    goalOptions.forEach(option => {
        option.addEventListener('click', function() {
            goalOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            goalInput.value = this.dataset.value;
        });
    });
});
</script>
@endsection