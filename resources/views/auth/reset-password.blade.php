<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le Mot de Passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Nouveau Mot de Passe</h2>
        <p class="text-gray-600 mb-6 text-center">Veuillez saisir votre nouveau mot de passe.</p>

        <!-- Messages d'erreur de validation -->
        <div id="errorMessages" class="mb-4"></div>
        
        <form id="resetForm" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Minimum 8 caractères">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Retapez votre mot de passe">
            </div>

            <div>
                <button type="submit" 
                        class="w-full py-3 px-4 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 font-medium text-sm transition duration-150 ease-in-out">
                    Réinitialiser le mot de passe
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Retour à la connexion
                </a>
            </div>
        </form>
    </div>

    <!-- Pop-up de succès -->
    <div id="successPopup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-80 text-center transform scale-90 opacity-0 transition duration-300">
            <h3 class="text-green-600 font-bold text-lg mb-4">Succès !</h3>
            <p class="text-gray-700 mb-6" id="successMessage"></p>
            <a href="{{ route('login') }}" 
               class="inline-block px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                Retour à la connexion
            </a>
        </div>
    </div>

<script>
const resetForm = document.getElementById('resetForm');
const successPopup = document.getElementById('successPopup');
const successMessage = document.getElementById('successMessage');
const errorMessages = document.getElementById('errorMessages');

resetForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorMessages.innerHTML = '';

    const formData = new FormData(resetForm);

    try {
        const response = await axios.post("{{ route('password.update') }}", formData);

        if (response.data.success) {
            successMessage.textContent = response.data.message;
            successPopup.classList.remove('hidden');

            // Animation pop-up
            setTimeout(() => {
                const popupContent = successPopup.querySelector('div');
                popupContent.classList.add('scale-100', 'opacity-100');
            }, 50);
        }
    } catch (err) {
        if (err.response && err.response.data) {
            if (err.response.data.errors) {
                // Affiche les erreurs de validation
                const ul = document.createElement('ul');
                ul.className = 'list-disc list-inside text-red-600';
                Object.values(err.response.data.errors).flat().forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
                errorMessages.appendChild(ul);
            } else {
                errorMessages.innerHTML = `<p class="text-red-600">${err.response.data.message || 'Une erreur est survenue.'}</p>`;
            }
        } else {
            errorMessages.innerHTML = `<p class="text-red-600">Erreur serveur.</p>`;
        }
    }
});
</script>

</body>
</html>