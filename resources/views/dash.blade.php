<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Responsable HSE | ERES-TOGO</title>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Include jQuery first -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Toastr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>

    <!-- Toastr JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

    <!-- jsPDF pour génération PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<!-- Scripts -->
<script src="{{ asset('js/anomalie.js') }}"></script>
<script src="{{ asset('js/proposition.js') }}"></script>
<script src="{{ asset('js/rapport.js') }}"></script>
<script src="{{ asset('js/utils.js') }}"></script>
<script src="{{ asset('js/notifications.js') }}"></script>
<script src="{{ asset('js/navigation.js') }}"></script>
<script src="{{ asset('js/trash.js') }}"></script>
<script src="{{ asset('js/archives.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>





    
</head>
<body>
    <div class="dashboard-container">
    <!-- sidebar -->
      @include('partials.sidebar')
    <!-- sidebar -->

        <div class="main-content">
<!-- header -->
      @include('partials.header')
<!-- header -->
<main class="content-area">
<!-- Dashboard View -->
<!--container -->
 <div id="view-dashboard" class="hse-view">   
                 @yield('content')
     </div>
</main>
 <!--container -->

     <!--modal -->

    <!-- Modal pour détails anomalie -->
   
    <!-- Modal pour détails anomalie -->

    <!-- Modal pour proposer une action -->
   <!-- Modal pour proposer une action -->


    <!--footer -->
           @include('partials.footer')
