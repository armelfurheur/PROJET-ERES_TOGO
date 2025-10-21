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
                        <a href="#" class="nav-link active" data-view="dashboard"><span>📊 Tableau de bord</span></a>
                    </li>

                    <li class="nav-item" style="margin-top: 1.5rem;">
                        <div class="nav-section-title" id="openHseMenu">
                            <span>🛡️ Responsable HSE</span>
                            <span class="icon">▾</span>
                            <span class="notification-badge" id="hseNotificationBadge" style="display:none;">0</span>
                        </div>
                        <ul class="sub-menu" id="hseSubmenu" style="list-style: none;">
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

                    <li class="nav-item" style="margin-top: 1.5rem;">
                        <div class="nav-section-title" id="openSettingsMenu">
                            <span>⚙️ Paramètres</span>
                            <span class="icon">▾</span>
                        </div>
                        <ul class="sub-menu" id="settingsSubmenu" style="list-style: none;">
                            <li><a href="#" class="nav-link" data-view="params">Configuration</a></li>
                            <li><a href="#" class="nav-link" data-view="trash">🗑️ Corbeille</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

        </aside>