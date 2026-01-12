@extends('layouts.app')

@section('title', 'Connexion | ERES-TOGO')

@section('content')
<div class="relative w-full min-h-screen flex items-center justify-center overflow-hidden"
     style="background: url('{{ asset('img/depot.jpg') }}') no-repeat center center / cover;">

    <!-- Overlay sombre pour lisibilité -->
    <div class="absolute inset-0 bg-blue-900 bg-opacity-60"></div>

    <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-6 border border-gray-200 z-10">
        <div class="text-center mb-6">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" class="h-14 w-auto mx-auto mb-3">

            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                Se Connecter à <span class="highlight pulse">ERESriskAlert</span>
            </h2>
        </div>

        {{-- Formulaire --}}
        <form id="loginForm" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">
                    Adresse E-mail :*
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="Entrer votre Email"
                    class="w-full border rounded-lg px-4 py-2 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
            </div>

            <div class="relative">
                <label for="password" class="block mb-1 font-semibold text-gray-700 text-sm md:text-base">
                    Mot de passe :*
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        placeholder="Entrer votre mot de passe"
                        class="w-full border rounded-lg px-4 py-2 pr-10 text-sm md:text-base focus:ring-green-500 focus:border-green-500 transition duration-150">
                    <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-green-600 focus:outline-none"
                            data-target="password">
                        <svg class="eye-open hidden w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg class="eye-closed w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 001.523-.423M9.88 9.88A4.992 4.992 0 0112 9c2.761 0 5 2.239 5 5a4.992 4.992 0 01-.88 2.12M15 15l3 3M9.88 9.88L7 7" />
                        </svg>
                    </button>
                </div>
            </div>

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

{{-- === CSS supplémentaire pour fond et highlight === --}}
<style>
    html, body {
        width: 100%;
        height: 100%;
    }

    .highlight {
        background: linear-gradient(90deg, #07411cff, #22c55e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        font-size: 1.8rem;
        display: inline-block;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .highlight:hover {
        transform: scale(1.1) rotate(-2deg);
        filter: drop-shadow(0 4px 8px rgba(34, 197, 94, 0.6));
        cursor: pointer;
    }

    .highlight.pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>

{{-- === JS Login / Toggle Password === --}}
<script>
$(document).ready(function () {
    $('.toggle-password').on('click', function () {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const eyeOpen = $(this).find('.eye-open');
        const eyeClosed = $(this).find('.eye-closed');

        const isPassword = input.attr('type') === 'password';
        input.attr('type', isPassword ? 'text' : 'password');
        eyeOpen.toggleClass('hidden', !isPassword);
        eyeClosed.toggleClass('hidden', isPassword);
    });

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
                    toastr.success('Connexion réussie 🎉', 'Succès');
                    setTimeout(() => window.location.href = response.redirect, 2000);
                } else {
                    toastr.error(response.message || 'Erreur de connexion', 'Erreur');
                }
            },
            error: function (xhr) {
                let message = 'Une erreur est survenue. Veuillez réessayer.';
                if (xhr.status === 401) {
                    message = 'Email ou mot de passe incorrect ❌';
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
