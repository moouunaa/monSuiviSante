<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Prenez le contrôle de votre bien-être</title>
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
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
        }
        .profile-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white; 
            text-decoration: none;
        }

        .profile-icon:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        .hero {
            padding: 100px 0;
            max-width: 600px;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: white;
            color: #4034e4;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        .feature-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 30px;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 0.15);
        }
        .feature-icon {
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 36px;
            }
            .hero {
                padding: 50px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">MonSuiviSanté</div>
            <a href="{{ route('login') }}" class="profile-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        </header>

        <main>
            <section class="hero">
                <h1>Prenez le contrôle de votre bien-être</h1>
                <p>Adoptez de meilleures habitudes avec un outil complet de suivi de votre alimentation, activité physique et hydratation.</p>
                <a href="{{ route('register') }}" class="btn">Commencer dès aujourd'hui</a>
            </section>

            <section class="features">
                <div class="feature-card">
                    <div class="feature-icon">🍎</div>
                    <h3>Suivi Nutritionnel</h3>
                    <p>Enregistrez vos repas et suivez votre bilan calorique quotidien pour atteindre vos objectifs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏃‍♂️</div>
                    <h3>Activité Physique</h3>
                    <p>Suivez vos exercices et visualisez votre progression au fil du temps.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💧</div>
                    <h3>Hydratation</h3>
                    <p>Gardez une trace de votre consommation d'eau pour rester bien hydraté.</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>