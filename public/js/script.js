
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
