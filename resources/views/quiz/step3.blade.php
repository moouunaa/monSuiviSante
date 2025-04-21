@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 75%"></div>
</div>

<h1>Quel est votre objectif ?</h1>
<p>Choisissez l'objectif qui correspond le mieux à vos attentes.</p>

<form action="{{ route('quiz.step4') }}" method="GET">
    <input type="hidden" name="name" value="{{ request('name') }}">
    <input type="hidden" name="gender" value="{{ request('gender') }}">
    <input type="hidden" name="age" value="{{ request('age') }}">
    <input type="hidden" name="weight" value="{{ request('weight') }}">
    <input type="hidden" name="height" value="{{ request('height') }}">
    <input type="hidden" name="goal" value="">

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
@endsection