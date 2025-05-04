@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 100%"></div>
</div>

<h1>Créez votre compte</h1>
<p>Une dernière étape pour commencer votre parcours vers une meilleure santé.</p>

<form action="{{ route('quiz.process.step5') }}" method="POST">
    @csrf
    
    <input type="hidden" name="name" value="{{ session('quiz_data.name', 'Utilisateur') }}">
    <input type="hidden" name="gender" value="{{ session('quiz_data.gender', 'male') }}">
    <input type="hidden" name="age" value="{{ session('quiz_data.age', 30) }}">
    <input type="hidden" name="weight" value="{{ session('quiz_data.weight', 70) }}">
    <input type="hidden" name="height" value="{{ session('quiz_data.height', 170) }}">
    <input type="hidden" name="goal" value="{{ session('quiz_data.goal', 'maintain') }}">
    <input type="hidden" name="plan" value="{{ session('quiz_data.plan', 'free') }}">
    
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
        @error('email')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Créez un mot de passe sécurisé" required>
        @error('password')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmez le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmez votre mot de passe" required>
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step4') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Créer mon compte</button>
    </div>
</form>
@endsection