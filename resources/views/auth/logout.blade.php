<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <style>
        /* Reset et style global */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e2e8f0, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #1a202c;
        }

        /* Container principal */
        .logout-container {
            text-align: center;
            background: #ffffff;
            padding: 3rem 4rem;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 90%;
            transition: transform 0.3s ease;
        }
        .logout-container:hover {
            transform: translateY(-5px);
        }

        /* Titre */
        .logout-container h1 {
            font-size: 2rem;
            color: #ff4d4f;
            margin-bottom: 1rem;
        }

        /* Texte */
        .logout-container p {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: #4a5568;
        }

        /* Bouton */
        .logout-container a {
            display: inline-block;
            text-decoration: none;
            color: #ffffff;
            background: #007bff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .logout-container a:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        /* Compte à rebours */
        .countdown {
            font-weight: bold;
            color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1>Déconnecté !</h1>
        <p>Vous avez été déconnecté avec succès.</p>
        <p>Redirection vers la page de connexion dans <span class="countdown" id="count">5</span> secondes...</p>
        <a href="{{ route('login') }}">Se reconnecter</a>
    </div>

    <script>
        let countdown = 5;
        const countdownEl = document.getElementById('count');

        const interval = setInterval(() => {
            countdown--;
            if(countdown <= 0){
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            } else {
                countdownEl.textContent = countdown;
            }
        }, 1000);
    </script>
</body>
</html>
