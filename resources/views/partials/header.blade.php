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
        <button class="btn-icon" id="notificationBtn" title="Notifications">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            <span class="notification-badge" id="topNotificationBadge" style="display:none;">0</span>
        </button>
        <button class="btn-icon" id="themeToggle" title="Changer le thème">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
        </button>
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
    
        // ========== USER MANAGEMENT ==========
        // Simulation de la connexion d'un utilisateur
      // Récupérer les données de l'utilisateur connecté depuis Laravel
    let currentUser = {
        name: "{{ $user->name ?? 'Utilisateur Inconnu' }}",
        email: "{{ $user->email ?? 'email@exemple.com' }}"
    };
        function loadUserData() {
            updateUserInterface();
            updateWelcomeMessage();
        }

        function updateUserInterface() {
            const initials = currentUser.name.split(' ').map(n => n[0]).join('').toUpperCase();
            document.getElementById('userInitials').textContent = initials;
            document.getElementById('dropdownUserName').textContent = currentUser.name;
            document.getElementById('dropdownUserEmail').textContent = currentUser.email;
        }

        function updateWelcomeMessage() {
            const welcomeTitle = document.getElementById('welcomeTitle');
            welcomeTitle.textContent = `Bienvenue, ${currentUser.name}`;
        }

        // ========== THEME TOGGLE ==========
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        const savedTheme = localStorage.getItem('eres_theme') || 'light';
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
        }

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('eres_theme', currentTheme);
            renderCurrentView();
        });

        // ========== SIDEBAR TOGGLE ==========
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        // ========== STORE MANAGEMENT ==========
        const store = {
            anomaliesKey: 'eres_anomalies_v3',
            proposalsKey: 'eres_proposals_v3',
            paramsKey: 'eres_params_v3',
            load() {
                const anomalies = JSON.parse(localStorage.getItem(this.anomaliesKey) || '[]');
                const proposals = JSON.parse(localStorage.getItem(this.proposalsKey) || '[]');
                const params = JSON.parse(localStorage.getItem(this.paramsKey) || '{}');
                return { anomalies, proposals, params };
            },
            saveAnomalies(list) { localStorage.setItem(this.anomaliesKey, JSON.stringify(list)); },
            saveProposals(list) { localStorage.setItem(this.proposalsKey, JSON.stringify(list)); },
            saveParams(obj) { localStorage.setItem(this.paramsKey, JSON.stringify(obj)); }
        };

        let { anomalies, proposals, params } = store.load();

        // Paramètres par défaut
        if (!params.email) {
            params = {
                email: 'hse@eres-togo.com',
                email_cc: 'direction@eres-togo.com',
                notify_email: true,
                notify_sound: true,
                auto_archive: true
            };
            store.saveParams(params);
        }

      
        // ========== TRASH SYSTEM ==========
        const trashKey = 'eres_trash_v3';

        function getTrash() {
            return JSON.parse(localStorage.getItem(trashKey) || '[]');
        }

        function saveTrash(trash) {
            localStorage.setItem(trashKey, JSON.stringify(trash));
        }

        function addToTrash(item, type, originalData = null) {
            const trash = getTrash();
            const trashItem = {
                id: generateId('trash'),
                type: type,
                data: item,
                originalData: originalData,
                deletedAt: new Date().toISOString(),
                deletedBy: currentUser?.name || 'System'
            };
            trash.push(trashItem);
            saveTrash(trash);
            return trashItem;
        }

        function restoreFromTrash(trashId) {
            const trash = getTrash();
            const itemIndex = trash.findIndex(item => item.id === trashId);
            if (itemIndex === -1) return false;
            
            const trashItem = trash[itemIndex];
            
            if (trashItem.type === 'anomaly') {
                anomalies.push(trashItem.data);
                store.saveAnomalies(anomalies);
            } else if (trashItem.type === 'proposal') {
                const anomaly = anomalies.find(a => a.id === trashItem.originalData.anomalyId);
                if (anomaly) {
                    if (!anomaly.proposals) anomaly.proposals = [];
                    anomaly.proposals.push(trashItem.data);
                    store.saveAnomalies(anomalies);
                }
            }
            
            trash.splice(itemIndex, 1);
            saveTrash(trash);
            return true;
        }

        function emptyTrash() {
            if (confirm('⚠️ Vider définitivement la corbeille ? Cette action est irréversible.')) {
                localStorage.removeItem(trashKey);
                return true;
            }
            return false;
        }

        // Fonction pour nettoyer automatiquement la corbeille après 1 mois
        function autoCleanTrash() {
            const trash = getTrash();
            if (trash.length === 0) return;
            
            const oneMonthAgo = new Date();
            oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
            
            const remainingTrash = trash.filter(item => {
                const deletedDate = new Date(item.deletedAt);
                return deletedDate > oneMonthAgo;
            });
            
            if (remainingTrash.length !== trash.length) {
                saveTrash(remainingTrash);
                console.log(`Corbeille nettoyée automatiquement. ${trash.length - remainingTrash.length} éléments supprimés.`);
            }
        }

</script>