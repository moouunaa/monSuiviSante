@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 50%"></div>
</div>

<h1>Parlez-nous de vous</h1>
<p>Ces informations nous aideront à personnaliser votre expérience.</p>

<form action="{{ route('quiz.process.step2') }}" method="POST">
    @csrf
    <input type="hidden" name="name" value="{{ session('quiz_data.name') }}">
    <input type="hidden" name="gender" id="gender-input" value="{{ session('quiz_data.gender', '') }}">
    
    <div class="form-group">
        <label>Sexe</label>
        <div class="gender-options">
            <div class="gender-option" data-value="male">
                <div>♂️</div>
                <div>Homme</div>
            </div>
            <div class="gender-option" data-value="female">
                <div>♀️</div>
                <div>Femme</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="age">Âge</label>
        <input type="number" id="age" name="age" min="16" max="100" placeholder="Votre âge" required value="{{ session('quiz_data.age', '') }}">
    </div>

    <div class="form-group">
        <label for="weight">Poids (kg)</label>
        <input type="number" id="weight" name="weight" min="30" max="300" step="0.1" placeholder="Votre poids en kg" required value="{{ session('quiz_data.weight', '') }}">
    </div>

    <div class="form-group">
        <label for="height">Taille (cm)</label>
        <input type="number" id="height" name="height" min="100" max="250" placeholder="Votre taille en cm" required value="{{ session('quiz_data.height', '') }}">
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step1') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Suivant</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const genderOptions = document.querySelectorAll('.gender-option');
    const genderInput = document.getElementById('gender-input');
    
    // Set initial selected gender if exists in session
    const currentGender = "{{ session('quiz_data.gender', '') }}";
    if (currentGender) {
        genderOptions.forEach(option => {
            if (option.dataset.value === currentGender) {
                option.classList.add('selected');
                genderInput.value = currentGender;
            }
        });
    }
    
    genderOptions.forEach(option => {
        option.addEventListener('click', function() {
            genderOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            genderInput.value = this.dataset.value;
        });
    });
});
</script>
@endsection