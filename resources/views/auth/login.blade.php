@extends('layouts.app')

@section('title', 'Connexion | ERES-TOGO')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-6 border border-gray-200">
        <div class="text-center mb-6">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" class="h-14 w-auto mx-auto mb-3">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">Se Connecter</h2>
        </div>

        {{-- Formulaire --}}
        <form id="loginForm" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">Adresse E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="Entrer votre Email"
                    class="w-full border rounded-lg px-4 py-2 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
            </div>

            <div class="relative">
    <label for="password" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">
        Mot de passe
    </label>

    <!-- Champ mot de passe avec œil à l'intérieur -->
    <div class="relative">
        <input type="password" name="password" id="password" required
            placeholder="Entrer votre mot de passe"
            class="w-full border rounded-lg px-4 py-2 pr-10 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">

        <!-- Bouton de l’œil à droite dans l'input -->
        <button type="button" id="togglePassword"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-green-600 focus:outline-none">
            
            <!-- Icône œil fermé 
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 001.523-.423M9.88 9.88A4.992 4.992 0 0112 9c2.761 0 5 2.239 5 5a4.992 4.992 0 01-.88 2.12M15 15l3 3M9.88 9.88L7 7" />
            </svg>-->

            <!-- Icône œil ouvert (cachée au départ) -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                class="w-5 h-5 hidden">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    toggleButton.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', !isPassword);
        eyeClosed.classList.toggle('hidden', isPassword);
    });
</script>


            <button type="submit" id="loginBtn"
                class="w-full bg-green-700 text-white py-2.5 md:py-3 rounded-lg font-bold shadow-md hover:bg-green-800 transition transform hover:scale-[1.01] flex justify-center items-center">
                <i id="spinner" class="fas fa-spinner fa-spin mr-2 hidden"></i>
                <span id="btnText">Se connecter</span>
            </button>
        </form>

        {{-- Liens bas --}}
        <div class="mt-5 text-center space-y-2 md:space-y-0 md:space-x-4 border-t pt-4">
            <a href="{{ route('register') }}" class="block md:inline text-sm text-green-700 hover:text-green-800 font-medium underline">Créer un compte</a>
            <span class="hidden md:inline text-gray-400">|</span>
            <a href="{{ route('password.request') }}" class="block md:inline text-sm text-green-700 hover:text-green-800 font-medium underline">Mot de passe oublié ?</a>
        </div>
    </div>
</div>

{{-- === TOASTR JS + CSS === --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    /* Toastr centré et professionnel */
    #toast-container {
        position: fixed !important;
        top: 30% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 70%;
        max-width: 350px;
        z-index: 999999;
        text-align: center;
    }

    .toast {
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
        padding: 14px 18px !important;
        font-size: 15px !important;
    }

    .toast-success {
        background-color: #16a34a !important;
    }

    .toast-error {
        background-color: #dc2626 !important;
    }

    .toast-title {
        font-weight: 700 !important;
    }

    .toast-message {
        font-weight: 500 !important;
    }
</style>

{{-- === SCRIPT LOGIN AJAX AVEC TOASTR CENTRÉ === --}}
<script>
$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        $('#spinner').removeClass('hidden');
        $('#btnText').text('Connexion...');
        $('#loginBtn').attr('disabled', true);

        $.ajax({
            url: "{{ route('login') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    toastr.success('Connexion réussie 🎉<br>Bienvenue sur ERES-TOGO !', 'Succès');
                    setTimeout(() => {
                        window.location.href = "/formulaire";
                    }, 2000);
                } else {
                        toastr.error(response.message || 'Erreur de connexion', 'Erreur');
                }
            },
            error: function (xhr) {
                let message = 'Une erreur est survenue. Veuillez réessayer.';
                if (xhr.status === 401) {
                    message = 'Email ou mot de passe incorrect ❌<br>Veuillez réessayer.';
                }
                toastr.error(message, 'Erreur');
            },
            complete: function () {
                $('#spinner').addClass('hidden');
                $('#btnText').text('Se connecter');
                $('#loginBtn').attr('disabled', false);
            }
        });
    });
});
</script>

@endsection
