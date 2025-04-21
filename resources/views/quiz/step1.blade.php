@extends('quiz.layout')

@section('content')
<div class="progress-bar">
    <div class="progress-fill" style="width: 25%"></div>
</div>

<h1>Comment vous appelez-vous ?</h1>
<p>Nous aimerions vous connaître un peu mieux.</p>

<form action="{{ route('quiz.process.step1') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Votre nom</label>
        <input type="text" id="name" name="name" placeholder="Entrez votre nom" required>
        @error('name')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="btn-group">
        <a href="{{ route('welcome') }}" class="btn btn-prev">Retour</a>
        <button type="submit" class="btn btn-next">Suivant</button>
    </div>
</form>
@endsection