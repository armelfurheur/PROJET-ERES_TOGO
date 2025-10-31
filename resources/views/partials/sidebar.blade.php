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
               
                  <a href="{{ route('statistics.view') }}" class="nav-link active" data-view="dashboard">
                           
                                                <span>Tableau de bord</span>
                        </a>
            </li>

            <!-- HSE -->
            <li class="nav-item" style="margin-top: 1.5rem;">
                <div class="nav-section-title">
                    <span>Responsable HSE</span>
                    <i class="icon fas fa-chevron-down"></i>
                    <span class="notification-badge" id="hseNotificationBadge" style="display:none;">0</span>
                </div>
                <ul class="sub-menu">
                    <li style="position:relative;">
                        <a href="{{ route('anomalies.view') }}" class="nav-link" data-view="anomalies" id="load-anomalies">
                            Anomalies soumises
                            <span class="notification-badge" id="anomaliesNotificationBadge" style="display:none;">0</span>
                        </a>


                    </li>
                    <li><a href="{{ route('proposition.view') }}" class="nav-link" data-view="proposals">Propositions actions</a></li>
                    <li><a href="{{ route('rapport.view') }}" class="nav-link" data-view="reports">Rapports</a></li>
                </ul>
            </li>

            <!-- Paramètres -->
            <li class="nav-item" style="margin-top: 1.5rem;">
                <div class="nav-section-title">
                    <span>Paramètres</span>
                    <i class="icon fas fa-chevron-down"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="{{ route('configuration.view') }}" class="nav-link" data-view="params">Configuration</a></li>
                    <li><a href="{{ route('archive.view') }}" class="nav-link" data-view="archive">Archives</a></li>
                    <li><a href="#" class="nav-link" data-view="trash">Corbeille</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- JS DU MENU DÉROULANT (AUTONOME) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-section-title').forEach(title => {
                title.addEventListener('click', function() {
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('sub-menu')) {
                        submenu.classList.toggle('open'); // Affiche ou cache le menu
                        this.classList.toggle('open'); // Pour rotation de l’icône
                    }
                });
            });
        });
    </script>
</aside>