<!--footer -->
      <!-- Modal pour proposer une action -->
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
        // ========== TOASTR CONFIGURATION ==========
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            onclick: null,
            showDuration: 300,
            hideDuration: 1000,
            timeOut: 5000,
            extendedTimeOut: 2000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        // ========== NOTIFICATIONS TOASTR ==========
        function showToast(message, type = 'success') {
            const options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                extendedTimeOut: 2000
            };

            switch(type) {
                case 'success':
                    toastr.success(message, 'Succès', options);
                    break;
                case 'warning':
                    toastr.warning(message, 'Attention', options);
                    break;
                case 'error':
                    toastr.error(message, 'Erreur', options);
                    break;
                case 'info':
                    toastr.info(message, 'Information', options);
                    break;
                default:
                    toastr.info(message, 'Information', options);
            }
        }

      

        // ========== TRASH VIEW ==========
        function renderTrashView() {
            const trash = getTrash();
            
            const html = `
                <div class="card">
                    <div class="card-header">
                        <h2>🗑️ Corbeille</h2>
                        <div class="btn-group">
                            <button class="btn btn-success btn-sm" id="restoreAllTrashBtn">♻️ Tout restaurer</button>
                            <button class="btn btn-danger btn-sm" id="emptyTrashBtn">⚠️ Vider la corbeille</button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Contenu</th>
                                    <th>Supprimé le</th>
                                    <th>Par</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${trash.length > 0 ? trash.map(item => `
                                    <tr>
                                        <td>
                                            <span class="badge ${item.type === 'anomaly' ? 'badge-warning' : 'badge-info'}">
                                                ${item.type === 'anomaly' ? '⚠️ Anomalie' : '📝 Proposition'}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>${item.type === 'anomaly' ? item.data.id.slice(0,12) : item.data.id.slice(0,12)}</strong><br>
                                            <small>${item.type === 'anomaly' ? item.data.description.slice(0,50) + '...' : item.data.action.slice(0,50) + '...'}</small>
                                        </td>
                                        <td>${formatDateTime(item.deletedAt)}</td>
                                        <td>${item.deletedBy}</td>
                                        <td style="text-align: center;">
                                            <button class="btn btn-success btn-sm btn-restore" data-id="${item.id}">♻️</button>
                                            <button class="btn btn-danger btn-sm btn-delete-permanent" data-id="${item.id}">🗑️</button>
                                        </td>
                                    </tr>
                                `).join('') : `
                                    <tr><td colspan="5" class="empty-state">Corbeille vide</td></tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('view-trash').innerHTML = html;
            
            document.getElementById('restoreAllTrashBtn')?.addEventListener('click', restoreAllTrash);
            document.getElementById('emptyTrashBtn')?.addEventListener('click', emptyTrashView);
            
            document.querySelectorAll('.btn-restore').forEach(btn => {
                btn.addEventListener('click', function() {
                    const trashId = this.dataset.id;
                    if (restoreFromTrash(trashId)) {
                        renderTrashView();
                        renderCurrentView();
                        showToast('Élément restauré', 'success');
                    }
                });
            });
            
            document.querySelectorAll('.btn-delete-permanent').forEach(btn => {
                btn.addEventListener('click', function() {
                    const trashId = this.dataset.id;
                    if (confirm('⚠️ Supprimer définitivement ? Cette action est irréversible.')) {
                        const trash = getTrash();
                        const newTrash = trash.filter(item => item.id !== trashId);
                        saveTrash(newTrash);
                        renderTrashView();
                        showToast('Élément supprimé définitivement', 'success');
                    }
                });
            });
        }

        function restoreAllTrash() {
            const trash = getTrash();
            if (trash.length === 0) {
                showToast('Corbeille vide', 'info');
                return;
            }
            
            if (confirm(`♻️ Restaurer tous les ${trash.length} éléments ?`)) {
                let restoredCount = 0;
                trash.forEach(item => {
                    if (restoreFromTrash(item.id)) {
                        restoredCount++;
                    }
                });
                renderTrashView();
                renderCurrentView();
                showToast(`${restoredCount} éléments restaurés`, 'success');
            }
        }

        function emptyTrashView() {
            if (emptyTrash()) {
                renderTrashView();
                showToast('Corbeille vidée', 'success');
            }
        }

        // ========== HELPERS ==========
        const formatDateTime = (iso) => new Date(iso).toLocaleString('fr-FR');

        function generateId(prefix='id') {
            return prefix + '_' + Date.now() + '_' + Math.random().toString(36).slice(2,9);
        }

        function downloadCSV(filename, rows, headers) {
            const csv = [headers.join(',')].concat(
                rows.map(r => headers.map(h => `"${(r[h] ?? '')?.toString().replace(/"/g,'""')}"`).join(','))
            ).join('\r\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            saveAs(blob, filename);
        }

        // Fonction améliorée pour l'export PDF avec logo ERES
        function openPrintable(title, htmlContent) {
            const isDarkMode = body.classList.contains('dark-mode');
            const bgColor = isDarkMode ? '#1f2937' : 'white';
            const textColor = isDarkMode ? '#f9fafb' : '#111827';
            const borderColor = isDarkMode ? '#4b5563' : '#d1d5db';
            const thBg = isDarkMode ? '#374151' : '#f3f4f6';
            const trEvenBg = isDarkMode ? '#4b5563' : '#f9fafb';
            const h1Color = isDarkMode ? '#10b981' : '#047857';
            const h1Border = isDarkMode ? '#10b981' : '#047857';

            const w = window.open('', '_blank');
            
            // Logo ERES (chargé depuis le dossier public/img)
    const eresLogo = "{{ asset('img/ERES.jpg') }}";

    // Exemple : création d'une image JS
    const img = new Image();
    img.src = eresLogo;

    img.onload = function() {
        console.log("Logo ERES chargé !");
        // Ici tu peux utiliser le logo pour ton affichage ou ton PDF
        // Exemple : doc.addImage(img, "JPEG", 10, 10, 40, 40);
    };
            w.document.write(`
                <html>
                <head>
                    <title>${title} - ERES-TOGO</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            padding: 40px; 
                            max-width: 1200px; 
                            margin: 0 auto; 
                            background: ${bgColor}; 
                            color: ${textColor}; 
                        }
                        .header { 
                            display: flex; 
                            align-items: center; 
                            justify-content: space-between; 
                            margin-bottom: 30px; 
                            padding-bottom: 20px; 
                            border-bottom: 3px solid ${h1Border}; 
                        }
                        .logo-container { 
                            display: flex; 
                            align-items: center; 
                            gap: 15px; 
                        }
                        .logo { 
                            width: 60px; 
                            height: 60px; 
                            border-radius: 10px; 
                            object-fit: cover; 
                        }
                        .company-info h1 { 
                            color: ${h1Color}; 
                            margin: 0; 
                            font-size: 24px; 
                        }
                        .company-info p { 
                            color: ${textColor}; 
                            margin: 0; 
                            font-size: 14px; 
                            opacity: 0.8; 
                        }
                        .document-info { 
                            text-align: right; 
                        }
                        .document-info h2 { 
                            color: ${h1Color}; 
                            margin: 0 0 10px 0; 
                            font-size: 20px; 
                        }
                        .document-info p { 
                            margin: 2px 0; 
                            font-size: 12px; 
                            color: ${textColor}; 
                            opacity: 0.8; 
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-top: 20px; 
                            border: 1px solid ${borderColor}; 
                        }
                        th, td { 
                            border: 1px solid ${borderColor}; 
                            padding: 12px; 
                            text-align: left; 
                        }
                        th { 
                            background: ${thBg}; 
                            color: ${textColor}; 
                            font-weight: 600; 
                        }
                        tr:nth-child(even) { 
                            background: ${trEvenBg}; 
                        }
                        .footer { 
                            margin-top: 40px; 
                            padding-top: 20px; 
                            border-top: 1px solid ${borderColor}; 
                            text-align: center; 
                            font-size: 12px; 
                            color: ${textColor}; 
                            opacity: 0.7; 
                        }
                        .stats-grid { 
                            display: grid; 
                            grid-template-columns: repeat(3, 1fr); 
                            gap: 15px; 
                            margin: 20px 0; 
                        }
                        .stat-item { 
                            padding: 15px; 
                            background: ${thBg}; 
                            border-radius: 8px; 
                            text-align: center; 
                            border-left: 4px solid ${h1Color}; 
                        }
                        .stat-value { 
                            font-size: 24px; 
                            font-weight: bold; 
                            color: ${h1Color}; 
                        }
                        .stat-label { 
                            font-size: 12px; 
                            color: ${textColor}; 
                            opacity: 0.8; 
                        }
                        @media print { 
                            body { 
                                padding: 20px; 
                                background: white; 
                                color: black; 
                            } 
                            .header { 
                                border-bottom-color: black; 
                            }
                            .company-info h1, 
                            .document-info h2 { 
                                color: black; 
                            }
                            th { 
                                background: #f3f4f6 !important; 
                                color: black !important; 
                            } 
                            tr:nth-child(even) { 
                                background: #f9fafb !important; 
                            }
                            .stat-item {
                                background: #f3f4f6 !important;
                            }
                            .stat-value {
                                color: #047857 !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="logo-container">
                            <img src="${eresLogo}" alt="Logo ERES-TOGO" class="logo">
                            <div class="company-info">
                            <p>Société de Référence en Sécurité</p>
                            </div>
                        </div>
                        <div class="document-info">
                            <h2>${title}</h2>
                            <p>Généré le: ${new Date().toLocaleDateString('fr-FR')}</p>
                            <p>Par: ${currentUser.name}</p>
                            <p>Email: ${currentUser.email}</p>
                        </div>
                    </div>
                    ${htmlContent}
               
            `);
            w.document.close();
            w.focus();
            setTimeout(() => w.print(), 300);
        }

        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        function setMinDateForProposals() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('proposal_date');
            if (dateInput) dateInput.setAttribute('min', today);
        }

        function updateReceptionDate() {
            const anomalyId = document.getElementById('proposal_anomaly_id').value;
            const anomaly = anomalies.find(a => a.id === anomalyId);
            if (anomaly) {
                document.getElementById('proposal_received').value = formatDateTime(anomaly.datetime);
            }
        }

        // ========== NOTIFICATIONS ==========
        function updateNotifications() {
            const unreadCount = anomalies.filter(a => !a.read).length;
            
            [document.getElementById('hseNotificationBadge'),
            document.getElementById('anomaliesNotificationBadge'),
            document.getElementById('topNotificationBadge')].forEach(badge => {
                if (badge) {
                    badge.textContent = unreadCount;
                    badge.style.display = unreadCount > 0 ? 'block' : 'none';
                }
            });
        }
