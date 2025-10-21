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
                            <span id="userInitials">RH</span>
                        </div>
                        <div class="user-dropdown-menu" id="userDropdown">
                            <div class="user-dropdown-info">
                                <div id="dropdownUserName">Responsable HSE</div>
                                <div id="dropdownUserEmail">hse@eres-togo.com</div>
                            </div>
                            <button class="btn btn-sm btn-secondary w-full" id="refreshBtn">🔄 Actualiser</button>
                        </div>
                    </div>
                </div>
            </nav>