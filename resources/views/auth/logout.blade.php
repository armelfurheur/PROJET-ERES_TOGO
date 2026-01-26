<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.35);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.4s ease forwards;
        }

        /* Popup */
        .popup {
            background: #ffffff;
            border-radius: 22px;
            width: 450px;
            padding: 2.8rem;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            animation: zoomIn 0.35s ease forwards;
            position: relative;
            overflow: hidden;
        }

        /* Confetti léger */
        .confetti {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #ffb703;
            border-radius: 50%;
            animation: confetti 2.5s ease-out infinite;
            opacity: 0;
        }

        .confetti:nth-child(2) {
            left: 75%;
            background: #fb8500;
            animation-delay: 0.3s;
        }

        .confetti:nth-child(3) {
            left: 20%;
            background: #8ecae6;
            animation-delay: 0.6s;
        }

        .confetti:nth-child(4) {
            left: 50%;
            background: #219ebc;
            animation-delay: 1s;
        }

        @keyframes confetti {
            0% {
                top: -10px;
                opacity: 1;
                transform: rotate(0deg);
            }
            100% {
                top: 500px;
                opacity: 0;
                transform: rotate(360deg);
            }
        }

        /* Icône */
        .popup .icon {
            width: 80px;
            height: 80px;
            background: #fff1f2;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1rem auto;
            color: #dc2626;
            font-size: 38px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        /* Titres */
        .popup h1 {
            color: #0f172a;
            font-size: 2rem;
            margin-bottom: 0.7rem;
            font-weight: 700;
        }

        .popup h2 {
            color: #16a34a;
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
            font-weight: 600;
        }

        /* Texte */
        .popup p {
            color: #475569;
            margin-bottom: 1.4rem;
            font-size: 1.05rem;
        }

        .countdown {
            font-weight: bold;
            color: #2563eb;
        }

        /* Bouton */
        .popup a {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 0.75rem 1.6rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        .popup a:hover {
            background: #1e40af;
            transform: scale(1.06);
        }

        /* Animations globales */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.85);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ===============================
           ERESriskalert - Effet FEU / DANGER
           =============================== */

        .brand-fire {
            font-weight: 800;
            letter-spacing: 0.5px;
            background: linear-gradient(
                90deg,
                #c74040,
                #c57a35,
                #832828
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fireGlow 3.5s ease-in-out infinite;
            text-shadow:
                0 0 6px rgba(255, 94, 0, 0.45),
                0 0 12px rgba(255, 0, 0, 0.25);
        }

        @keyframes fireGlow {
            0% {
                background-position: 0% center;
                text-shadow:
                    0 0 5px rgba(255, 94, 0, 0.35),
                    0 0 10px rgba(255, 0, 0, 0.2);
            }
            50% {
                background-position: 100% center;
                text-shadow:
                    0 0 8px rgba(255, 94, 0, 0.55),
                    0 0 16px rgba(255, 0, 0, 0.35);
            }
            100% {
                background-position: 0% center;
                text-shadow:
                    0 0 5px rgba(255, 94, 0, 0.35),
                    0 0 10px rgba(255, 0, 0, 0.2);
            }
        }
    </style>
</head>

<body>

<div class="overlay">
    <div class="popup">

        <!-- Confettis -->
        <div class="confetti"></div>
        <div class="confetti"></div>
        <div class="confetti"></div>
        <div class="confetti"></div>

        <div class="icon">⚠️</div>

        <h1>
            Merci pour votre visite sur
            <span class="brand-fire">ERESriskalert</span>
        </h1>

        <h2>Vous êtes maintenant déconnecté</h2>

        <p>
            Merci d’avoir utilisé notre plateforme
            <span class="brand-fire">ERESriskalert</span>.
            Nous espérons vous revoir très bientôt !
        </p>

        <p>
            Redirection vers la page de connexion dans
            <span class="countdown" id="count">5</span> secondes...
        </p>

        <a href="{{ route('login') }}">Se reconnecter</a>

    </div>
</div>

<script>
    let countdown = 5;
    const countdownEl = document.getElementById('count');

    const interval = setInterval(() => {
        countdown--;
        if (countdown <= 0) {
            clearInterval(interval);
            window.location.href = "{{ route('login') }}";
        } else {
            countdownEl.textContent = countdown;
        }
    }, 1000);
</script>

</body>
</html>
