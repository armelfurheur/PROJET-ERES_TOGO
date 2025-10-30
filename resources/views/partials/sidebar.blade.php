<aside class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="logo-container">
            <div class="logo-text">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                <h1>ERES-TOGO</h1>
                <p>Dashboard HSE</p>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul style="list-style: none;">
            <li class="nav-item">
                <a href="#" class="nav-link active" data-view="dashboard">
                    <span>Tableau de bord</span>
                </a>
            </li>

            <!-- HSE -->
            <li class="nav-item" style="margin-top: 1.5rem;">
                <div class="nav-section-title">
                    <span>Resp HSE</span>
                    <i class="icon fas fa-chevron-down"></i>
                    <span class="notification-badge" id="hseNotificationBadge" style="display:none;">0</span>
                </div>
                <ul class="sub-menu">
                    <li style="position:relative;">
                        <a href="#" class="nav-link" data-view="anomalies">
                            Anomalies soumises
                            <span class="notification-badge" id="anomaliesNotificationBadge" style="display:none;">0</span>
                        </a>
                    </li>
                    <li><a href="#" class="nav-link" data-view="proposals">Propositions actions</a></li>
                    <li><a href="#" class="nav-link" data-view="reports">Rapports</a></li>
                </ul>
            </li>

            <!-- Paramètres -->
            <li class="nav-item" style="margin-top: 1.5rem;">
                <div class="nav-section-title">
                    <span>Paramètres</span>
                    <i class="icon fas fa-chevron-down"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="#" class="nav-link" data-view="params">Configuration</a></li>
                    <li><a href="#" class="nav-link" data-view="archive">Archives</a></li>
                    <li><a href="#" class="nav-link" data-view="trash">Corbeille</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- JS DU MENU DÉROULANT + NAVIGATION (INCLUS DANS LE SIDEBAR) -->
    <script>
        // Attendre que le DOM soit chargé
        document.addEventListener('DOMContentLoaded', function () {
            const views = ['dashboard', 'anomalies', 'proposals', 'reports', 'params', 'archive', 'trash'];

            // Fonction pour activer une vue
            function showView(view) {
                // Cacher toutes les vues
                views.forEach(v => {
                    const el = document.getElementById('view-' + v);
                    if (el) el.classList.add('hidden');
                });

                // Afficher la vue demandée
                const target = document.getElementById('view-' + view);
                if (target) target.classList.remove('hidden');

                // Mettre à jour les liens actifs
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.dataset.view === view) {
                        link.classList.add('active');
                    }
                });

                // Appeler les fonctions de rendu spécifiques
                if (typeof window.renderCurrentView === 'function') {
                    window.currentView = view;
                    window.renderCurrentView();
                }
            }

            // Attacher les événements aux liens
            document.querySelectorAll('.nav-link[data-view]').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const view = this.dataset.view;
                    showView(view);
                });
            });

            // Menu déroulant
            document.querySelectorAll('.nav-section-title').forEach(title => {
                title.addEventListener('click', function () {
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('sub-menu')) {
                        this.classList.toggle('open');
                        submenu.classList.toggle('open');
                    }
                });
            });

            // Charger la vue par défaut (dashboard)
            showView('dashboard');
        });
    </script>
</aside>