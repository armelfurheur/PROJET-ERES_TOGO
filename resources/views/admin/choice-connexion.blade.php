@extends('layouts.app')

@section('title', 'Choix d’accès | ERES-TOGO')

@section('content')
<div class="relative w-full min-h-screen flex items-center justify-center"
     style="background: url('{{ asset('img/admin-bg.jpg') }}') no-repeat center center / cover;">

    <!-- Overlay sombre pour lisibilité -->
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative bg-white/90 backdrop-blur-md p-12 rounded-3xl shadow-2xl max-w-lg text-center space-y-8 z-10">
        
        <!-- Titre -->
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800">
            Bonjour <span class="text-green-600">{{ auth()->user()->firstname }}</span> 👋
        </h2>
        <p class="text-gray-700 text-base md:text-lg">
            Vous êtes administrateur. Choisissez votre accès :
        </p>

        <!-- Boutons de choix -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center justify-center px-6 py-4 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 hover:scale-105 transition transform duration-300">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="{{ route('formulaire.anomalie') }}" 
               class="flex items-center justify-center px-6 py-4 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 hover:scale-105 transition transform duration-300">
                <i class="fas fa-file-alt mr-2"></i> Formulaire de remontée
            </a>
        </div>

        <!-- Note -->
    </div>
</div>

<!-- FontAwesome pour les icônes -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
    a:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    /* Animation légère des titres */
    h2 {
        animation: fadeInUp 1s ease forwards;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
