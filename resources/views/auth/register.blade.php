@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-2 border border-gray-0">
        
        <!-- Logo + Titre -->
        <div class="text-center mb-6">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" 
                 class="h-14 w-auto mx-auto mb-3">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                Inscription
            </h2>
        </div>

        <!-- Affichage des erreurs -->
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg shadow-sm text-sm md:text-base" role="alert">
                <p class="font-bold">Erreur(s) de Validation</p>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Formulaire -->
        <form id="registerForm" class="space-y-5">
            @csrf

            <!-- Nom -->
            <div>
                <label for="name" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Nom</label>
                <input type="text" name="name" id="name" required placeholder="Nom complet"
                    class="w-full border rounded-lg px-4 py-2 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150"
                    value="{{ old('name') }}" autofocus>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Email</label>
                <input type="email" name="email" id="email" required placeholder="Votre email"
                    class="w-full border rounded-lg px-4 py-2 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150"
                    value="{{ old('email') }}">
            </div>

            <!-- Département -->
            <div>
                <label for="department" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Département</label>
                <select name="department" id="department" required
                    class="w-full border rounded-lg px-4 py-2 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
                    <option value="" disabled selected>Sélectionner un département</option>
                    <option value="Technique" {{ old('department') == 'Technique' ? 'selected' : '' }}>Technique</option>
                    <option value="Logistique" {{ old('department') == 'Logistique' ? 'selected' : '' }}>Logistique</option>
                    <option value="Administratif" {{ old('department') == 'Administratif' ? 'selected' : '' }}>Administratif</option>
                    <option value="Commercial" {{ old('department') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                </select>
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Mot de passe</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="Mot de passe"
                        class="w-full border rounded-lg px-4 py-2 pr-10 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-green-600 focus:outline-none">
                      <!--  <svg id="eyeClosed1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 001.523-.423M9.88 9.88A4.992 4.992 0 0112 9c2.761 0 5 2.239 5 5a4.992 4.992 0 01-.88 2.12M15 15l3 3M9.88 9.88L7 7" />
                        </svg>-->
                        <svg id="eyeOpen1" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Confirmation -->
            <div>
                <label for="password_confirmation" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Confirmer le mot de passe</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Confirmer"
                        class="w-full border rounded-lg px-4 py-2 pr-10 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
                    <button type="button" id="toggleConfirm"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-green-600 focus:outline-none">
                    <!--    <svg id="eyeClosed2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 001.523-.423M9.88 9.88A4.992 4.992 0 0112 9c2.761 0 5 2.239 5 5a4.992 4.992 0 01-.88 2.12M15 15l3 3M9.88 9.88L7 7" />
                        </svg>-->
                        <svg id="eyeOpen2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Bouton -->
            <button type="submit" id="registerBtn"
                class="w-full bg-green-700 text-white py-2.5 md:py-3 rounded-lg font-bold text-base shadow-md hover:bg-green-800 transition transform hover:scale-[1.01] flex justify-center items-center">
                <i id="spinnerRegister" class="fas fa-spinner fa-spin mr-2 hidden"></i>
                <span id="btnTextRegister">S'inscrire</span>
            </button>
        </form>

        <!-- Lien vers login -->
        <div class="mt-5 text-center border-t pt-4">
            <a href="{{ route('login') }}" class="text-sm text-green-700 hover:text-green-800 font-medium underline">
                Déjà inscrit ? Se connecter
            </a>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

<!-- JS pour l’affichage des mots de passe -->
<script>
    function setupPasswordToggle(inputId, openId, closedId, buttonId) {
        const input = document.getElementById(inputId);
        const eyeOpen = document.getElementById(openId);
        const eyeClosed = document.getElementById(closedId);
        const button = document.getElementById(buttonId);

        button.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', !isPassword);
            eyeClosed.classList.toggle('hidden', isPassword);
        });
    }

    setupPasswordToggle('password', 'eyeOpen1', 'eyeClosed1', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'eyeOpen2', 'eyeClosed2', 'toggleConfirm');

    // Script d’inscription AJAX
    $(document).ready(function () {
        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            $('#spinnerRegister').removeClass('hidden');
            $('#btnTextRegister').text("Inscription...");
            $('#registerBtn').attr('disabled', true);

            $.ajax({
                url: "{{ route('register') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function () {
                    window.location.href = "/";
                },
                error: function () {
                    alert("Erreur : Vérifiez vos données.");
                },
                complete: function () {
                    $('#spinnerRegister').addClass('hidden');
                    $('#btnTextRegister').text("S'inscrire");
                    $('#registerBtn').attr('disabled', false);
                }
            });
        });
    });
</script>
@endsection
