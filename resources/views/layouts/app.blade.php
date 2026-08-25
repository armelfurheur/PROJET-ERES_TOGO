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

    <style>
        :root{
            --ra-navy:#0a2540;
            --ra-green-900:#14532d;
            --ra-green-800:#166534;
        }

        .ra-brand{
            font-size:1.05rem;
            font-weight:700;
            letter-spacing:.01em;
            color:#fef9c3;
        }

        .ra-badge{
            display:flex;
            align-items:center;
            justify-content:center;
            width:1.75rem;
            height:1.75rem;
            border-radius:9999px;
            background:#fbbf24;
            color:var(--ra-green-900);
            flex-shrink:0;
        }

        .ra-user-menu{
            min-width:14rem;
        }

        .ra-user-menu a,
        .ra-user-menu button{
            width:100%;
        }


        /* Coach mark déconnexion */
.ra-coachmark{
    position:absolute;
    right:0;
    top:3rem;
    width:230px;
    background:#78350f;
    color:#fef3c7;
    font-size:.8rem;
    line-height:1.35;
    padding:.85rem 1rem;
    border-radius:.75rem;
    box-shadow:0 10px 25px rgba(0,0,0,.3);
    z-index:60;
    animation:ra-coach-in .25s ease-out;
}
.ra-coachmark::before{
    content:"";
    position:absolute;
    top:-6px;
    right:14px;
    width:12px;
    height:12px;
    background:inherit;
    transform:rotate(45deg);
}
.ra-coach-close{
    position:absolute;
    top:4px;
    right:8px;
    cursor:pointer;
    opacity:.7;
    font-size:1rem;
    line-height:1;
}
.ra-coach-close:hover{ opacity:1; }

@keyframes ra-coach-in{
    from{ opacity:0; transform:translateY(-6px); }
    to{ opacity:1; transform:translateY(0); }
}
@keyframes ra-pulse-ring{
    0%{ box-shadow:0 0 0 0 rgba(251,191,36,.7); }
    70%{ box-shadow:0 0 0 10px rgba(251,191,36,0); }
    100%{ box-shadow:0 0 0 0 rgba(251,191,36,0); }
}
.ra-pulse{
    animation:ra-pulse-ring 1.4s ease-out infinite;
}
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- ================= NAVBAR ================= -->
<nav class="bg-green-900 shadow fixed w-full top-0 left-0 z-50">
    <div class="flex justify-between items-center px-6 py-3">

        <!-- ===== LOGO + APP NAME ===== -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="h-9 w-auto rounded-sm">

            <span class="text-green-700 text-xl font-light">|</span>

            <div class="flex items-center space-x-2">
                <span class="ra-badge">
                    <i class="fas fa-triangle-exclamation text-sm"></i>
                </span>

                <div class="leading-tight">
                    <span class="block text-xs uppercase tracking-widest text-green-300">
                        Plateforme
                    </span>
                    <span class="ra-brand">
                        ERESriskAlert
                    </span>
                </div>
            </div>
        </div>

        <!-- ===== AUTH ===== -->
        <div>
            @auth
                <div class="flex items-center space-x-2">

                    <div class="relative">

                        <!-- Avatar -->
                        <button onclick="toggleUserMenu()" id="avatarBtn"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-800 hover:bg-green-700 transition focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 text-white"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 15
                                         c2.5 0 4.847.655 6.879 1.804M15 11
                                         a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>

                        <!-- Menu utilisateur -->
                        <div id="userMenu"
                             class="ra-user-menu hidden absolute right-0 mt-3 bg-white rounded-lg shadow-lg border border-gray-100 py-2 text-gray-700">

                            <div class="px-4 py-2 text-sm border-b border-gray-100">
                                <span class="block text-xs text-gray-400">Connecté en tant que</span>
                                <span class="font-semibold text-gray-800">
                                    {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                                </span>
                            </div>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V4"/>
                                    </svg>
                                    Déconnexion
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('register') }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded-md text-yellow-300 font-medium hover:bg-green-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-4a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Inscription
                    </a>
                </div>
            @endauth
        </div>

    </div>
</nav>
<!-- ================= END NAVBAR ================= -->

<!-- ================= CONTENT ================= -->
<main class="container mx-auto mt-24 px-4 flex-grow">
    @yield('content')
</main>

<!-- ================= FOOTER ================= -->
<footer class="bg-green-900 text-white py-6 mt-auto">
    <div class="container mx-auto px-2 text-center text-sm text-green-100">
        &copy; {{ date('Y') }} <strong class="text-white">ERESriskAlert</strong> &ndash; ERES-TOGO. Tous droits réservés.
    </div>
</footer>

<!-- ================= SCRIPTS ================= -->
<script>
function toggleUserMenu() {
    const menu = document.getElementById("userMenu");
    if (!menu) return;
    menu.classList.toggle("hidden");
    hideLogoutCoachMark(); // ferme le coach mark si l'utilisateur ouvre le menu lui-même
}

document.addEventListener('click', function (event) {
    const menu = document.getElementById('userMenu');
    if (!menu || menu.classList.contains('hidden')) return;

    const isClickInsideMenu = menu.contains(event.target);
    const isToggleButton = event.target.closest('button[onclick="toggleUserMenu()"]');

    if (!isClickInsideMenu && !isToggleButton) {
        menu.classList.add('hidden');
    }
});

// ===== Coach mark "pensez à vous déconnecter" =====
window.showLogoutCoachMark = function (message) {
    const avatarBtn = document.getElementById('avatarBtn');
    if (!avatarBtn) return;

    const existing = document.getElementById('logoutCoachMark');
    if (existing) existing.remove();

    avatarBtn.classList.add('ra-pulse');

    const coach = document.createElement('div');
    coach.id = 'logoutCoachMark';
    coach.className = 'ra-coachmark';
    coach.innerHTML = `
        <span class="ra-coach-close" onclick="hideLogoutCoachMark()">&times;</span>
        <strong class="block mb-1">✅ Anomalie envoyée</strong>
        ${message || "Pensez à vous déconnecter si vous avez terminé."}
    `;

    avatarBtn.parentElement.appendChild(coach);

    clearTimeout(window._raCoachTimeout);
    window._raCoachTimeout = setTimeout(hideLogoutCoachMark, 10000);
};

window.hideLogoutCoachMark = function () {
    const coach = document.getElementById('logoutCoachMark');
    if (coach) coach.remove();
    const avatarBtn = document.getElementById('avatarBtn');
    if (avatarBtn) avatarBtn.classList.remove('ra-pulse');
};

document.addEventListener('click', function (event) {
    const coach = document.getElementById('logoutCoachMark');
    if (!coach) return;
    const avatarBtn = document.getElementById('avatarBtn');
    const isClickInside = coach.contains(event.target) || (avatarBtn && avatarBtn.contains(event.target));
    if (!isClickInside) {
        hideLogoutCoachMark();
    }
});

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