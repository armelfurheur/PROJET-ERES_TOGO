<nav class="top-nav">
    <div class="nav-title">
        <button class="btn-icon" id="sidebarToggle" title="Plier le sidebar">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h2>Espace Responsable HSE</h2>
    </div>

    <div class="nav-actions">
        <!-- Notifications -->
        <button class="btn-icon" id="notificationBtn" title="Notifications">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
            </svg>
            <span class="notification-badge" id="topNotificationBadge" style="display:none;">0</span>
        </button>

        <!-- Theme toggle -->
        <button class="btn-icon" id="themeToggle" title="Changer le thème">
            <svg id="sunIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
            <svg id="moonIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>

        <!-- User profile -->
        <div class="user-profile-dropdown">
            <div class="user-badge" id="userMenuBtn">
                <span id="userInitials">{{ strtoupper(substr(Auth::user()->name ?? 'U',0,2)) }}</span>
            </div>
            <div class="user-dropdown-menu" id="userDropdown">
                <div class="user-dropdown-info">
                    <div id="dropdownUserName">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                    <div id="dropdownUserEmail">{{ Auth::user()->email ?? 'email@example.com' }}</div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
                <button class="btn btn-sm btn-danger w-full" id="logoutBtn">🚪 Déconnexion</button>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;

    // ===== SIDEBAR =====
    const sidebarToggle = document.getElementById('sidebarToggle');
    if(localStorage.getItem('sidebarState') === 'collapsed') body.classList.add('sidebar-collapsed');
    sidebarToggle.addEventListener('click', () => {
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarState', body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
        body.style.transition = 'all 0.3s ease';
        window.dispatchEvent(new CustomEvent('sidebarToggle', {detail:{collapsed: body.classList.contains('sidebar-collapsed')}}));
    });

    // ===== NOTIFICATIONS =====
    const notificationBadge = document.getElementById('topNotificationBadge');
    function loadNotifications() {
        fetch("{{ route('anomalies.today') }}")
            .then(res => res.json())
            .then(data => {
                const anomalies = data.anomalies || [];
                const totalAnomaliesToday = anomalies.length; // juste le nombre total d'anomalies aujourd'hui
                updateNotificationBadge(totalAnomaliesToday);
            })
            .catch(err => console.error("Erreur notifications:", err));
    }

    function updateNotificationBadge(count) {
        notificationBadge.textContent = count>99?'99+':count;
        notificationBadge.style.display = count>0?'flex':'none';
        if(count>0){
            notificationBadge.classList.add('pulse');
            setTimeout(()=>notificationBadge.classList.remove('pulse'),800);
        }
    }

   // ===== THEME TOGGLE - Version 2025 (Propre, robuste & respecte le thème système) =====
const themeToggle = document.getElementById('themeToggle');
const sunIcon = document.getElementById('sunIcon');
const moonIcon = document.getElementById('moonIcon');

// Fonction pour appliquer le thème
function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.body.classList.add('dark-mode');
        sunIcon.style.display = 'block';
        moonIcon.style.display = 'none';
    } else {
        document.documentElement.classList.remove('dark-mode');
        document.body.classList.remove('dark-mode');
        sunIcon.style.display = 'none';
        moonIcon.style.display = 'block';
    }
    localStorage.setItem('eres_theme', theme);
}

// Détection du thème sauvegardé OU du thème système
const savedTheme = localStorage.getItem('eres_theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme) {
    applyTheme(savedTheme);
} else if (prefersDark) {
    applyTheme('dark');
} else {
    applyTheme('light');
}

// Écoute du changement de thème système en temps réel
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    if (!localStorage.getItem('eres_theme')) { // seulement si l'utilisateur n'a pas choisi manuellement
        applyTheme(e.matches ? 'dark' : 'light');
    }
});

// Clique sur le bouton
themeToggle.addEventListener('click', () => {
    const isDark = document.documentElement.classList.contains('dark-mode');
    applyTheme(isDark ? 'light' : 'dark');
});


    // ===== USER PROFILE =====
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBtn = document.getElementById('logoutBtn');
    const currentUser = {name:"{{ Auth::user()->name ?? 'Utilisateur' }}", email:"{{ Auth::user()->email ?? 'email@example.com' }}"};

    function updateUserInterface(){
        const initials = currentUser.name.split(' ').map(n=>n[0]).join('').toUpperCase();
        document.getElementById('userInitials').textContent = initials;
        document.getElementById('dropdownUserName').textContent = currentUser.name;
        document.getElementById('dropdownUserEmail').textContent = currentUser.email;
        const welcomeTitle = document.getElementById('welcomeTitle');
        if(welcomeTitle) welcomeTitle.textContent = `Bienvenue, ${currentUser.name}`;
    }

    userMenuBtn.addEventListener('click',e=>{e.stopPropagation(); userDropdown.classList.toggle('show');});
    document.addEventListener('click',()=>userDropdown.classList.remove('show'));
    logoutBtn.addEventListener('click',()=>document.getElementById('logout-form').submit());

    // ===== INITIALIZE =====
    function initializeNavbar(){updateUserInterface(); loadNotifications(); setInterval(loadNotifications,30000);}
    initializeNavbar();

    // ===== TEST FUNCTION (simulate new anomaly) =====
    window.simulateNewAnomaly = function(){
        let currentCount = parseInt(notificationBadge.textContent)||0;
        currentCount++;
        updateNotificationBadge(currentCount);
    }
});
</script>

<style>
/* Notifications */
.notification-badge{position:absolute;top:-5px;right:-5px;background:#ff4444;color:white;border-radius:50%;width:18px;height:18px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:bold;}
.pulse{animation:pulse 0.8s ease-in-out;}
@keyframes pulse{0%{transform:scale(1);}50%{transform:scale(1.3);}100%{transform:scale(1);}}

/* User dropdown */
.user-profile-dropdown{position:relative;}
.user-dropdown-menu{position:absolute;top:100%;right:0;background:white;border:1px solid #ddd;border-radius:8px;padding:1rem;min-width:200px;box-shadow:0 4px 12px rgba(0,0,0,0.1);display:none;z-index:1000;}
.user-dropdown-menu.show{display:block;}
.user-dropdown-info{margin-bottom:1rem;text-align:center;}
.user-dropdown-info div:first-child{font-weight:600;margin-bottom:0.25rem;}
.user-dropdown-info div:last-child{font-size:0.875rem;color:#666;}

/* Sidebar collapsed */
.sidebar-collapsed .sidebar{width:60px;}
.sidebar-collapsed .sidebar .nav-item span{display:none;}
.sidebar-collapsed .sidebar .nav-item{justify-content:center;padding:0.75rem;}
.sidebar-collapsed .main-content{margin-left:60px;}
.sidebar-collapsed .sidebar .logo-text{display:none;}
.sidebar-collapsed .sidebar .nav-section{text-align:center;}
.sidebar-collapsed .sidebar .nav-section span{display:none;}
.sidebar,.main-content{transition:all 0.3s ease;}

/* Dark mode */
.dark-mode .user-dropdown-menu{background:#2d3748;border-color:#4a5568;color:white;}
.dark-mode .user-dropdown-info div:last-child{color:#cbd5e0;}

/* Notification button */
#notificationBtn{position:relative;}

/* Sidebar toggle active */
.sidebar-collapsed #sidebarToggle{background-color:rgba(0,123,255,0.1);}
</style>
