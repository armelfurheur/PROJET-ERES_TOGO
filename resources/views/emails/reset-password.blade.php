<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .content h2 {
            color: #0d6efd;
            font-size: 20px;
        }
        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 25px;
            background: #198754;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
        }
        .button:hover {
            background: #157347;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>Réinitialisation de votre mot de passe</h1>
        </div>

        <!-- Contenu -->
        <div class="content">
            <h2>Bonjour,</h2>
            <p>
                Vous avez demandé à réinitialiser votre mot de passe.  
                Cliquez sur le bouton ci-dessous pour procéder :
            </p>
            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">
                    Réinitialiser mon mot de passe
                </a>
            </p>
            <p>
                Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer cet email en toute sécurité.
            </p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Votre Application. Tous droits réservés.</p>
            <p>Besoin d’aide ? <a href="mailto:support@votreapp.com">Contactez notre support</a></p>
        </div>
    </div>
</body>
</html>
