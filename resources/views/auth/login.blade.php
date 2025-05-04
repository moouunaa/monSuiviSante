<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Connexion</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            background: linear-gradient(135deg, #4034e4 0%, #105cec 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
        }
        .login-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        input:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.3);
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 16px;
        }
        .btn-login {
            background-color: white;
            color: #4034e4;
            margin-bottom: 15px;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: white;
            text-decoration: underline;
        }
        .error-message {
            color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.1);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-home a {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .back-to-home a:hover {
            text-decoration: underline;
        }
        .back-icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MonSuiviSanté</div>
        <div class="login-card">
            <h1>Connexion</h1>
            
            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="votre@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" required placeholder="Votre mot de passe">
                </div>
                
                <button type="submit" class="btn btn-login">Se connecter</button>
            </form>
            
            <div class="register-link">
                Pas encore de compte? <a href="{{ route('register') }}">S'inscrire</a>
            </div>
        </div>
        
        <div class="back-to-home">
            <a href="{{ route('welcome') }}">
                <svg class="back-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</body>
</html>
