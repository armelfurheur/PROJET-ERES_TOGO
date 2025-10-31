<nav class="top-nav">
    <div class="nav-title">
        <button class="btn-icon" id="sidebarToggle" title="Plier le sidebar">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h2>Espace Responsable HSE</h2>
    </div>
    <div class="nav-actions">
        <!-- Notifications avec badge pour nouvelles anomalies -->
        <button class="btn-icon" id="notificationBtn" title="Notifications">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            <span class="notification-badge" id="topNotificationBadge" style="display:none;">0</span>
        </button>
        
        <!-- Bouton changement de thème avec icône dynamique -->
        <button class="btn-icon" id="themeToggle" title="Changer le thème">
            <!-- Icône soleil (mode clair) -->
            <svg id="sunIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
            <!-- Icône lune (mode sombre) -->
            <svg id="moonIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>
        
        <!-- Profil utilisateur -->
        <div class="user-profile-dropdown">
            <div class="user-badge" id="userMenuBtn">
                <span id="userInitials">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>
            </div>
            <div class="user-dropdown-menu" id="userDropdown">
                <div class="user-dropdown-info">
                    <div id="dropdownUserName">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                    <div id="dropdownUserEmail">{{ Auth::user()->email ?? 'email@example.com' }}</div>
                </div>
                <button class="btn btn-sm btn-secondary w-full" id="refreshBtn">🔄 Actualiser</button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>  
                <button class="btn btn-sm btn-danger w-full" id="logoutBtn">🚪 Déconnexion</button>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== TOGGLE SIDEBAR ==========
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;

    // Vérifier l'état sauvegardé du sidebar
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        body.classList.add('sidebar-collapsed');
    }

    sidebarToggle.addEventListener('click', function() {
        body.classList.toggle('sidebar-collapsed');
        
        // Sauvegarder l'état
        const isCollapsed = body.classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
        
        // Ajouter une transition fluide
        body.style.transition = 'all 0.3s ease';
        
        // Dispatcher un événement personnalisé pour informer autres composants
        window.dispatchEvent(new CustomEvent('sidebarToggle', {
            detail: { collapsed: isCollapsed }
        }));
    });

    // ========== GESTION DES NOTIFICATIONS ==========
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationBadge = document.getElementById('topNotificationBadge');
    let unreadAnomalies = 0;

    // Fonction pour charger les notifications (nouvelles anomalies)
    function loadNotifications() {
        fetch("{{ route('anomalies.list') }}")
            .then(response => response.json())
            .then(data => {
                const anomalies = data.anomalies || [];
                
                // Compter les anomalies non lues (exemple: statut "Nouvelle" ou date récente)
                unreadAnomalies = anomalies.filter(anomaly => {
                    // Critère: anomalies des dernières 24 heures ou statut spécifique
                    const anomalyDate = new Date(anomaly.datetime);
                    const now = new Date();
                    const hoursDiff = (now - anomalyDate) / (1000 * 60 * 60);
                    
                    return hoursDiff < 24 || anomaly.statut === 'Ouverte'; // Adaptez selon vos besoins
                }).length;

                updateNotificationBadge();
            })
            .catch(err => console.error("Erreur chargement notifications:", err));
    }

    // Mettre à jour le badge de notification
    function updateNotificationBadge() {
        if (unreadAnomalies > 0) {
            notificationBadge.textContent = unreadAnomalies > 99 ? '99+' : unreadAnomalies;
            notificationBadge.style.display = 'flex';
            
            // Ajouter une animation pour attirer l'attention
            notificationBadge.classList.add('pulse');
            setTimeout(() => notificationBadge.classList.remove('pulse'), 1000);
        } else {
            notificationBadge.style.display = 'none';
        }
    }

    // Clic sur la notification
    notificationBtn.addEventListener('click', function() {
        if (unreadAnomalies > 0) {
            // Rediriger vers la vue anomalies
            document.getElementById('view-anomalies').classList.remove('hidden');
            document.getElementById('view-proposals').classList.add('hidden');
            
            // Réinitialiser le compteur (marquer comme lues)
            unreadAnomalies = 0;
            updateNotificationBadge();
            
            // Optionnel: Appeler une API pour marquer comme lues
            // fetch("/api/notifications/mark-as-read", { method: 'POST' });
        }
    });

    // ========== GESTION DU THÈME ==========
    const themeToggle = document.getElementById('themeToggle');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');

    // Charger le thème sauvegardé
    const savedTheme = localStorage.getItem('eres_theme') || 'light';
    
    // Appliquer le thème au chargement
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        sunIcon.style.display = 'block';
        moonIcon.style.display = 'none';
    } else {
        sunIcon.style.display = 'none';
        moonIcon.style.display = 'block';
    }

    // Changer le thème
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
        
        // Changer les icônes
        if (currentTheme === 'dark') {
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
        } else {
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
        }
        
        // Sauvegarder le thème
        localStorage.setItem('eres_theme', currentTheme);
        
        // Re-rendre la vue si nécessaire
        if (typeof renderCurrentView === 'function') {
            renderCurrentView();
        }
    });

    // ========== GESTION UTILISATEUR ==========
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBtn = document.getElementById('logoutBtn');
    const refreshBtn = document.getElementById('refreshBtn');

    // Récupérer les données de l'utilisateur connecté depuis Laravel
    const currentUser = {
        name: "{{ Auth::user()->name ?? 'Utilisateur' }}",
        email: "{{ Auth::user()->email ?? 'email@example.com' }}"
    };

    // Mettre à jour l'interface utilisateur
    function updateUserInterface() {
        const initials = currentUser.name.split(' ').map(n => n[0]).join('').toUpperCase();
        document.getElementById('userInitials').textContent = initials;
        document.getElementById('dropdownUserName').textContent = currentUser.name;
        document.getElementById('dropdownUserEmail').textContent = currentUser.email;
        
        // Mettre à jour le message de bienvenue si l'élément existe
        const welcomeTitle = document.getElementById('welcomeTitle');
        if (welcomeTitle) {
            welcomeTitle.textContent = `Bienvenue, ${currentUser.name}`;
        }
    }

    // Menu déroulant utilisateur
    userMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });

    // Fermer le menu déroulant en cliquant ailleurs
    document.addEventListener('click', function() {
        userDropdown.classList.remove('show');
    });

    // Bouton actualiser
    refreshBtn.addEventListener('click', function() {
        location.reload();
    });

    // Bouton déconnexion
    logoutBtn.addEventListener('click', function() {
        document.getElementById('logout-form').submit();
    });

    // ========== INITIALISATION ==========
    function initializeNavbar() {
        updateUserInterface();
        loadNotifications();
        
        // Recharger les notifications périodiquement
        setInterval(loadNotifications, 30000); // Toutes les 30 secondes
    }

    // Démarrer l'initialisation
    initializeNavbar();
});

