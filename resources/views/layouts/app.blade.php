<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ERESriskAlert | ERES-TOGO')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Toastr -->
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

    <!-- ===== STYLE DANGER / FEU ===== -->
    <style>
        .risk-fire {
            display: inline-block;
            font-size: 1.145rem; /* text-lg */
            font-weight: 800;
            letter-spacing: 0.05em;

            background: linear-gradient(
                90deg,
                #ff0000,
                #ff6a00,
                #ffcc00,
                #ff6a00,
                #ff0000
            );
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            animation: fireMove 2.8s linear infinite;

            text-shadow:
                0 0 6px rgba(255, 90, 0, 0.7),
                0 0 12px rgba(255, 120, 0, 0.6),
                0 0 20px rgba(255, 0, 0, 0.5);
        }

        @keyframes fireMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- ================= NAVBAR ================= -->
<nav class="bg-green-900 shadow fixed w-full top-0 left-0 z-50">
    <div class="flex justify-between items-center px-6 py-4">

        <!-- ===== LOGO + APP NAME ===== -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="h-10 w-auto rounded-sm">

            <span class="text-green-300 text-xl font-light">|</span>

            <!-- ===== PLATEFORME ===== -->
            <div class="leading-tight flex items-center space-x-2">

                <!-- Icône Alerte -->
                <span class="flex items-center justify-center w-7 h-7 rounded-full bg-yellow-400 animate-pulse">
                    <i class="fas fa-triangle-exclamation text-green-900 text-sm"></i>
                </span>

                <!-- Texte -->
                <div>
                    <span class="block text-xs uppercase tracking-widest text-green-200">
                        Plateforme
                    </span>
                    <span class="block risk-fire">
                        ERESriskAlert
                    </span>
                </div>
            </div>
        </div>

        <!-- ===== AUTH ===== -->
        <div>
            @auth
                <div class="flex items-center space-x-4">

                    <!-- Avatar -->
                    <div class="flex items-center text-white">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-6 w-6 text-white"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 15
                                         c2.5 0 4.847.655 6.879 1.804M15 11
                                         a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>

                        <span class="ml-2 text-sm">
                            Bienvenue,
                            <span class="font-semibold text-yellow-300">
                                {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                            </span>
                        </span>
                    </div>

                    <!-- Logout -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <a href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="px-4 py-2 rounded-md font-semibold text-yellow-300 hover:bg-green-800 transition">
                        Déconnexion
                    </a>
                </div>
            @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('register') }}" class="flex items-center text-yellow-300 hover:underline">
                        <img src="{{ asset('img/8e2704d5c0038bae80b51ebf747e7bad.jpg') }}"
                             class="w-9 h-9 mr-2 object-contain mix-blend-multiply"
                             alt="user icon">
                        Inscription
                    </a>
                </div>
            @endauth
        </div>

    </div>
</nav>
<!-- ================= END NAVBAR ================= -->

<!-- ================= CONTENT ================= -->
<main class="container mx-auto mt-28 px-4 flex-grow">
    @yield('content')
</main>

<!-- ================= FOOTER ================= -->
<footer class="bg-green-900 text-white py-6 mt-auto">
    <div class="container mx-auto px-2 text-center">
        &copy; {{ date('Y') }} <strong>ERESriskAlert</strong> – ERES-TOGO. Tous droits réservés.
    </div>
</footer>

<!-- ================= AJAX SETUP ================= -->
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>

</body>
</html>
