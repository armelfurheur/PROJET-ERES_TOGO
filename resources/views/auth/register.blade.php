@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 border border-gray-200">
        <!-- Logo + Titre -->
        <div class="text-center mb-8">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" 
                 class="h-16 w-auto mx-auto mb-4">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">
               S'Inscription a<span class="highlight">ERESriskAlert</span>
            </h2>
            <p class="text-sm text-gray-600 mt-2">Créez votre compte ERESriskalert</p>
        </div>

        <!-- Affichage des erreurs -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm" role="alert">
                <p class="font-bold">Erreur(s) de validation :</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <form id="registerForm" class="space-y-6">
            @csrf

            <!-- NOM + PRÉNOM SUR LA MÊME LIGNE -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Nom & prenom(s) <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nom de famille -->
                    <div>
                        <input type="text" name="lastname" id="lastname" required 
                               placeholder="Nom de famille" autofocus
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition placeholder-gray-400"
                               value="{{ old('lastname') }}">
                    </div>

                    <!-- Prénom(s) -->
                     
                    <div>
                        <input type="text" name="firstname" id="firstname" required 
                               placeholder="Prénom(s)"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition placeholder-gray-400"
                               value="{{ old('firstname') }}">
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-2 font-semibold text-gray-700">
                    Adresse email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" required 
                       placeholder="votre.email@erestogo.Com"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                       value="{{ old('email') }}">
            </div>

            <!-- Département -->
            <div>
                <label for="department" class="block mb-2 font-semibold text-gray-700">
                    Département <span class="text-red-500">*</span>
                </label>
                <select name="department" id="department" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="" disabled selected>Choisir votre département</option>
                    <option value="Technique" {{ old('department') == 'Technique' ? 'selected' : '' }}>Technique</option>
                    <option value="Logistique" {{ old('department') == 'Logistique' ? 'selected' : '' }}>Logistique</option>
                    <option value="Administratif" {{ old('department') == 'Administratif' ? 'selected' : '' }}>Administratif</option>
                    <option value="Commercial" {{ old('department') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="Achat" {{ old('department') == 'Achat' ? 'selected' : '' }}>Achat</option>
                </select>
            </div>

            <!-- Code Admin (facultatif) -->
            <div>
                <label for="admin_code" class="block mb-2 font-semibold text-gray-700">
                    Code Admin <span class="text-gray-500">(facultatif)</span>
                </label>
                <input type="text" name="admin_code" id="admin_code" autocomplete="off"
                       placeholder="Réservé aux administrateurs"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block mb-2 font-semibold text-gray-700">
                    Mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password" required 
                           placeholder="Minimum 8 caractères"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-green-600"
                            data-target="password">
                        <svg class="eye-open hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                        </svg>
                        <svg class="eye-closed w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Confirmation mot de passe -->
            <div>
                <label for="password_confirmation" class="block mb-2 font-semibold text-gray-700">
                    Confirmer le mot de passe <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                           placeholder="Répétez votre mot de passe"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-green-600"
                            data-target="password_confirmation">
                        <svg class="eye-open hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                        </svg>
                        <svg class="eye-closed w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Bouton d'inscription -->
            <button type="submit" id="registerBtn"
                class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-4 rounded-lg shadow-lg transition transform hover:scale-105 flex justify-center items-center text-lg">
                <svg id="spinnerRegister" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="btnTextRegister">Créer mon compte</span>
            </button>
        </form>

        <!-- Lien vers connexion -->
        <div class="mt-8 text-center border-t pt-6">
            <p class="text-sm text-gray-600">
                Déjà inscrit ? 
                <a href="{{ route('login') }}" class="text-green-700 font-bold hover:text-green-800 underline">
                    Se connecter
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        $('.toggle-password').on('click', function () {
            const target = $(this).data('target');
            const input = $('#' + target);
            const isPassword = input.attr('type') === 'password';

            input.attr('type', isPassword ? 'text' : 'password');
            $(this).find('.eye-open').toggleClass('hidden', !isPassword);
            $(this).find('.eye-closed').toggleClass('hidden', isPassword);
        });

        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            $('#spinnerRegister').removeClass('hidden');
            $('#btnTextRegister').text('Création en cours...');
            $('#registerBtn').attr('disabled', true);

            $.ajax({
                url: "{{ route('register') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        alert(response.message || "Inscription réussie !");
                        window.location.href = response.redirect || "/formulaire";
                    } else {
                        alert("Erreur : " + (response.message || "Une erreur est survenue."));
                    }
                },
                error: function (xhr) {
                    let msg = "Veuillez vérifier vos informations.";
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join("\n");
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert("Erreur :\n" + msg);
                },
                complete: function () {
                    $('#spinnerRegister').addClass('hidden');
                    $('#btnTextRegister').text('Créer mon compte');
                    $('#registerBtn').attr('disabled', false);
                }
            });
        });
    });
</script>

<style>
    /* Style pour le span ERESriskAlert */
.highlight {
    background: linear-gradient(90deg, #07411cff, #22c55e); /* dégradé vert */
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 900;
    font-size: 1.8rem; /* légèrement plus grand */
    position: relative;
    display: inline-block;
    transition: transform 0.3s ease, filter 0.3s ease;
}

/* Animation hover */
.highlight:hover {
    transform: scale(1.1) rotate(-2deg);
    filter: drop-shadow(0 4px 8px rgba(34, 197, 94, 0.6));
    cursor: pointer;
}

/* Optionnel : animation "pulse" douce */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.highlight.pulse {
    animation: pulse 2s infinite;
}
</style>
@endsection