// Fonction pour simuler une nouvelle anomalie (pour test)
function simulateNewAnomaly() {
    const notificationBadge = document.getElementById('topNotificationBadge');
    let currentCount = parseInt(notificationBadge.textContent) || 0;
    currentCount++;
    
    notificationBadge.textContent = currentCount > 99 ? '99+' : currentCount;
    notificationBadge.style.display = 'flex';
    notificationBadge.classList.add('pulse');
    
    setTimeout(() => notificationBadge.classList.remove('pulse'), 1000);
}
</script>

<style>
/* Styles pour les notifications */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.pulse {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Styles pour le menu utilisateur */
.user-profile-dropdown {
    position: relative;
}

.user-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    min-width: 200px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: none;
    z-index: 1000;
}

.user-dropdown-menu.show {
    display: block;
}

.user-dropdown-info {
    margin-bottom: 1rem;
    text-align: center;
}

.user-dropdown-info div:first-child {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.user-dropdown-info div:last-child {
    font-size: 0.875rem;
    color: #666;
}

/* Styles pour le sidebar replié */
.sidebar-collapsed .sidebar {
    width: 60px;
}

.sidebar-collapsed .sidebar .nav-item span {
    display: none;
}

.sidebar-collapsed .sidebar .nav-item {
    justify-content: center;
    padding: 0.75rem;
}

.sidebar-collapsed .main-content {
    margin-left: 60px;
}

.sidebar-collapsed .sidebar .logo-text {
    display: none;
}

.sidebar-collapsed .sidebar .nav-section {
    text-align: center;
}

.sidebar-collapsed .sidebar .nav-section span {
    display: none;
}

/* Transition fluide pour le sidebar */
.sidebar, .main-content {
    transition: all 0.3s ease;
}

/* Styles mode sombre */
.dark-mode .user-dropdown-menu {
    background: #2d3748;
    border-color: #4a5568;
    color: white;
}

.dark-mode .user-dropdown-info div:last-child {
    color: #cbd5e0;
}

/* Position relative pour le bouton de notification */
#notificationBtn {
    position: relative;
}

/* Style pour le bouton sidebar toggle quand il est actif */
.sidebar-collapsed #sidebarToggle {
    background-color: rgba(0, 123, 255, 0.1);
}
</style>