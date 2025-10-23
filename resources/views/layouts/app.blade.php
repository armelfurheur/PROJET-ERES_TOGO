<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ERES-TOGO')</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- jQuery (une seule inclusion, version 3.7.1) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Toastr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
    <!-- Toastr JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    
    <nav class="bg-green-900 shadow p-4 flex justify-between items-center fixed w-full top-0 left-0 z-50">
        <!-- Logo ERES-TOGO -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo de l'application" class="h-10 w-auto">
        </div>
        
        <!-- Bloc d'authentification -->
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
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="ml-4 inline">
                    @csrf
                    <button type="submit" id="logoutBtn" class="text-sm text-yellow-300 hover:text-yellow-400 font-medium underline">
                        Déconnexion
                    </button>
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
    
    <main class="container mx-auto mt-24 flex-grow">
        @yield('content')
    </main>

    <footer class="bg-green-900 text-white py-6 mt-auto">
        <div class="container mx-auto px-2 text-center md:flex md:justify-around md:items-start">
            <div>
                &copy; {{ date('Y') }} ERES-TOGO. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Script pour gérer la déconnexion via AJAX -->
    <script>
        $(document).ready(function () {
            // Configurer le jeton CSRF pour toutes les requêtes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#logoutForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message, 'Succès', { timeOut: 1500 });
                            setTimeout(function () {
                                window.location.href = response.redirect || '/login';
                            }, 1500);
                        } else {
                            toastr.error(response.message || 'Une erreur inconnue est survenue.', 'Erreur');
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Erreur : Une erreur est survenue lors de la déconnexion.';
                        if (xhr.status === 419) {
                            errorMessage = 'Erreur : La session a expiré. Veuillez recharger la page et réessayer.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage, 'Erreur');
                    }
                });
            });
        });
    </script>
</body>
</html>