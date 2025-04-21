@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 50%"></div>
</div>

<h1>Parlez-nous de vous</h1>
<p>Ces informations nous aideront à personnaliser votre expérience.</p>

<form action="{{ route('quiz.step3') }}" method="GET">
    <input type="hidden" name="name" value="{{ request('name') }}">
    <input type="hidden" name="gender" value="">
    
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
        <input type="number" id="age" name="age" min="16" max="100" placeholder="Votre âge" required>
    </div>

    <div class="form-group">
        <label for="weight">Poids (kg)</label>
        <input type="number" id="weight" name="weight" min="30" max="300" step="0.1" placeholder="Votre poids en kg" required>
    </div>

    <div class="form-group">
        <label for="height">Taille (cm)</label>
        <input type="number" id="height" name="height" min="100" max="250" placeholder="Votre taille en cm" required>
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step1') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Suivant</button>
    </div>
</form>
@endsection