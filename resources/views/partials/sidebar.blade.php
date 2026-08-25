<aside class="sidebar">

    <!-- ================= HEADER ================= -->
    <div class="sidebar-header">
        <a href="#" class="logo-container">
            <div class="logo-text">

                <!-- Logo image -->
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">

                <!-- Texte FEU -->
                <h1 class="fire-text">ERESriskAlert</h1>
                <p class="subtitle">Dashboard HSE</p>

            </div>
        </a>

        <!-- Bouton plier/déplier -->
        <button class="btn-icon" id="sidebarToggle" title="Plier le sidebar">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- ================= NAV ================= -->
    <nav class="sidebar-nav">
        <ul style="list-style: none; padding:0; margin:0;">

            <li class="nav-item">
                <a href="{{ route('statistics.view') }}"
                   class="nav-link"
                   data-view="dashboard">
                    <span>Tableau de bord</span>
                </a>
            </li> 

            <!-- ===== HSE ===== -->
            <li class="nav-item" style="margin-top: 1.5rem;">
                <div class="nav-section-title">
                    <span>Responsable HSE</span>
                    <i class="icon fas fa-chevron-down"></i>
                    <span class="notification-badge"
                          id="hseNotificationBadge"
                          style="display:none;">0</span>
                </div>

                <ul class="sub-menu">
                    <li style="position:relative;">
                        <a href="{{ route('anomalies.view') }}"
                           class="nav-link"
                           data-view="anomalies"
                           id="load-anomalies">
                            <span>Anomalies soumises</span>
                            <span class="notification-badge"
                                  id="anomaliesNotificationBadge"
                                  style="display:none;">0</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('rapport.view') }}"
                           class="nav-link"
                           data-view="reports">
                            <span>Rapports</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('archive.view') }}"
                           class="nav-link"
                           data-view="archive">
                            <span>Archives</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>

</aside>

<!-- ================= CSS FEU / SIDEBAR ================= -->
<style>
.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.btn-icon {
    flex-shrink: 0;
    background: transparent;
    border: none;
    cursor: pointer;
    color: inherit;
    padding: 6px;
    border-radius: 6px;
    transition: background 0.2s ease, transform 0.2s ease;
}

.btn-icon:hover {
    background: rgba(0, 0, 0, 0.06);
}

/* --- État plié du sidebar --- */
.sidebar.collapsed {
    width: 72px; /* ajuste selon ta largeur normale */
}

.sidebar.collapsed .logo-text h1,
.sidebar.collapsed .logo-text p,
.sidebar.collapsed .nav-section-title span:not(.notification-badge),
.sidebar.collapsed .sub-menu a span:not(.notification-badge),
.sidebar.collapsed .nav-link span:not(.notification-badge) {
    display: none;
}

.sidebar.collapsed .sidebar-header {
    justify-content: center;
}

.sidebar.collapsed .btn-icon svg {
    transform: rotate(180deg);
}
</style>

<!-- ================= JS MENU ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ====== GESTION SOUS-MENU HSE ======
    const sectionTitle = document.querySelector('.nav-section-title');
    const submenu = sectionTitle ? sectionTitle.nextElementSibling : null;
    const SUBMENU_KEY = 'sidebar.hse.open';

    if (sectionTitle && submenu) {

        const isOpen = () => submenu.classList.contains('open');

        const setOpen = (open) => {
            submenu.classList.toggle('open', open);
            sectionTitle.classList.toggle('open', open);
            localStorage.setItem(SUBMENU_KEY, open ? '1' : '0');
        };

        const saved = localStorage.getItem(SUBMENU_KEY);
        if (saved === '1') setOpen(true);

        sectionTitle.addEventListener('click', function (e) {
            e.preventDefault();
            setOpen(!isOpen());
        });
    }

    // ====== GESTION TRAIT BAS ACTIF ======
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function () {

            // Retirer active de tous les liens
            navLinks.forEach(l => l.classList.remove('active'));

            // Ajouter active au lien cliqué
            this.classList.add('active');

            // Sauvegarder dans localStorage
            localStorage.setItem('sidebar.activeLink', this.dataset.view);

        });
    });

    // Restaurer le lien actif après reload
    const savedView = localStorage.getItem('sidebar.activeLink');
    if (savedView) {
        const savedLink = document.querySelector(`.sidebar-nav .nav-link[data-view="${savedView}"]`);
        if (savedLink) savedLink.classList.add('active');
    }

    // ====== GESTION TOGGLE SIDEBAR (plier/déplier) ======
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const SIDEBAR_KEY = 'sidebar.collapsed';

    if (sidebar && sidebarToggle) {

        const setCollapsed = (collapsed) => {
            sidebar.classList.toggle('collapsed', collapsed);
            sidebarToggle.title = collapsed ? 'Déplier le sidebar' : 'Plier le sidebar';
            localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
        };

        // Restaurer l'état sauvegardé au chargement
        const savedState = localStorage.getItem(SIDEBAR_KEY);
        if (savedState === '1') setCollapsed(true);

        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            setCollapsed(!sidebar.classList.contains('collapsed'));
        });
    }

});
</script>