;


        // ========== RENDER PARAMS ==========
        function renderParams() {
            document.getElementById('param_email').value = params.email || '';
            document.getElementById('param_email_cc').value = params.email_cc || '';
            document.getElementById('param_notify_email').checked = params.notify_email !== false;
            document.getElementById('param_notify_sound').checked = params.notify_sound !== false;
            document.getElementById('param_auto_archive').checked = params.auto_archive !== false;
            
            document.getElementById('info_total_anomalies').textContent = anomalies.length;
            document.getElementById('info_open_anomalies').textContent = anomalies.filter(a => a.status === 'Ouverte').length;
            document.getElementById('info_total_proposals').textContent = anomalies.reduce((sum, a) => sum + (a.proposals?.length || 0), 0);
            document.getElementById('info_last_update').textContent = new Date().toLocaleString('fr-FR');
        }

        // ========== NAVIGATION ==========
        document.getElementById('openHseMenu').addEventListener('click', function() {
            this.classList.toggle('open');
            document.getElementById('hseSubmenu').classList.toggle('open');
        });

        document.getElementById('openSettingsMenu').addEventListener('click', function() {
            this.classList.toggle('open');
            document.getElementById('settingsSubmenu').classList.toggle('open');
        });

        let currentView = 'dashboard';

        document.querySelectorAll('.nav-link[data-view]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = link.dataset.view;
                
                document.querySelectorAll('.hse-view').forEach(v => v.classList.add('hidden'));
                document.getElementById('view-' + view).classList.remove('hidden');
                
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                currentView = view;
                renderCurrentView();
            });
        });

        function renderCurrentView() {
            if (currentView === 'dashboard') renderDashboard();
            else if (currentView === 'anomalies') renderAnomalies();
            else if (currentView === 'proposals') renderProposals();
            else if (currentView === 'params') renderParams();
            else if (currentView === 'trash') renderTrashView();
            else if (currentView === 'reports') {
                if (currentReportData) {
                    createGravityChart(currentReportData.filtered, 'gravityChartCanvas');
                    createDepartmentChart(currentReportData.filtered, 'departmentChartCanvas');
                }
            }
        }

   

     

        // ========== PARAMETRES ==========
        document.getElementById('saveEmailConfig').addEventListener('click', () => {
            params.email = document.getElementById('param_email').value;
            params.email_cc = document.getElementById('param_email_cc').value;
            params.notify_email = document.getElementById('param_notify_email').checked;
            params.notify_sound = document.getElementById('param_notify_sound').checked;
            params.auto_archive = document.getElementById('param_auto_archive').checked;
            
            store.saveParams(params);
            showToast('Paramètres enregistrés !', 'success');
        });

        document.getElementById('exportAllData').addEventListener('click', () => {
            const allData = {
                anomalies,
                params,
                trash: getTrash(),
                export_date: new Date().toISOString()
            };
            
            const dataStr = JSON.stringify(allData, null, 2);
            const blob = new Blob([dataStr], { type: 'application/json' });
            saveAs(blob, `eres_backup_${new Date().toISOString().split('T')[0]}.json`);
            showToast('Données exportées !', 'success');
        });

        document.getElementById('clearOldData').addEventListener('click', () => {
            if (confirm('Archiver les anomalies clôturées de plus de 30 jours ?')) {
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                
                const oldAnomalies = anomalies.filter(a => 
                    a.status === 'Clos' && new Date(a.datetime) < thirtyDaysAgo
                );
                
                anomalies = anomalies.filter(a => 
                    !(a.status === 'Clos' && new Date(a.datetime) < thirtyDaysAgo)
                );
                
                store.saveAnomalies(anomalies);
                showToast(`${oldAnomalies.length} anomalie(s) archivée(s)`, 'success');
                renderAnomalies();
                renderParams();
                renderDashboard();
            }
        });

        document.getElementById('resetAllData').addEventListener('click', () => {
            if (confirm('⚠️ ATTENTION : Supprimer TOUTES les données ?')) {
                if (confirm('Confirmez-vous vraiment ?')) {
                    localStorage.clear();
                    showToast('Données effacées. Rechargement...', 'info');
                    setTimeout(() => location.reload(), 1500);
                }
            }
        });

        // ========== USER MENU ==========
        document.getElementById('userMenuBtn').addEventListener('click', function() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userMenuBtn && userDropdown) {
                if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            }
        });

        document.getElementById('refreshBtn').addEventListener('click', function() {
            location.reload();
        });

        // ========== MODAL CLOSE ==========
        document.getElementById('closeModal').addEventListener('click', closeModal);

 // ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    setMinDateForProposals();
    fetchAnomalies(); // Charge les données depuis la base de données
    updateNotifications();
    
    // Nettoyage automatique de la corbeille
    autoCleanTrash();
    
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    
    // Activer la vue dashboard par défaut
    document.querySelector('[data-view="dashboard"]').click();
});
        

  
// ========== SYNC DATA ==========
function syncData() {
    fetchAnomalies();
}

