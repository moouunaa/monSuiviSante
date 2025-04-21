@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 100%"></div>
</div>

<h1>Créez votre compte</h1>
<p>Une dernière étape pour commencer votre parcours vers une meilleure santé.</p>

<form action="{{ route('register.submit') }}" method="POST">
    @csrf
    <input type="hidden" name="name" value="{{ request('name') }}">
    <input type="hidden" name="gender" value="{{ request('gender') }}">
    <input type="hidden" name="age" value="{{ request('age') }}">
    <input type="hidden" name="weight" value="{{ request('weight') }}">
    <input type="hidden" name="height" value="{{ request('height') }}">
    <input type="hidden" name="goal" value="{{ request('goal') }}">

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="votre@email.com" required>
    </div>

    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Créez un mot de passe sécurisé" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmez le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmez votre mot de passe" required>
    </div>

    <div class="btn-group">
        <a href="{{ route('quiz.step3') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Créer mon compte</button>
    </div>
</form>
@endsection