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

                    <span>Acceuil</span>
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
                    <li><a href="{{ route('rapport.view') }}" class="nav-link" data-view="reports">Rapports</a></li>
                    <li><a href="{{ route('archive.view') }}" class="nav-link" data-view="archive">Archives</a></li>


                </ul>

            </li>

          
    </nav>

    <!-- JS DU MENU DÉROULANT (AUTONOME) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sectionTitle = document.querySelector('.nav-section-title');
    const submenu = sectionTitle ? sectionTitle.nextElementSibling : null;
    const STORAGE_KEY = 'sidebar.hse.open'; // clé de persistance

    if (!sectionTitle || !submenu) return;

    // ---- Helpers
    const isOpen = () => submenu.classList.contains('open');
    const setOpen = (open) => {
        submenu.classList.toggle('open', open);
        sectionTitle.classList.toggle('open', open);
        localStorage.setItem(STORAGE_KEY, open ? '1' : '0');
    };

    // ---- Restaurer l’état au chargement
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === '1') setOpen(true);

    // ---- Ouvrir/fermer uniquement en cliquant sur "Responsable HSE"
    sectionTitle.addEventListener('click', function (e) {
        e.preventDefault();
        setOpen(!isOpen());
    });

    // ---- Quand on clique dans le sous-menu, on force l’état à "ouvert"
    // (pour qu’il reste ouvert après navigation)
    submenu.addEventListener('click', function () {
        localStorage.setItem(STORAGE_KEY, '1');
    });
});
</script>

</aside>