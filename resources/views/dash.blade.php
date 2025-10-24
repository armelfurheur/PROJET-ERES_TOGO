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
    <style>
        :root {
            --primary: #047857;
            --primary-dark: #065f46;
            --primary-light: #10b981;
            --accent: #34d399;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #3067dd;
            --bg-main: #f9fafb;
            --bg-card: white;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        /* Mode sombre */
        body.dark-mode {
            --gray-50: #1f2937;
            --gray-100: #374151;
            --gray-200: #4b5563;
            --gray-300: #6b7280;
            --gray-600: #d1d5db;
            --gray-700: #e5e7eb;
            --gray-800: #f3f4f6;
            --gray-900: #f9fafb;
            --bg-main: #111827;
            --bg-card: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background 0.3s, color 0.3s;
        }

        .dashboard-container { display: flex; min-height: 100vh; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 10;
            transition: width 0.3s;
        }

        .sidebar.collapsed {
            width: 60px;
            overflow: hidden;
        }

        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .nav-link span:not(.icon),
        .sidebar.collapsed .nav-section-title span:not(.icon),
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .notification-badge {
            display: none;
        }

        .sidebar.collapsed .nav-link,
        .sidebar.collapsed .nav-section-title {
            justify-content: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .sub-menu {
            display: none;
        }

        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }

        .logo-container { display: flex; align-items: center; gap: 0.875rem; text-decoration: none; color: white; }

        .logo-img { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; box-shadow: var(--shadow-md); }

        .logo-text h1 { font-size: 1.25rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }

        .logo-text p { font-size: 0.75rem; color: var(--accent); margin: 0; font-weight: 500; }

        .sidebar-nav { flex: 1; padding: 1.5rem 1rem; overflow-y: auto; }

        .nav-item { margin-bottom: 0.5rem; position: relative; list-style: none; }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; transform: translateX(2px); }

        .nav-link.active { background: rgba(255,255,255,0.15); color: white; }

        .nav-section-title {
            background: rgba(0,0,0,0.2);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .nav-section-title:hover { background: rgba(0,0,0,0.3); }

        .nav-section-title .icon { transition: transform 0.3s; }

        .nav-section-title.open .icon { transform: rotate(180deg); }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 9999px;
            min-width: 20px;
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.9; }
        }

        .sub-menu {
            margin-top: 0.5rem;
            margin-left: 0.5rem;
            border-left: 2px solid rgba(255,255,255,0.2);
            padding-left: 0.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sub-menu.open { max-height: 500px; }

        .sub-menu .nav-link { font-size: 0.85rem; padding: 0.625rem 1rem; }

        .sidebar-footer { padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }

        .user-profile { display: flex; align-items: center; gap: 0.875rem; }

        .user-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }

        .user-info h4 { font-size: 0.9rem; font-weight: 600; margin: 0; }

        .user-info p { font-size: 0.75rem; color: var(--accent); margin: 0; }

        /* ===== MAIN CONTENT ===== */
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; position: relative; }

        .page-logo {
            position: absolute;
            top: 80px;
            left: 20px;
            width: 60px;
            height: 60px;
            z-index: 1;
        }

        .top-nav {
            background: var(--bg-card);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 5;
            transition: background 0.3s;
        }

        .nav-title { display: flex; align-items: center; gap: 1rem; }

        .nav-title h2 { font-size: 1.5rem; color: var(--primary-dark); font-weight: 700; margin: 0; }

        .nav-actions { display: flex; align-items: center; gap: 1rem; }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--gray-100);
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            position: relative;
        }

        .btn-icon:hover { background: var(--gray-200); }

        .btn-icon .notification-badge { top: -3px; right: -3px; }

        .user-profile-dropdown { position: relative; }

        .user-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-badge:hover { background: var(--primary-dark); transform: scale(1.05); }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem;
            min-width: 220px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            margin-top: 0.5rem;
        }

        .user-dropdown-menu.active { display: block; }

        .user-dropdown-info { 
            padding: 0.5rem; 
            border-bottom: 1px solid var(--gray-200); 
            margin-bottom: 0.5rem;
        }

        .user-dropdown-info div:first-child { font-weight: 600; font-size: 0.9rem; }
        .user-dropdown-info div:last-child { font-size: 0.75rem; color: var(--text-secondary); }

        .content-area { flex: 1; padding: 2rem; overflow-y: auto; }

        .page-header { margin-bottom: 2rem; }

        .page-header h1 { font-size: 2rem; color: var(--text-primary); font-weight: 700; margin-bottom: 0.5rem; }

        .page-header p { color: var(--text-secondary); font-size: 1rem; }

        /* ===== CARDS ===== */
        .card { 
            background: var(--bg-card); 
            border-radius: 12px; 
            box-shadow: var(--shadow); 
            padding: 1.5rem; 
            margin-bottom: 1.5rem;
            transition: background 0.3s;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-100);
        }

        .card-header h2 { font-size: 1.25rem; color: var(--primary-dark); font-weight: 600; margin: 0; }

        /* ===== MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .modal.active { display: flex; }

        .modal-content {
            background: var(--bg-card);
            border-radius: 16px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            transition: background 0.3s;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .modal-header h3 { font-size: 1.5rem; font-weight: 700; margin: 0; }

        .modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .modal-close:hover { background: rgba(255,255,255,0.3); }

        .modal-body { padding: 1.5rem; }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border-left: 3px solid var(--primary);
        }

        .detail-item label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .detail-item .value { font-size: 1rem; color: var(--text-primary); font-weight: 600; }

        .detail-full {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border-left: 3px solid var(--primary);
        }

        .detail-full label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .detail-full .value { color: var(--text-primary); line-height: 1.6; white-space: pre-wrap; }

        .proof-image { max-width: 100%; border-radius: 8px; box-shadow: var(--shadow-md); margin-top: 1rem; }

        /* ===== FORMS ===== */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group { display: flex; flex-direction: column; }

        .form-group label { font-weight: 500; font-size: 0.875rem; color: var(--text-primary); margin-bottom: 0.5rem; }

        .form-control {
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1);
        }

        .form-control:disabled {
            background: var(--gray-100);
            cursor: not-allowed;
            color: var(--text-secondary);
        }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary { background: var(--primary); color: white; }

        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: var(--shadow-md); }

        .btn-secondary { background: var(--gray-700); color: white; }

        .btn-secondary:hover { background: var(--gray-800); }

        .btn-sm { padding: 0.4rem 0.875rem; font-size: 0.8rem; }

        .btn-warning { background: #f59e0b; color: white; }

        .btn-warning:hover { background: #d97706; }

        .btn-danger { background: #ef4444; color: white; }

        .btn-danger:hover { background: #dc2626; }

        .btn-success { background: var(--primary-light); color: white; }

        .btn-success:hover { background: var(--primary); }

        .btn-info { background: #3b82f6; color: white; }

        .btn-info:hover { background: #2563eb; }

        .btn-group { display: flex; gap: 0.75rem; flex-wrap: wrap; }

        /* ===== TABLES ===== */
        .table-container { overflow-x: auto; margin-bottom: 1rem; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); }

        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }

        thead { background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            background: var(--gray-100);
        }

        td { padding: 1rem; border-bottom: 1px solid var(--gray-200); color: var(--text-primary); }

        tbody tr { transition: background 0.2s; }

        tbody tr:hover { background: var(--gray-50); }

        tbody tr.unread { background: #fef3c7; font-weight: 500; }

        body.dark-mode tbody tr.unread { background: #78350f; }

        td input, td select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 0.85rem;
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-secondary); }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-open { background: #fef3c7; color: #92400e; }
        .badge-closed { background: #d1fae5; color: #065f46; }
        .badge-proposed { background: #e0e7ff; color: #3730a3; }
        .badge-arret { background: #fee2e2; color: #991b1b; }
        .badge-precaution { background: #fef3c7; color: #92400e; }
        .badge-continuer { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0e7ff; color: #3730a3; }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
            transition: background 0.3s;
        }

        .stat-card h4 { font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.5rem 0; font-weight: 500; }

        .stat-card .value { font-size: 2rem; font-weight: 700; color: var(--text-primary); }

        .stat-card.warning { border-left-color: #f59e0b; }

        .stat-card.success { border-left-color: #10b981; }

        /* ===== CHARTS ===== */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .chart-card {
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .chart-card h4 { margin-bottom: 1rem; text-align: center; font-size: 1rem; }

        .chart-container { 
            height: 250px; 
            position: relative;
        }

        /* ===== FOOTER ===== */
        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
            transition: background 0.3s;
        }

        /* ===== TOASTR CUSTOMIZATION ===== */
        .toast {
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            font-family: 'Inter', sans-serif;
        }

        .toast-success {
            background-color: var(--primary);
            border-left: 4px solid var(--primary-dark);
        }

        .toast-warning {
            background-color: #f59e0b;
            border-left: 4px solid #d97706;
        }

        .toast-error {
            background-color: #ef4444;
            border-left: 4px solid #dc2626;
        }

        .toast-info {
            background-color: #3b82f6;
            border-left: 4px solid #2563eb;
        }

        /* Mode sombre pour Toastr */
        body.dark-mode .toast {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--gray-600);
        }

        body.dark-mode .toast-success {
            border-left-color: var(--primary-light);
        }

        body.dark-mode .toast-warning {
            border-left-color: #f59e0b;
        }

        body.dark-mode .toast-error {
            border-left-color: #ef4444;
        }

        body.dark-mode .toast-info {
            border-left-color: #3b82f6;
        }

        /* ===== UTILITIES ===== */
        .hidden { display: none; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 1.5rem; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -280px; top: 0; bottom: 0; transition: left 0.3s; z-index: 100; }
            .sidebar.open { left: 0; }
            .form-grid { grid-template-columns: 1fr; }
            .top-nav { padding: 1rem; }
            .content-area { padding: 1rem; }
            .modal { padding: 1rem; }
            .page-logo { top: 60px; left: 10px; width: 50px; height: 50px; }
            .charts-container { grid-template-columns: 1fr; }
            .user-dropdown-menu { right: -50px; }
        }

        /* Améliorations pour mode sombre */
        body.dark-mode .btn-secondary { background: var(--gray-600); color: var(--text-primary); }
        body.dark-mode .btn-secondary:hover { background: var(--gray-500); }

        /* Styles pour le message de bienvenue */
        .welcome-container {
            text-align: center;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .welcome-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }

        body.dark-mode .welcome-title {
            color: var(--accent);
        }
    </style>
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
   <div id="anomalyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                <h3>Détails de l'anomalie</h3>
                <button class="modal-close" id="closeModal">×</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>
    <!-- Modal pour détails anomalie -->

    <!-- Modal pour proposer une action -->
      <div id="proposalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="logo-img">
                <h3>Proposer une action corrective</h3>
                <button class="modal-close" id="closeProposalModal">×</button>
            </div>
            <div class="modal-body">
                <form id="proposalForm">
                    <input type="hidden" id="proposal_anomaly_id">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="proposal_received">📅 Date & heure de réception (anomalie)</label>
                            <input type="text" id="proposal_received" class="form-control" disabled placeholder="Généré automatiquement">
                        </div>
                        <div class="form-group">
                            <label for="proposal_action">Action corrective *</label>
                            <input type="text" id="proposal_action" class="form-control" placeholder="Ex: Installer un garde-corps">
                        </div>
                        <div class="form-group">
                            <label for="proposal_person">Personne responsable *</label>
                            <input type="text" id="proposal_person" class="form-control" placeholder="Ex: Équipe maintenance">
                        </div>
                        <div class="form-group">
                            <label for="proposal_date">Date prévue d'action *</label>
                            <input type="date" id="proposal_date" class="form-control">
                        </div>
                    </div>
                    <div style="text-align: right; margin-top: 1rem;">
                        <button type="button" id="addProposalBtn" class="btn btn-primary">➕ Ajouter proposition</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <!--modal -->

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

        // ========== MODIFIED DELETE FUNCTIONS ==========
        function deleteAnomaly(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (!anomaly) return;
            
            if (confirm('Supprimer cette anomalie ? Elle sera placée dans la corbeille.')) {
                const proposalsBackup = anomaly.proposals || [];
                addToTrash(anomaly, 'anomaly', { proposals: proposalsBackup });
                anomalies = anomalies.filter(a => a.id !== id);
                store.saveAnomalies(anomalies);
                renderAnomalies();
                renderProposals();
                renderDashboard();
                showToast('Anomalie déplacée dans la corbeille', 'success');
            }
        }

        function deleteProposal(proposalId) {
            let deleted = false;
            let anomalyData = null;
            
            anomalies.forEach(a => {
                if (a.proposals) {
                    const propIndex = a.proposals.findIndex(p => p.id === proposalId);
                    if (propIndex !== -1) {
                        const proposal = a.proposals[propIndex];
                        addToTrash(proposal, 'proposal', { anomalyId: a.id });
                        a.proposals.splice(propIndex, 1);
                        deleted = true;
                        anomalyData = a;
                    }
                }
            });
            
            if (deleted) {
                store.saveAnomalies(anomalies);
                renderProposals();
                renderAnomalies();
                renderDashboard();
                showToast('Proposition déplacée dans la corbeille', 'success');
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
                    <div class="footer">
                        <p>ERES-TOGO - Tous droits réservés | www.eres-togo.com</p>
                    </div>
                </body>
                </html>
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

        // ========== CHARTS ==========
        let gravityChart = null;
        let departmentChart = null;
        let dashboardGravityChart = null;
        let dashboardDepartmentChart = null;

        function createGravityChart(filtered, canvasId, isDashboard = false) {
            const arret = filtered.filter(a => a.statut_anomalie === 'arret').length;
            const precaution = filtered.filter(a => a.statut_anomalie === 'precaution').length;
            const continuer = filtered.filter(a => a.statut_anomalie === 'continuer').length;
            const total = arret + precaution + continuer;
            
            const arretPct = total > 0 ? Math.round((arret / total) * 100) : 0;
            const precautionPct = total > 0 ? Math.round((precaution / total) * 100) : 0;
            const continuerPct = total > 0 ? Math.round((continuer / total) * 100) : 0;
            
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            
            const existingChart = isDashboard ? dashboardGravityChart : gravityChart;
            if (existingChart) existingChart.destroy();
            
            const data = [
                { label: `🚨 Arrêt Imminent`, value: arretPct },
                { label: `⚠️ Précaution`, value: precautionPct },
                { label: `🟢 Continuer`, value: continuerPct }
            ].filter(d => d.value > 0);
            
            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        data: data.map(d => d.value),
                        backgroundColor: ['rgba(239, 68, 68, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(16, 185, 129, 0.7)'],
                        borderColor: ['rgb(239, 68, 68)', 'rgb(245, 158, 11)', 'rgb(16, 185, 129)'],
                        borderWidth: 2
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value) => value + '%'
                        }
                    }
                }
            });

            if (isDashboard) {
                dashboardGravityChart = chart;
            } else {
                gravityChart = chart;
            }
        }

        function createDepartmentChart(filtered, canvasId, isDashboard = false) {
            const deptCounts = {
                technique: filtered.filter(a => a.departement === 'technique').length,
                logistique: filtered.filter(a => a.departement === 'logistique').length,
                commercial: filtered.filter(a => a.departement === 'commercial').length,
                administratif: filtered.filter(a => a.departement === 'administratif').length
            };
            
            const total = Object.values(deptCounts).reduce((sum, count) => sum + count, 0);
            const percentages = {
                technique: total > 0 ? Math.round((deptCounts.technique / total) * 100) : 0,
                logistique: total > 0 ? Math.round((deptCounts.logistique / total) * 100) : 0,
                commercial: total > 0 ? Math.round((deptCounts.commercial / total) * 100) : 0,
                administratif: total > 0 ? Math.round((deptCounts.administratif / total) * 100) : 0
            };
            
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            
            const existingChart = isDashboard ? dashboardDepartmentChart : departmentChart;
            if (existingChart) existingChart.destroy();
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Technique', 'Logistique', 'Commercial', 'Administratif'],
                    datasets: [{
                        label: 'Pourcentage d\'anomalies',
                        data: [percentages.technique, percentages.logistique, percentages.commercial, percentages.administratif],
                        backgroundColor: [
                            'rgba(4, 120, 87, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderColor: [
                            'rgb(4, 120, 87)',
                            'rgb(245, 158, 11)',
                            'rgb(59, 130, 246)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const dept = Object.keys(deptCounts)[context.dataIndex];
                                    return `${context.parsed.y}% (${deptCounts[dept]} anomalies)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Pourcentage (%)' }
                        },
                        x: {
                            title: { display: true, text: 'Départements' }
                        }
                    }
                }
            });

            if (isDashboard) {
                dashboardDepartmentChart = chart;
            } else {
                departmentChart = chart;
            }
        }

        // ========== EMAIL ==========
        function sendReportByEmail(reportData) {
            const openAnomalies = reportData.filtered.filter(a => a.status === 'Ouverte');
            const closedAnomalies = reportData.filtered.filter(a => a.status === 'Clos');

            const formatTable = (anomalies) => {
                if (!anomalies.length) return 'Aucune pour cette période.';
                let table = 'ID         | Description                          | Gravité\n';
                table += '--------------|--------------------------------------|--------\n';
                anomalies.forEach(a => {
                    const id = a.id.slice(0,12).padEnd(13);
                    const desc = a.description.slice(0,36).padEnd(38);
                    const gravite = a.statut_anomalie.padEnd(6);
                    table += `${id}| ${desc}| ${gravite}\n`;
                });
                return table;
            };

            const openTable = formatTable(openAnomalies);
            const closedTable = formatTable(closedAnomalies);

            const subject = `Rapport HSE Mensuel - ${reportData.period}`;
            const body = `Cher Directeur,

Veuillez trouver ci-joint le rapport de remontée d'anomalie mensuel pour la période : ${reportData.period}.

=== RÉSUMÉ DES STATISTIQUES ===
- Total d'anomalies traitées : ${reportData.total}
- Anomalies non corrigées (ouvertes) : ${reportData.open}
- Anomalies corrigées (clôturées) : ${reportData.closed}

=== RÉPARTITION PAR NIVEAU DE GRAVITÉ ===
- Arrêt Imminent : ${reportData.arret}%
- Précaution : ${reportData.precaution}%
- Continuer : ${reportData.continuer}%

=== DÉTAILS DES ANOMALIES NON CORRIGÉES (${reportData.open}) ===
${openTable}

=== DÉTAILS DES ANOMALIES CORRIGÉES (${reportData.closed}) ===
${closedTable}


Cordialement,
${currentUser.name}
Responsable HSE - ERES-TOGO`;

            window.location.href = `mailto:${params.email}?cc=${params.email_cc}&subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            showToast('Rapport envoyé par email avec succès', 'success');
        }

        // ========== RENDER DASHBOARD ==========
        function renderDashboard() {
            const total = anomalies.length;
            const open = anomalies.filter(a => a.status === 'Ouverte').length;
            const closed = total - open;
            const totalProposals = anomalies.reduce((sum, a) => sum + (a.proposals?.length || 0), 0);
            
            document.getElementById('dashboardTotalAnomalies').textContent = total;
            document.getElementById('dashboardOpenAnomalies').textContent = open;
            document.getElementById('dashboardClosedAnomalies').textContent = closed;
            document.getElementById('dashboardTotalProposals').textContent = totalProposals;

            createGravityChart(anomalies, 'dashboardGravityChart', true);
            createDepartmentChart(anomalies, 'dashboardDepartmentChart', true);
        }

        // ========== RENDER ANOMALIES ==========
        let filteredAnomalies = [];

        function renderAnomalies() {
            const tbody = document.getElementById('anomaliesTableBody');
            const statusFilter = document.getElementById('filterStatus').value;
            const priorityFilter = document.getElementById('filterPriority').value;
            const searchDept = document.getElementById('searchDepartment').value.toLowerCase();
            const searchDate = document.getElementById('searchDate').value;
            
            filteredAnomalies = anomalies.filter(a => {
                const statusMatch = statusFilter === 'all' || a.status === statusFilter;
                const priorityMatch = priorityFilter === 'all' || a.statut_anomalie === priorityFilter;
                const deptMatch = !searchDept || a.departement.toLowerCase().includes(searchDept);
                const dateMatch = !searchDate || new Date(a.datetime).toISOString().split('T')[0] === searchDate;
                return statusMatch && priorityMatch && deptMatch && dateMatch;
            });
            
            tbody.innerHTML = '';
            document.getElementById('anomalyCount').textContent = `(${filteredAnomalies.length})`;
            
            if (!filteredAnomalies.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Aucune anomalie trouvée</td></tr>';
                return;
            }
            
            filteredAnomalies.forEach((a) => {
                const priorityClass = a.statut_anomalie === 'arret' ? 'badge-arret' : 
                                    a.statut_anomalie === 'precaution' ? 'badge-precaution' : 'badge-continuer';
                const statusClass = a.status === 'Clos' ? 'badge-closed' : 'badge-open';
                const priorityText = a.statut_anomalie === 'arret' ? '🚨 Arrêt' : 
                                    a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
                const hasProposal = a.proposals && a.proposals.length > 0;
                
                const tr = document.createElement('tr');
                if (!a.read) tr.classList.add('unread');
                
                tr.innerHTML = `
                    <td><strong>${a.id.slice(0, 12)}</strong></td>
                    <td>${formatDateTime(a.datetime)}</td>
                    <td>${a.rapporte_par}</td>
                    <td>${a.departement}</td>
                    <td>${a.localisation}</td>
                    <td style="text-align: center;"><span class="badge ${priorityClass}">${priorityText}</span></td>
                    <td style="text-align: center;"><span class="badge ${statusClass}">${a.status}</span></td>
                    <td style="text-align: center;">
                        <button class="btn btn-info btn-sm btn-view-anomaly" data-id="${a.id}">👁️</button>
                        ${a.status !== 'Clos' ? `
                            <button class="btn btn-primary btn-sm btn-propose-action" data-id="${a.id}" ${hasProposal ? 'disabled title="Action déjà proposée"' : ''}>📝</button>
                            <button class="btn btn-success btn-sm btn-close-anomaly" data-id="${a.id}" ${!hasProposal ? 'disabled title="Proposition d\'action requise avant clôture"' : ''}>✓</button>
                        ` : ''}
                        <button class="btn btn-danger btn-sm btn-delete-anomaly" data-id="${a.id}">🗑️</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            updateNotifications();
        }

        // ========== VIEW ANOMALY DETAILS ==========
        function viewAnomalyDetails(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (!anomaly) return;
            
            if (!anomaly.read) {
                anomaly.read = true;
                store.saveAnomalies(anomalies);
                updateNotifications();
            }
            
            const priorityText = anomaly.statut_anomalie === 'arret' ? '🚨 Arrêt Imminent' : 
                                anomaly.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
            const priorityClass = anomaly.statut_anomalie === 'arret' ? 'badge-arret' : 
                                anomaly.statut_anomalie === 'precaution' ? 'badge-precaution' : 'badge-continuer';
            const hasProposal = anomaly.proposals && anomaly.proposals.length > 0;
            
            let proposalsHtml = '';
            if (hasProposal) {
                proposalsHtml = `
                    <div class="detail-full">
                        <label>Propositions d'actions</label>
                        <div class="value">
                            ${anomaly.proposals.map(p => `
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
                        <label>ID Anomalie</label>
                        <div class="value">${anomaly.id.slice(0, 12)}</div>
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
                
                ${anomaly.preuve_url ? `<div class="detail-full"><label>Preuve</label><img src="${anomaly.preuve_url}" class="proof-image"></div>` : '<div class="detail-full"><label>Preuve</label><div class="value">Aucune preuve</div></div>'}
                
                ${proposalsHtml}
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    ${anomaly.status !== 'Clos' ? `
                        ${!hasProposal ? `<button class="btn btn-primary" onclick="proposeActionFromModal('${anomaly.id}')">📝 Proposer action</button>` : ''}
                        ${hasProposal ? `<button class="btn btn-success" onclick="closeAnomalyFromModal('${anomaly.id}')">✓ Clôturer</button>` : ''}
                    ` : '<div class="badge badge-closed" style="padding: 0.5rem 1rem;">Clôturée</div>'}
                    <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
                </div>
            `;
            
            document.getElementById('anomalyModal').classList.add('active');
        }

        window.proposeActionFromModal = function(id) {
            openProposalModal(id);
        };

        window.closeAnomalyFromModal = function(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (anomaly && anomaly.proposals && anomaly.proposals.length > 0) {
                anomaly.status = 'Clos';
                store.saveAnomalies(anomalies);
                closeModal();
                renderAnomalies();
                renderProposals();
                renderDashboard();
                showToast('Anomalie clôturée !', 'success');
            } else {
                showToast('Une proposition d\'action est requise avant clôture', 'warning');
            }
        };

        window.closeModal = function() {
            document.getElementById('anomalyModal').classList.remove('active');
        };

        // ========== PROPOSAL MODAL ==========
        function openProposalModal(anomalyId) {
            const anomaly = anomalies.find(a => a.id === anomalyId);
            if (!anomaly) return;
            
            document.getElementById('proposal_anomaly_id').value = anomalyId;
            document.getElementById('proposal_received').value = formatDateTime(anomaly.datetime);
            document.getElementById('proposal_action').value = '';
            document.getElementById('proposal_person').value = '';
            document.getElementById('proposal_date').value = '';
            setMinDateForProposals();
            document.getElementById('proposalModal').classList.add('active');
        }

        document.getElementById('closeProposalModal').addEventListener('click', () => {
            document.getElementById('proposalModal').classList.remove('active');
        });

        document.getElementById('proposalModal').addEventListener('click', (e) => {
            if (e.target.id === 'proposalModal') document.getElementById('proposalModal').classList.remove('active');
        });

        document.getElementById('addProposalBtn').addEventListener('click', () => {
            const anomalyId = document.getElementById('proposal_anomaly_id').value;
            const anomaly = anomalies.find(a => a.id === anomalyId);
            if (!anomaly) return;
            
            const action = document.getElementById('proposal_action').value.trim();
            const person = document.getElementById('proposal_person').value.trim();
            const date = document.getElementById('proposal_date').value;
            
            if (!action || !person || !date) {
                showToast('Veuillez remplir tous les champs obligatoires (*)', 'warning');
                return;
            }
            
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showToast('La date prévue ne peut pas être antérieure à aujourd\'hui', 'warning');
                return;
            }
            
            const newProposal = {
                id: generateId('prop'),
                received_at: formatDateTime(anomaly.datetime),
                action,
                person,
                date,
                status: 'Proposée'
            };
            
            if (!anomaly.proposals) anomaly.proposals = [];
            anomaly.proposals.push(newProposal);
            
            store.saveAnomalies(anomalies);
            document.getElementById('proposalModal').classList.remove('active');
            
            document.getElementById('proposal_action').value = '';
            document.getElementById('proposal_person').value = '';
            document.getElementById('proposal_date').value = '';
            
            renderAnomalies();
            renderProposals();
            renderDashboard();
            showToast('Proposition ajoutée avec succès !', 'success');
        });

        // ========== RENDER PROPOSALS ==========
        function renderProposals() {
            const tbody = document.getElementById('proposalsTableBody');
            tbody.innerHTML = '';
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id, anomaly_desc: a.description.slice(0, 50) + '...' })));
            
            if (!allProposals.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state">Aucune proposition</td></tr>';
                return;
            }
            
            allProposals.forEach((p) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${p.anomaly_id.slice(0,12)}</strong><br><small>${p.anomaly_desc}</small></td>
                    <td><strong>${p.received_at}</strong></td>
                    <td><input data-id="${p.id}" data-field="action" class="form-control" value="${p.action || ''}"></td>
                    <td><input data-id="${p.id}" data-field="person" class="form-control" value="${p.person || ''}"></td>
                    <td><input data-id="${p.id}" data-field="date" type="date" class="form-control" value="${p.date || ''}" min="${new Date().toISOString().split('T')[0]}"></td>
                    <td style="text-align: center;"><span class="badge badge-proposed">${p.status || 'Proposée'}</span></td>
                    <td style="text-align: center;">
                        <button class="btn btn-warning btn-sm btn-save-proposal" data-id="${p.id}">💾</button>
                        <button class="btn btn-danger btn-sm btn-delete-proposal" data-id="${p.id}">🗑️</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            tbody.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                
                if (e.target.matches('.btn-delete-proposal')) {
                    if (confirm('Supprimer cette proposition ?')) {
                        deleteProposal(id);
                    }
                } else if (e.target.matches('.btn-save-proposal')) {
                    const inputs = document.querySelectorAll(`[data-id="${id}"]`);
                    let updated = false;
                    anomalies.forEach(a => {
                        if (a.proposals) {
                            const prop = a.proposals.find(pr => pr.id === id);
                            if (prop) {
                                inputs.forEach(inp => {
                                    const field = inp.dataset.field;
                                    if (field) prop[field] = inp.value;
                                });
                                updated = true;
                            }
                        }
                    });
                    if (updated) {
                        store.saveAnomalies(anomalies);
                        showToast('Proposition mise à jour !', 'success');
                    }
                }
            });
            
            tbody.addEventListener('change', (e) => {
                const el = e.target;
                const id = el.dataset.id;
                const field = el.dataset.field;
                
                if (id && field && field === 'date') {
                    const selectedDate = new Date(el.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        showToast('La date prévue ne peut pas être antérieure à aujourd\'ui.', 'warning');
                        el.value = '';
                        return;
                    }
                }
                
                if (id && field) {
                    let updated = false;
                    anomalies.forEach(a => {
                        if (a.proposals) {
                            const prop = a.proposals.find(pr => pr.id === id);
                            if (prop) {
                                prop[field] = el.value;
                                updated = true;
                            }
                        }
                    });
                    if (updated) store.saveAnomalies(anomalies);
                }
            });
        }

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

        // ========== ANOMALIES ACTIONS ==========
        document.addEventListener('click', (e) => {
            const id = e.target.dataset.id;
            
            if (e.target.matches('.btn-view-anomaly')) {
                viewAnomalyDetails(id);
            } else if (e.target.matches('.btn-propose-action')) {
                openProposalModal(id);
            } else if (e.target.matches('.btn-close-anomaly')) {
                const anomaly = anomalies.find(a => a.id === id);
                if (anomaly && anomaly.proposals && anomaly.proposals.length > 0) {
                    if (confirm('Clôturer cette anomalie ?')) {
                        anomaly.status = 'Clos';
                        store.saveAnomalies(anomalies);
                        renderAnomalies();
                        renderProposals();
                        renderDashboard();
                        showToast('Anomalie clôturée !', 'success');
                    }
                } else {
                    showToast('Une proposition d\'action est requise avant clôture', 'warning');
                }
            } else if (e.target.matches('.btn-delete-anomaly')) {
                deleteAnomaly(id);
            }
        });

        document.getElementById('filterStatus').addEventListener('change', renderAnomalies);
        document.getElementById('filterPriority').addEventListener('change', renderAnomalies);
        document.getElementById('searchDepartment').addEventListener('input', renderAnomalies);
        document.getElementById('searchDate').addEventListener('change', renderAnomalies);

        document.getElementById('markAllAsRead').addEventListener('click', () => {
            anomalies.forEach(a => a.read = true);
            store.saveAnomalies(anomalies);
            renderAnomalies();
            showToast('Toutes les anomalies ont été marquées comme lues', 'success');
        });

        // ========== EXPORTS ==========
        document.getElementById('exportAnomaliesCsv').addEventListener('click', () => {
            if (!filteredAnomalies.length) {
                showToast('Aucune anomalie à exporter', 'warning');
                return;
            }
            const rows = filteredAnomalies.map(a => ({
                id: a.id,
                datetime: formatDateTime(a.datetime),
                rapporte_par: a.rapporte_par,
                departement: a.departement,
                localisation: a.localisation,
                gravite: a.statut_anomalie,
                description: a.description,
                action: a.action,
                status: a.status
            }));
            downloadCSV('anomalies_eres_togo.csv', rows, ['id','datetime','rapporte_par','departement','localisation','gravite','description','action','status']);
        });

        document.getElementById('exportAnomaliesPdf').addEventListener('click', () => {
            if (!filteredAnomalies.length) {
                showToast('Aucune anomalie à exporter', 'warning');
                return;
            }
            const html = `
                <h1>📋 Liste des Anomalies</h1>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.length}</div>
                        <div class="stat-label">Total anomalies</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.filter(a => a.status === 'Ouverte').length}</div>
                        <div class="stat-label">Anomalies ouvertes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${filteredAnomalies.filter(a => a.status === 'Clos').length}</div>
                        <div class="stat-label">Anomalies clôturées</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Date/Heure</th><th>Rapporté par</th><th>Département</th><th>Localisation</th><th>Gravité</th><th>Statut</th></tr>
                    </thead>
                    <tbody>
                        ${filteredAnomalies.map(a => `<tr>
                            <td>${a.id.slice(0,12)}</td>
                            <td>${formatDateTime(a.datetime)}</td>
                            <td>${a.rapporte_par}</td>
                            <td>${a.departement}</td>
                            <td>${a.localisation}</td>
                            <td>${a.statut_anomalie === 'arret' ? '🚨 Arrêt' : a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td>
                            <td>${a.status}</td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            `;
            openPrintable('Rapport Anomalies', html);
        });

        document.getElementById('exportProposalsCsv').addEventListener('click', () => {
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id })));
            if (!allProposals.length) {
                showToast('Aucune proposition à exporter', 'warning');
                return;
            }
            const rows = allProposals.map(p => ({
                anomaly_id: p.anomaly_id,
                received_at: p.received_at,
                action: p.action,
                person: p.person,
                date: p.date,
                status: p.status
            }));
            downloadCSV('propositions_actions_eres_togo.csv', rows, ['anomaly_id','received_at','action','person','date','status']);
        });

        document.getElementById('exportProposalsPdf').addEventListener('click', () => {
            const allProposals = anomalies.flatMap(a => (a.proposals || []).map(p => ({ ...p, anomaly_id: a.id })));
            if (!allProposals.length) {
                showToast('Aucune proposition à exporter', 'warning');
                return;
            }
            const html = `
                <h1>📝 Propositions d'Actions Correctrices</h1>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.length}</div>
                        <div class="stat-label">Total propositions</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.filter(p => p.status === 'Proposée').length}</div>
                        <div class="stat-label">En attente</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${allProposals.filter(p => p.status === 'Terminée').length}</div>
                        <div class="stat-label">Terminées</div>
                    </div>
                </div>
                <table>
                    <thead><tr><th>Anomalie ID</th><th>Date & heure réception</th><th>Action</th><th>Personne</th><th>Date prévue</th><th>Statut</th></tr></thead>
                    <tbody>${allProposals.map(p => `<tr><td>${p.anomaly_id.slice(0,12)}</td><td>${p.received_at}</td><td>${p.action}</td><td>${p.person}</td><td>${p.date}</td><td>${p.status}</td></tr>`).join('')}</tbody>
                </table>
            `;
            openPrintable('Propositions Actions', html);
        });

        // ========== REPORTS ==========
        let currentReportData = null;

        document.getElementById('generateReport').addEventListener('click', () => {
            const month = document.getElementById('reportMonth').value;
            const year = document.getElementById('reportYear').value;
            
            const filtered = anomalies.filter(a => {
                if (month === 'all' && year === 'all') return true;
                const d = new Date(a.datetime);
                const monthMatch = (month === 'all') || (d.getMonth() + 1 === Number(month));
                const yearMatch = (year === 'all') || (d.getFullYear() === Number(year));
                return monthMatch && yearMatch;
            });
            
            const total = filtered.length;
            const closed = filtered.filter(a => a.status === 'Clos').length;
            const open = total - closed;
            const arret = filtered.filter(a => a.statut_anomalie === 'arret').length;
            const precaution = filtered.filter(a => a.statut_anomalie === 'precaution').length;
            const continuer = filtered.filter(a => a.statut_anomalie === 'continuer').length;
            const totalSeverity = arret + precaution + continuer;
            const arretPct = totalSeverity > 0 ? Math.round((arret / totalSeverity) * 100) : 0;
            const precautionPct = totalSeverity > 0 ? Math.round((precaution / totalSeverity) * 100) : 0;
            const continuerPct = totalSeverity > 0 ? Math.round((continuer / totalSeverity) * 100) : 0;
            
            const periodText = month === 'all' && year === 'all' ? 'Toutes périodes' :
                            month === 'all' ? `Année ${year}` :
                            year === 'all' ? monthNames[parseInt(month)-1] :
                            `${monthNames[parseInt(month)-1]} ${year}`;
            
            currentReportData = { period: periodText, total, open, closed, arret: arretPct, precaution: precautionPct, continuer: continuerPct, filtered };
            
            document.getElementById('reportStats').innerHTML = `
                <div class="stat-card">
                    <h4>Total anomalie(s)</h4>
                    <div class="value">${total}</div>
                </div>
                <div class="stat-card warning">
                    <h4>Non corrigé(s)</h4>
                    <div class="value">${open}</div>
                </div>
                <div class="stat-card success">
                    <h4>Corrigé(s)</h4>
                    <div class="value">${closed}</div>
                </div>
            `;
            
            createGravityChart(filtered, 'gravityChartCanvas');
            createDepartmentChart(filtered, 'departmentChartCanvas');
            
            document.getElementById('chartsContainer').style.display = 'block';
            
            document.getElementById('exportReportCsv').onclick = () => {
                const rows = filtered.map(a => ({
                    id: a.id,
                    datetime: formatDateTime(a.datetime),
                    rapporte_par: a.rapporte_par,
                    departement: a.departement,
                    localisation: a.localisation,
                    gravite: a.statut_anomalie,
                    status: a.status
                }));
                downloadCSV(`rapport_hse_${month}_${year}_eres_togo.csv`, rows, ['id','datetime','rapporte_par','departement','localisation','gravite','status']);
            };
            
            document.getElementById('exportReportPdf').onclick = () => {
                const html = `
                    <h1>📈 Rapport d'Activité HSE</h1>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">${total}</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${open}</div>
                            <div class="stat-label">Non corrigés</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${closed}</div>
                            <div class="stat-label">Corrigés</div>
                        </div>
                    </div>
                    <h3>Répartition par gravité</h3>
                    <p>🚨 Arrêt: ${arretPct}% | ⚠️ Précaution: ${precautionPct}% | 🟢 Continuer: ${continuerPct}%</p>
                    <table>
                        <thead><tr><th>ID</th><th>Date/Heure</th><th>Rapporté par</th><th>Département</th><th>Gravité</th><th>Statut</th></tr></thead>
                        <tbody>${filtered.map(a => `<tr><td>${a.id.slice(0,12)}</td><td>${formatDateTime(a.datetime)}</td><td>${a.rapporte_par}</td><td>${a.departement}</td><td>${a.statut_anomalie === 'arret' ? '🚨 Arrêt' : a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td><td>${a.status}</td></tr>`).join('')}</tbody>
                    </table>
                `;
                openPrintable('Rapport HSE', html);
            };
        });

        document.getElementById('toggleCharts').addEventListener('click', () => {
            const chartsContainer = document.getElementById('chartsContainer');
            const btn = document.getElementById('toggleCharts');
            
            if (chartsContainer.style.display === 'none' || !chartsContainer.style.display) {
                if (currentReportData) {
                    chartsContainer.style.display = 'block';
                    btn.textContent = '📊 Masquer diagrammes';
                } else {
                    showToast('Veuillez d\'abord générer un rapport', 'warning');
                }
            } else {
                chartsContainer.style.display = 'none';
                btn.textContent = '📊 Afficher diagrammes';
            }
        });

        document.getElementById('sendReportEmail').addEventListener('click', () => {
            if (!currentReportData) {
                showToast('Veuillez d\'abord générer un rapport', 'warning');
                return;
            }
            sendReportByEmail(currentReportData);
        });

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
        

        // ========== FETCH ANOMALIES FROM DATABASE ==========
async function fetchAnomalies() {
    try {
        const response = await fetch('{{ route('api.anomalies') }}');
        const data = await response.json();
        
        if (data.anomalies) {
            // Convertir les données MySQL en format compatible avec le dashboard
            anomalies = data.anomalies.map(anomalie => ({
                id: 'anom_' + anomalie.id,
                rapporte_par: anomalie.rapporte_par,
                departement: anomalie.departement,
                localisation: anomalie.localisation,
                statut_anomalie: anomalie.statut, // 'statut' dans MySQL devient 'statut_anomalie' dans JS
                description: anomalie.description,
                action: anomalie.action,
                preuve_url: anomalie.preuve ? '/storage/' + anomalie.preuve : null,
                datetime: anomalie.datetime,
                status: anomalie.status || 'Ouverte', // Valeur par défaut
                read: anomalie.read ? true : false,
                has_proposal: anomalie.has_proposal ? true : false,
                proposals: anomalie.proposals || [], // Si vous avez des propositions
                created_at: anomalie.created_at,
                updated_at: anomalie.updated_at
            }));
            
            // Sauvegarder dans localStorage pour la session
            store.saveAnomalies(anomalies);
            
            // Re-rendre les vues
            renderAnomalies();
            renderDashboard();
            updateNotifications();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des anomalies:', error);
        // En cas d'erreur, utiliser les données du localStorage
        const stored = store.load();
        anomalies = stored.anomalies;
    }
}

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
    </script>
</body>
</html>