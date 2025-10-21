<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERES-TOGO')</title>
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Include jQuery first -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<!-- Toastr CSS -->
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>

<!-- Toastr JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
{{-- CHANGEMENT 1: Ajout de 'flex flex-col' pour permettre à 'main' de prendre tout l'espace disponible --}}
<body class="bg-gray-100 min-h-screen flex flex-col">
    
    {{-- La navigation est fixée, cela ne change pas --}}
    <nav class="bg-green-900 shadow p-4 flex justify-between items-center fixed w-full top-0 left-0 z-50">
        
        {{-- ⭐ Bloc Logo ERES-TOGO (STATIC, NON-CLIQUABLE, IMAGE) ⭐ --}}
        <div class="flex items-center space-x-2">
            {{-- VEUILLEZ REMPLACER L'URL DE CETTE IMAGE (src) PAR L'URL DE VOTRE LOGO --}}
          <!-- Fichier : resources/views/layout.blade.php -->

<img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" class="h-10 w-auto">
            {{-- Retire le texte "ERES-TOGO" --}}
        </div>
        
        {{-- Bloc d'authentification --}}
        <div>
            @auth
                <span class="align-middle flex items-center">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <span class="ml-2 text-white">bienvenue</span>
                    <span class="ml-1 font-semibold text-yellow-300">{{ Auth::user()->name }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="ml-4 inline">
                    @csrf
                    <button type="submit" class="text-yellow-300 underline font-semibold hover:text-yellow-500">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mr-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-700 hover:bg-green-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                </a>
                <a href="{{ route('register') }}" class="text-yellow-300 hover:underline">Inscription</a>
            @endauth
        </div>
    </nav>
    
    {{-- CHANGEMENT 2: Ajout de 'flex-grow' pour que le contenu principal pousse le footer vers le bas --}}
    <main class="container mx-auto mt-24 flex-grow">
        @yield('content')
    </main>

    {{-- CHANGEMENT 3: Ajout du Pied de Page Uniforme --}}
    <footer class="bg-green-900 text-white py-6 mt-auto">
        <div class="container mx-auto px-2 text-center md:flex md:justify-around md:items-start">
            
        <!-- Ligne de Copyright en bas -->
        <div class="">
            &copy; {{ date('Y') }} ERES-TOGO. Tous droits réservés.
        </div>
    </footer>
    {{-- FIN du Pied de Page --}}
</body>
</html>
 