// ========== MODIFIER LA FONCTION viewAnomalyDetails ==========
async function viewAnomalyDetails(id) {
    try {
        // Extraire l'ID numérique de l'ID formaté
        const numericId = id.replace('anom_', '');
        const response = await fetch(`/api/anomalies/${numericId}`);
        const data = await response.json();
        
        const anomaly = data.anomalie;
        
        // Marquer comme lu dans la base de données
        if (!anomaly.read) {
            // Vous devrez ajouter une méthode pour marquer comme lu dans votre contrôleur
            await fetch(`/api/anomalies/${numericId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }
        
        // Le reste du code existant pour afficher les détails...
        const priorityText = anomaly.statut === 'arret' ? '🚨 Arrêt Imminent' : 
                            anomaly.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
        const priorityClass = anomaly.statut === 'arret' ? 'badge-arret' : 
                            anomaly.statut === 'precaution' ? 'badge-precaution' : 'badge-continuer';
        

                            
        // Afficher les détails dans la modal...
        document.getElementById('modalBody').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item">
                    <label>ID Anomalie</label>
                    <div class="value">${anomaly.id}</div>
                </div>
                <div class="detail-item">
                    <label>Date & Heure</label>
                    <div class="value">${formatDateTime(anomaly.datetime)}</div>
                </div>
                <div class="detail-item">
                    <label>Rapporté par</label>
                    <div class="value">${anomaly.rapporte_par}</div>
                </div>
                <div class="detail-item">
                    <label>Département</label>
                    <div class="value">${anomaly.departement}</div>
                </div>
                <div class="detail-item">
                    <label>Localisation</label>
                    <div class="value">${anomaly.localisation}</div>
                </div>
                <div class="detail-item">
                    <label>Gravité</label>
                    <div class="value"><span class="badge ${priorityClass}">${priorityText}</span></div>
                </div>
            </div>
            
            <div class="detail-full">
                <label>Description</label>
                <div class="value">${anomaly.description}</div>
            </div>
            
            <div class="detail-full">
                <label>Action immédiate</label>
                <div class="value">${anomaly.action}</div>
            </div>
            
            ${anomaly.preuve ? `<div class="detail-full"><label>Preuve</label><img src="/storage/${anomaly.preuve}" class="proof-image"></div>` : '<div class="detail-full"><label>Preuve</label><div class="value">Aucune preuve</div></div>'}
            
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
            </div>
        `;
        
        document.getElementById('anomalyModal').classList.add('active');
        
    } catch (error) {
        console.error('Erreur lors du chargement des détails:', error);
        showToast('Erreur lors du chargement des détails', 'error');
    }
    
}
// ========== ARCHIVES ==========
let archives = [];

// Charger les archives
async function fetchArchives() {
    try {
        const response = await fetch('{{ route("api.archives") }}');
        const data = await response.json();
        
        if (data.archives) {
            archives = data.archives;
            renderArchives();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des archives:', error);
    }
}

// Rendre les archives
function renderArchives() {
    const tbody = document.getElementById('archivesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (archives.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Aucune archive</td></tr>';
        return;
    }
    
    archives.forEach((archive) => {
        const priorityClass = archive.statut === 'arret' ? 'badge-arret' : 
                            archive.statut === 'precaution' ? 'badge-precaution' : 'badge-continuer';
        const priorityText = archive.statut === 'arret' ? '🚨 Arrêt' : 
                            archive.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>ARCH-${archive.id}</strong></td>
            <td>${formatDateTime(archive.datetime)}</td>
            <td>${archive.rapporte_par}</td>
            <td>${archive.departement}</td>
            <td style="text-align: center;"><span class="badge ${priorityClass}">${priorityText}</span></td>
            <td>${formatDateTime(archive.closed_at)}</td>
            <td>${archive.closed_by}</td>
            <td style="text-align: center;">
                <button class="btn btn-info btn-sm btn-view-archive" data-id="${archive.id}">👁️</button>
                <button class="btn btn-success btn-sm btn-restore-archive" data-id="${archive.id}">♻️</button>
                <button class="btn btn-danger btn-sm btn-delete-archive" data-id="${archive.id}">🗑️</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Clôturer une anomalie (MODIFIÉ)
async function closeAnomaly(id) {
    const anomaly = anomalies.find(a => a.id === id);
    if (!anomaly || !anomaly.proposals || anomaly.proposals.length === 0) {
        showToast('Une proposition d\'action est requise avant clôture', 'warning');
        return;
    }
    
    if (!confirm('Clôturer et archiver cette anomalie ?')) {
        return;
    }
    
    try {
        const numericId = id.replace('anom_', '');
        
        const response = await fetch(`/api/anomalies/${numericId}/close`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                closed_by: currentUser.name
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchAnomalies();
            await fetchArchives();
            renderAnomalies();
            renderDashboard();
            showToast('Anomalie clôturée et archivée !', 'success');
        } else {
            showToast(data.message || 'Erreur lors de la clôture', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la clôture', 'error');
    }
}

// Restaurer depuis les archives
async function restoreArchive(id) {
    if (!confirm('Restaurer cette anomalie depuis les archives ?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/archives/${id}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchAnomalies();
            await fetchArchives();
            renderArchives();
            renderAnomalies();
            renderDashboard();
            showToast('Anomalie restaurée !', 'success');
        } else {
            showToast(data.message || 'Erreur lors de la restauration', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la restauration', 'error');
    }
}

// Supprimer une archive définitivement
async function deleteArchive(id) {
    if (!confirm('⚠️ Supprimer définitivement cette archive ? Cette action est irréversible.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/archives/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            await fetchArchives();
            renderArchives();
            showToast('Archive supprimée définitivement', 'success');
        } else {
            showToast('Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la suppression', 'error');
    }
}

// Voir les détails d'une archive
function viewArchiveDetails(id) {
    const archive = archives.find(a => a.id === id);
    if (!archive) return;
    
    const priorityText = archive.statut === 'arret' ? '🚨 Arrêt Imminent' : 
                        archive.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
    const priorityClass = archive.statut === 'arret' ? 'badge-arret' : 
                        archive.statut === 'precaution' ? 'badge-precaution' : 'badge-continuer';
    
    let proposalsHtml = '';
    if (archive.proposals && archive.proposals.length > 0) {
        proposalsHtml = `
            <div class="detail-full">
                <label>Propositions d'actions</label>
                <div class="value">
                    ${archive.proposals.map(p => `
                        <div style="margin-bottom: 1rem; padding: 0.75rem; background: var(--gray-100); border-radius: 6px;">
                            <strong>Action:</strong> ${p.action}<br>
                            <strong>Responsable:</strong> ${p.person}<br>
                            <strong>Date prévue:</strong> ${p.date}<br>
                            <strong>Statut:</strong> <span class="badge badge-proposed">${p.status}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    document.getElementById('modalBody').innerHTML = `
        <div class="detail-grid">
            <div class="detail-item">
                <label>ID Archive</label>
                <div class="value">ARCH-${archive.id}</div>
            </div>
            <div class="detail-item">
                <label>Date anomalie</label>
                <div class="value">${formatDateTime(archive.datetime)}</div>
            </div>
            <div class="detail-item">
                <label>Rapporté par</label>
                <div class="value">${archive.rapporte_par}</div>
            </div>
            <div class="detail-item">
                <label>Département</label>
                <div class="value">${archive.departement}</div>
            </div>
            <div class="detail-item">
                <label>Localisation</label>
                <div class="value">${archive.localisation}</div>
            </div>
            <div class="detail-item">
                <label>Gravité</label>
                <div class="value"><span class="badge ${priorityClass}">${priorityText}</span></div>
            </div>
            <div class="detail-item">
                <label>Date clôture</label>
                <div class="value">${formatDateTime(archive.closed_at)}</div>
            </div>
            <div class="detail-item">
                <label>Clôturé par</label>
                <div class="value">${archive.closed_by}</div>
            </div>
        </div>
        
        <div class="detail-full">
            <label>Description</label>
            <div class="value">${archive.description}</div>
        </div>
        
        <div class="detail-full">
            <label>Action immédiate</label>
            <div class="value">${archive.action}</div>
        </div>
        
        ${archive.preuve ? `<div class="detail-full"><label>Preuve</label><img src="/storage/${archive.preuve}" class="proof-image"></div>` : '<div class="detail-full"><label>Preuve</label><div class="value">Aucune preuve</div></div>'}
        
        ${proposalsHtml}
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <button class="btn btn-success" onclick="restoreArchive(${archive.id})">♻️ Restaurer</button>
            <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
        </div>
    `;
    
    document.getElementById('anomalyModal').classList.add('active');
}

// Event listeners pour les archives
document.addEventListener('click', (e) => {
    if (e.target.matches('.btn-view-archive')) {
        viewArchiveDetails(parseInt(e.target.dataset.id));
    } else if (e.target.matches('.btn-restore-archive')) {
        restoreArchive(parseInt(e.target.dataset.id));
    } else if (e.target.matches('.btn-delete-archive')) {
        deleteArchive(parseInt(e.target.dataset.id));
    }
});

// Exports archives
document.getElementById('exportArchivesCsv')?.addEventListener('click', () => {
    if (!archives.length) {
        showToast('Aucune archive à exporter', 'warning');
        return;
    }
    const rows = archives.map(a => ({
        id: 'ARCH-' + a.id,
        datetime: formatDateTime(a.datetime),
        rapporte_par: a.rapporte_par,
        departement: a.departement,
        localisation: a.localisation,
        gravite: a.statut,
        closed_at: formatDateTime(a.closed_at),
        closed_by: a.closed_by
    }));
    downloadCSV('archives_eres_togo.csv', rows, ['id','datetime','rapporte_par','departement','localisation','gravite','closed_at','closed_by']);
});

document.getElementById('exportArchivesPdf')?.addEventListener('click', () => {
    if (!archives.length) {
        showToast('Aucune archive à exporter', 'warning');
        return;
    }
    const html = `
        <h1>📦 Archives des Anomalies</h1>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">${archives.length}</div>
                <div class="stat-label">Total archives</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>ID</th><th>Date anomalie</th><th>Rapporté par</th><th>Département</th><th>Gravité</th><th>Date clôture</th><th>Clôturé par</th></tr>
            </thead>
            <tbody>
                ${archives.map(a => `<tr>
                    <td>ARCH-${a.id}</td>
                    <td>${formatDateTime(a.datetime)}</td>
                    <td>${a.rapporte_par}</td>
                    <td>${a.departement}</td>
                    <td>${a.statut === 'arret' ? '🚨 Arrêt' : a.statut === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td>
                    <td>${formatDateTime(a.closed_at)}</td>
                    <td>${a.closed_by}</td>
                </tr>`).join('')}
            </tbody>
        </table>
    `;
    openPrintable('Archives Anomalies', html);
});

// Modifier renderCurrentView pour inclure les archives
function renderCurrentView() {
    if (currentView === 'dashboard') renderDashboard();
    else if (currentView === 'anomalies') renderAnomalies();
    else if (currentView === 'proposals') renderProposals();
    else if (currentView === 'params') renderParams();
    else if (currentView === 'trash') renderTrashView();
    else if (currentView === 'archive') renderArchives();
    else if (currentView === 'reports') {
        if (currentReportData) {
            createGravityChart(currentReportData.filtered, 'gravityChartCanvas');
            createDepartmentChart(currentReportData.filtered, 'departmentChartCanvas');
        }
    }
}

// Modifier l'événement de clôture dans les anomalies
document.addEventListener('click', (e) => {
    const id = e.target.dataset.id;
    
    if (e.target.matches('.btn-view-anomaly')) {
        viewAnomalyDetails(id);
    } else if (e.target.matches('.btn-propose-action')) {
        openProposalModal(id);
    } else if (e.target.matches('.btn-close-anomaly')) {
        closeAnomaly(id); // Utilise la nouvelle fonction
    } else if (e.target.matches('.btn-delete-anomaly')) {
        deleteAnomaly(id);
    }
});

// Modifier closeAnomalyFromModal
window.closeAnomalyFromModal = async function(id) {
    await closeAnomaly(id);
    closeModal();
};

// Modifier l'initialisation pour charger les archives
document.addEventListener('DOMContentLoaded', function() {
    loadUserData();
    setMinDateForProposals();
    fetchAnomalies();
    fetchArchives(); // Ajouter ceci
    updateNotifications();
    autoCleanTrash();
    
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    document.querySelector('[data-view="dashboard"]').click();
});


    </script>
</body>
</html>