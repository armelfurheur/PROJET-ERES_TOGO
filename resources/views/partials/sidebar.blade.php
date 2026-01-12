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
    </div>

    <!-- ================= NAV ================= -->
    <nav class="sidebar-nav">
        <ul style="list-style: none; padding:0; margin:0;">

            <li class="nav-item">
                <a href="{{ route('statistics.view') }}"
                   class="nav-link active"
                   data-view="dashboard">
                    <span>Accueil</span>
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
                            Anomalies soumises
                            <span class="notification-badge"
                                  id="anomaliesNotificationBadge"
                                  style="display:none;">0</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('rapport.view') }}"
                           class="nav-link"
                           data-view="reports">
                            Rapports
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('archive.view') }}"
                           class="nav-link"
                           data-view="archive">
                            Archives
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>

</aside>

<!-- ================= CSS FEU / SIDEBAR ================= -->
<style>
/* LOGO */
.logo-img {
    width: 48px;
    height: auto;
    margin-bottom: 6px;
}

/* TEXTE FEU */
.fire-text {
    font-size: 1.4rem;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #ffe066 20%,
        #ffb703 40%,
        #ff6a00 60%,
        #d00000 80%
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    position: relative;
    animation: fireGlow 2s infinite alternate;
}

/* HALO FEU */
.fire-text::after {
    content: "ERESriskAlert";
    position: absolute;
    left: 0;
    top: 0;
    z-index: -1;
    color: #ff6a00;
    filter: blur(12px);
    opacity: 0.7;
    animation: fireFlicker 1.5s infinite;
}

/* SOUS TITRE */
.subtitle {
    font-size: 0.75rem;
    letter-spacing: 1px;
    color: #9ef0c1;
    margin-top: -2px;
}

/* ANIMATIONS */
@keyframes fireGlow {
    0% {
        filter: drop-shadow(0 0 6px #ffb703);
        transform: scale(1);
    }
    100% {
        filter: drop-shadow(0 0 16px #ff4500);
        transform: scale(1.05);
    }
}

@keyframes fireFlicker {
    0% { opacity: 0.5; }
    50% { opacity: 0.9; }
    100% { opacity: 0.6; }
}
</style>

<!-- ================= JS MENU ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sectionTitle = document.querySelector('.nav-section-title');
    const submenu = sectionTitle ? sectionTitle.nextElementSibling : null;
    const STORAGE_KEY = 'sidebar.hse.open';

    if (!sectionTitle || !submenu) return;

    const isOpen = () => submenu.classList.contains('open');

    const setOpen = (open) => {
        submenu.classList.toggle('open', open);
        sectionTitle.classList.toggle('open', open);
        localStorage.setItem(STORAGE_KEY, open ? '1' : '0');
    };

    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === '1') setOpen(true);

    sectionTitle.addEventListener('click', function (e) {
        e.preventDefault();
        setOpen(!isOpen());
    });

    submenu.addEventListener('click', function () {
        localStorage.setItem(STORAGE_KEY, '1');
    });

});
</script>
