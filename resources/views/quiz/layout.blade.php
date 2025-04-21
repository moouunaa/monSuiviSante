<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonSuiviSanté - Questionnaire Initial</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
        }
        .quiz-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .progress-bar {
            height: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background-color: white;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        input:focus,
        select:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.3);
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-next {
            background-color: white;
            color: #4034e4;
        }
        .btn-prev {
            background-color: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn:hover {
            transform: translateY(-3px);
        }
        .btn-next:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .goal-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .goal-option {
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .goal-option:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .goal-option.selected {
            border-color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .goal-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .gender-options {
            display: flex;
            gap: 15px;
        }
        .gender-option {
            flex: 1;
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .gender-option:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .gender-option.selected {
            border-color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MonSuiviSanté</div>
        <div class="quiz-card">
            @yield('content')
        </div>
    </div>

    <script>
        // Simple frontend JavaScript for the quiz functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle goal selection
            const goalOptions = document.querySelectorAll('.goal-option');
            goalOptions.forEach(option => {
                option.addEventListener('click', function() {
                    goalOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    const goalInput = document.querySelector('input[name="goal"]');
                    if (goalInput) {
                        goalInput.value = this.dataset.value;
                    }
                });
            });

            // Handle gender selection
            const genderOptions = document.querySelectorAll('.gender-option');
            genderOptions.forEach(option => {
                option.addEventListener('click', function() {
                    genderOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    const genderInput = document.querySelector('input[name="gender"]');
                    if (genderInput) {
                        genderInput.value = this.dataset.value;
                    }
                });
            });
        });
    </script>
</body>
</html>