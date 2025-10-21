<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le Mot de Passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Nouveau Mot de Passe</h2>
        <p class="text-gray-600 mb-6 text-center">Veuillez saisir votre nouvel identifiant.</p>

        <!-- Affichage des messages d'erreur de la session (e.g. mot de passe invalide) -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf

            <!-- Champ TOKEN (caché mais obligatoire) -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Champ EMAIL (pré-rempli) -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse E-mail</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" 
                       placeholder="votre.email@exemple.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Champ NOUVEAU MOT DE PASSE -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                       placeholder="Minimum 8 caractères">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Champ CONFIRMATION DU MOT DE PASSE -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Retapez votre mot de passe">
            </div>

            <!-- Bouton de soumission -->
            <div>
                <button type="submit" 
                        class="w-full py-3 px-4 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium text-sm transition duration-150 ease-in-out">
                    Réinitialiser le mot de passe
                </button>
            </div>

            <!-- Lien de retour -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Retour à la connexion
                </a>
            </div>
        </form>
    </div>

</body>
</html>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau Mot de Passe</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                       placeholder="Minimum 8 caractères">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Champ CONFIRMATION MOT DE PASSE -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le Mot de Passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Confirmer">
            </div>

            <!-- Bouton de Soumission -->
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Réinitialiser le Mot de Passe
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Annuler et revenir à la connexion
            </a>
        </div>
    </div>

</body>
</html>
