// ========== USER MANAGEMENT ==========
// Simulation de la connexion d'un utilisateur
let currentUser = {
    name: "Koffi Mensah", // Nom du responsable HSE connecté
    email: "k.mensah@eres-togo.com"
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

// Données d'exemple
if (anomalies.length === 0) {
    const now = new Date();
    anomalies = [
        {
            id: generateId('anom'),
            rapporte_par: 'Jean Dupont',
            departement: 'technique',
            localisation: 'Zone A - Entrée',
            statut_anomalie: 'arret',
            description: 'Fuite d\'huile importante. Risque de glissade élevé.',
            action: 'Zone balisée. Maintenance alertée.',
            preuve_url: '',
            datetime: new Date(now.getFullYear(), now.getMonth(), now.getDate() - 2, 10, 30).toISOString(),
            status: 'Ouverte',
            read: false,
            created_at: new Date(now.getFullYear(), now.getMonth(), now.getDate() - 2, 10, 30).toISOString(),
            proposals: []
        },
        // ... autres données d'exemple
    ];
    store.saveAnomalies(anomalies);
}

// ========== TRASH SYSTEM ==========
const trashKey = 'eres_trash_v3';

function getTrash() {
    return JSON.parse(localStorage.getItem(trashKey) || '[]');
}

function saveTrash(trash) {
    localStorage.setItem(trashKey, JSON.stringify(trash));
}

// ... Le reste du code JavaScript reste identique mais organisé dans des fonctions modulaires

// ========== INIT ==========
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    setMinDateForProposals();
    renderDashboard();
    updateNotifications();
    
    // Nettoyage automatique de la corbeille au chargement
    autoCleanTrash();
    
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    
    document.querySelector('[data-view="dashboard"]').click();
});




