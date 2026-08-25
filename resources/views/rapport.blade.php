@extends('dash')
@section('content')

<div id="view-reports" class="rp-scope p-4 md:p-6">
    <div class="rp-card">

        <!-- ===== En-tête ===== -->
        <div class="rp-header">
            <div class="rp-header-inner">
                <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="rp-logo">
                <div>
                    <p class="rp-eyebrow">ERESriskalert · Module rapports</p>
                    <h2 class="rp-title">Rapports d'anomalies</h2>
                    <p class="rp-subtitle">Générez, consultez et exportez les rapports de remontée d'anomalies par période et par structure.</p>
                </div>
            </div>
        </div>
        <div class="rp-hazard-strip" aria-hidden="true"></div>

        <div class="rp-body">

            <!-- ===== Filtres ===== -->
            <div class="rp-toolbar">
                <div class="rp-field">
                    <label for="dateType">Type de période</label>
                    <select id="dateType">
                        <option value="month">Par mois</option>
                        <option value="year">Par année</option>
                    </select>
                </div>

                <div class="rp-field" id="monthGroup">
                    <label for="reportMonth">Mois</label>
                    <input id="reportMonth" type="month" name="reportMonth">
                </div>

                <div class="rp-field" id="yearGroup" style="display:none;">
                    <label for="reportYear">Année</label>
                    <select id="reportYear" name="reportYear"></select>
                </div>

                <div class="rp-field">
                    <label for="structureSelect">Structure</label>
                    <select id="structureSelect">
                        <option value="GLOBAL">Global (ERES + RAST)</option>
                        <option value="ERES">ERES</option>
                        <option value="RAST">RAST</option>
                    </select>
                </div>

                <div class="rp-field rp-field-btn">
                    <label class="rp-field-label-ghost">&nbsp;</label>
                    <button id="generateReport" type="button" class="rp-btn rp-btn-primary">
                        <span class="rp-btn-label">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                            Générer le rapport
                        </span>
                    </button>
                </div>
            </div>

            <!-- Zone pour les boutons d'export (créés dynamiquement) -->
            <div id="exportButtonsContainer" class="rp-export-row"></div>

            <!-- ===== Résultats ===== -->
            <div id="reportResult" class="rp-results hidden">

                <div id="reportStats" class="rp-stats-grid"></div>

                <div class="rp-charts-grid">
                    <div class="rp-chart-card">
                        <h3 id="mainChartTitle">Statistiques visuelles</h3>
                        <canvas id="reportChart" height="80"></canvas>
                    </div>

                    <div id="horizontalChartContainer" class="rp-chart-card">
                        <h3>Totaux anomalies ouvertes vs clôturées</h3>
                        <canvas id="reportChartHorizontal" height="120"></canvas>
                    </div>
                </div>

                <div class="rp-table-card">
                    <div class="rp-table-card-head">
                        <h3>Liste des anomalies</h3>
                        <span id="anomaliesCount" class="rp-table-count"></span>
                    </div>
                    <div class="rp-table-scroll">
                        <table class="rp-table">
                            <thead>
                                <tr>
                                    <th class="rp-col-num">N°</th>
                                    <th>Rapporté par</th>
                                    <th>Description</th>
                                    <th>Localisation</th>
                                    <th>Gravité</th>
                                    <th>Département</th>
                                    <th>Structure</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="anomaliesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dépendances -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

<style>
    /* ===================================================================
       ERESriskalert — Design tokens (scopé à #view-reports)
       Palette "sécurité industrielle" : ardoise + ambre d'alerte,
       vert/ambre/rouge réservés strictement à la sémantique gravité/statut.
       =================================================================== */
    .rp-scope {
        --rp-ink:            #0f121f;
        --rp-ink-soft:       #55627a;
        --rp-slate-900:      #16253f;
        --rp-slate-800:      #28375a;
        --rp-slate-700:      #22314f;
        --rp-amber:          #d97706;
        --rp-amber-soft:     #fef3c7;
        --rp-red:            #dc2626;
        --rp-red-soft:       #fee2e2;
        --rp-green:          #15803d;
        --rp-green-soft:     #dcfce7;
        --rp-blue:           #2563eb;
        --rp-blue-soft:      #dbeafe;
        --rp-border:         #e2e8f0;
        --rp-bg-page:        #eef1f6;
        --rp-card:           #ffffff;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background: var(--rp-bg-page);
        color: var(--rp-ink);
    }

    .rp-scope .hidden { display: none !important; }

    .rp-card {
        background: var(--rp-card);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -8px rgba(15,23,42,.10);
        border: 1px solid var(--rp-border);
    }

    /* ---------- Header ---------- */
    .rp-header {
        background: linear-gradient(135deg, var(--rp-slate-900) 0%, var(--rp-slate-700) 100%);
        padding: 28px 32px;
    }
    .rp-header-inner {
        display: flex;
        align-items: center;
        gap: 18px;
        max-width: 980px;
        margin: 0 auto;
    }
    .rp-logo {
        height: 42px;
        width: auto;
        border-radius: 6px;
        background: #fff;
        padding: 4px;
        flex-shrink: 0;
    }
    .rp-eyebrow {
        color: var(--rp-amber);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin: 0 0 4px;
    }
    .rp-title {
        color: #fff;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0 0 4px;
        line-height: 1.2;
    }
    .rp-subtitle {
        color: #b9c3d6;
        font-size: 13.5px;
        margin: 0;
        line-height: 1.5;
    }

    /* Signature : bandeau "hazard stripe" — clin d'œil direct au métier
       (remontée d'anomalies / sécurité) sans être criard. */
    .rp-hazard-strip {
        height: 5px;
        background: repeating-linear-gradient(
            135deg,
            var(--rp-amber) 0px, var(--rp-amber) 14px,
            var(--rp-slate-900) 14px, var(--rp-slate-900) 28px
        );
    }

    .rp-body { padding: 28px 32px 32px; }

    /* ---------- Toolbar / filtres ---------- */
    .rp-toolbar {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 14px;
        align-items: end;
        background: #f8fafc;
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 16px;
    }
    @media (max-width: 900px) {
        .rp-toolbar { grid-template-columns: 1fr 1fr; }
        .rp-field-btn { grid-column: 1 / -1; }
    }
    .rp-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
    .rp-field label {
        font-size: 12px;
        font-weight: 600;
        color: var(--rp-ink-soft);
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .rp-field select,
    .rp-field input {
        width: 100%;
        border: 1px solid var(--rp-border);
        background: #fff;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 14px;
        color: var(--rp-ink);
        transition: border-color .15s, box-shadow .15s;
    }
    .rp-field select:focus,
    .rp-field input:focus {
        outline: none;
        border-color: var(--rp-slate-700);
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    /* ---------- Boutons ---------- */
    .rp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s, transform .05s, opacity .15s;
    }
    .rp-btn:active { transform: translateY(1px); }
    .rp-btn:disabled { opacity: .65; cursor: not-allowed; }
    .rp-btn-primary { background: var(--rp-slate-900); color: #fff; width: 100%; }
    .rp-btn-primary:hover:not(:disabled) { background: var(--rp-slate-700); }
    .rp-btn-label { display: inline-flex; align-items: center; gap: 8px; }

    .rp-btn-export {
        background: #fff;
        color: var(--rp-ink);
        border: 1px solid var(--rp-border);
    }
    .rp-btn-export:hover { background: #f8fafc; border-color: #cbd5e1; }
    .rp-btn-export.rp-btn-csv { color: var(--rp-green); }
    .rp-btn-export.rp-btn-pdf { color: var(--rp-red); }

    .rp-export-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-bottom: 18px;
    }

    /* ---------- Spinner (état de génération) ---------- */
    .rp-spinner {
        width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: rp-spin .7s linear infinite;
        display: inline-block;
    }
    @keyframes rp-spin { to { transform: rotate(360deg); } }

    /* ---------- Stats ---------- */
    .rp-results { margin-top: 8px; }
    .rp-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    @media (max-width: 900px) { .rp-stats-grid { grid-template-columns: 1fr 1fr; } }

    .rp-stat-card {
        background: #fff;
        border: 1px solid var(--rp-border);
        border-left: 4px solid var(--rp-accent, var(--rp-slate-700));
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .rp-stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--rp-ink-soft);
    }
    .rp-stat-value {
        font-size: 24px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        color: var(--rp-ink);
    }
    .rp-stat-value.rp-small { font-size: 15px; font-weight: 700; }

    .rp-users-card { grid-column: span 1; }
    .rp-users-group { display: flex; flex-direction: column; gap: 6px; margin-top: 2px; }
    .rp-users-block-label {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; color: var(--rp-ink-soft);
    }
    .rp-user-pill {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--rp-blue-soft); color: #1e3a8a;
        padding: 3px 9px; border-radius: 999px;
        font-size: 11.5px; font-weight: 600; margin: 2px 4px 0 0;
    }
    .rp-user-pill.rp-rast { background: #ede9fe; color: #5b21b6; }
    .rp-users-empty { font-size: 12.5px; color: var(--rp-ink-soft); text-align: center; padding: 6px 0; }

    /* ---------- Charts ---------- */
    .rp-charts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
        max-width: 760px;
        margin: 0 auto 24px;
    }
    .rp-chart-card {
        background: #fff;
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        padding: 18px;
    }
    .rp-chart-card h3 {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--rp-ink);
        text-align: center;
        margin: 0 0 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    /* ---------- Table ---------- */
    .rp-table-card {
        background: #fff;
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        overflow: hidden;
    }
    .rp-table-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid var(--rp-border);
    }
    .rp-table-card-head h3 {
        font-size: 14.5px; font-weight: 700; margin: 0; color: var(--rp-ink);
    }
    .rp-table-count { font-size: 12.5px; color: var(--rp-ink-soft); font-weight: 600; }

    .rp-table-scroll { overflow-x: auto; max-height: 520px; overflow-y: auto; }
    .rp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rp-table thead th {
        position: sticky; top: 0; z-index: 1;
        background: var(--rp-slate-900);
        color: #e2e8f0;
        font-weight: 600;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding: 10px 12px;
        text-align: left;
        white-space: nowrap;
    }
    .rp-table thead th.rp-col-num { text-align: center; width: 48px; }
    .rp-table tbody td {
        padding: 9px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--rp-ink);
        vertical-align: top;
    }
    .rp-table tbody tr:nth-child(even) { background: #fafbfd; }
    .rp-table tbody tr:hover { background: #f0f5ff; }
    .rp-table tbody td:first-child { text-align: center; color: var(--rp-ink-soft); font-weight: 600; }
    .rp-table-empty-row td {
        text-align: center; padding: 32px 12px; color: var(--rp-ink-soft); font-style: italic;
    }

    /* Badges gravité / statut */
    .rp-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 999px;
        font-size: 11.5px; font-weight: 700;
        white-space: nowrap;
    }
    .rp-badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .rp-badge-green  { background: var(--rp-green-soft);  color: var(--rp-green); }
    .rp-badge-amber  { background: var(--rp-amber-soft);  color: var(--rp-amber); }
    .rp-badge-red    { background: var(--rp-red-soft);    color: var(--rp-red); }
    .rp-badge-gray   { background: #f1f5f9; color: #475569; }

    .rp-structure-tag {
        font-weight: 700;
        color: var(--rp-slate-700);
        background: #eef2f9;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };

    const dateType = document.getElementById('dateType');
    const monthGroup = document.getElementById('monthGroup');
    const yearGroup = document.getElementById('yearGroup');
    const yearSelect = document.getElementById('reportYear');
    const structureSelect = document.getElementById('structureSelect');
    const generateBtn = document.getElementById('generateReport');
    const reportResult = document.getElementById('reportResult');
    const reportStats = document.getElementById('reportStats');
    const anomaliesTableBody = document.getElementById('anomaliesTableBody');
    const anomaliesCount = document.getElementById('anomaliesCount');
    const exportButtonsContainer = document.getElementById('exportButtonsContainer');
    const mainChartTitle = document.getElementById('mainChartTitle');
    const horizontalChartContainer = document.getElementById('horizontalChartContainer');

    let reportChart = null, horizontalChart = null, currentReportData = null;

    // Remplir les années
    const currentYear = new Date().getFullYear();
    for (let y = currentYear - 5; y <= currentYear + 10; y++) {
        yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }
    yearSelect.value = currentYear;

    // Gestion du type de période
    dateType.addEventListener('change', () => {
        monthGroup.style.display = dateType.value === 'month' ? 'flex' : 'none';
        yearGroup.style.display = dateType.value === 'year' ? 'flex' : 'none';
    });

    // ---------- Aides visuelles (badges gravité / statut) ----------
    function normalize(str) {
        return (str || '')
            .toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function gravityBadge(raw) {
        const v = normalize(raw);
        if (!v) return `<span class="rp-badge rp-badge-gray">-</span>`;
        if (/(crit|grav|elev|haut|major)/.test(v)) return `<span class="rp-badge rp-badge-red">${escapeHtml(raw)}</span>`;
        if (/(moy|moder|mid)/.test(v)) return `<span class="rp-badge rp-badge-amber">${escapeHtml(raw)}</span>`;
        if (/(faib|leg|min|low)/.test(v)) return `<span class="rp-badge rp-badge-green">${escapeHtml(raw)}</span>`;
        return `<span class="rp-badge rp-badge-gray">${escapeHtml(raw)}</span>`;
    }

    function statusBadge(raw) {
        const v = normalize(raw);
        if (!v) return `<span class="rp-badge rp-badge-gray">-</span>`;
        if (/(clotur|ferm|resol|closed|termin)/.test(v)) return `<span class="rp-badge rp-badge-green">${escapeHtml(raw)}</span>`;
        if (/(ouvert|open|cours|attente)/.test(v)) return `<span class="rp-badge rp-badge-amber">${escapeHtml(raw)}</span>`;
        return `<span class="rp-badge rp-badge-gray">${escapeHtml(raw)}</span>`;
    }

    // Génération du rapport
    generateBtn.addEventListener('click', () => {
        const type = dateType.value;
        const month = document.getElementById('reportMonth').value;
        const year = yearSelect.value;

        if ((type === 'month' && !month) || (type === 'year' && !year)) {
            toastr.warning('Veuillez sélectionner une période valide.');
            return;
        }

        toastr.info('Génération du rapport en cours...');

        const structure = structureSelect.value;

        generateBtn.disabled = true;
        const originalLabel = generateBtn.innerHTML;
        generateBtn.innerHTML = `<span class="rp-btn-label"><span class="rp-spinner"></span> Génération...</span>`;

        fetch('{{ route("generate.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                type,
                reportMonth: month,
                reportYear: year,
                structure: structure
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) return toastr.error(data.error);
            currentReportData = data;
            displayReport(data, type);
            reportResult.classList.remove('hidden');
            createExportButtons();
            toastr.success('Rapport généré avec succès !');
        })
        .catch(() => toastr.error('Erreur de connexion au serveur.'))
        .finally(() => {
            generateBtn.disabled = false;
            generateBtn.innerHTML = originalLabel;
        });
    });

    function createExportButtons() {
        exportButtonsContainer.innerHTML = '';

        const pdfBtn = document.createElement('button');
        pdfBtn.id = 'exportReportPdf';
        pdfBtn.className = 'rp-btn rp-btn-export rp-btn-pdf';
        pdfBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            Exporter en PDF`;
        pdfBtn.addEventListener('click', exportToPDF);

        const csvBtn = document.createElement('button');
        csvBtn.id = 'exportReportCsv';
        csvBtn.className = 'rp-btn rp-btn-export rp-btn-csv';
        csvBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
            Exporter en CSV`;
        csvBtn.addEventListener('click', exportToCSV);

        exportButtonsContainer.appendChild(pdfBtn);
        exportButtonsContainer.appendChild(csvBtn);
    }

    function displayReport(data, type) {
        const stats = data.statistiques || {};
        const periode = data.periode || {};

        // Mettre à jour le titre du graphique principal
        mainChartTitle.textContent = type === 'year'
            ? 'Statistiques visuelles par année'
            : 'Statistiques visuelles par mois';

        reportStats.innerHTML = `
            <div class="rp-stat-card" style="--rp-accent:#64748b;">
                <span class="rp-stat-label">Période</span>
                <span class="rp-stat-value rp-small">${safeFormatDate(periode.debut)} — ${safeFormatDate(periode.fin)}</span>
            </div>
            <div class="rp-stat-card" style="--rp-accent:#2563eb;">
                <span class="rp-stat-label">Total</span>
                <span class="rp-stat-value">${formatNumber(stats.total)}</span>
            </div>
            <div class="rp-stat-card" style="--rp-accent:#15803d;">
                <span class="rp-stat-label">Clôturées</span>
                <span class="rp-stat-value">${formatNumber(stats.cloturees)}</span>
            </div>
            <div class="rp-stat-card" style="--rp-accent:#d97706;">
                <span class="rp-stat-label">Ouvertes</span>
                <span class="rp-stat-value">${formatNumber(stats.ouvertes)}</span>
            </div>
            <div class="rp-stat-card rp-users-card" style="--rp-accent:#7c3aed; grid-column: 1 / -1;">
                <span class="rp-stat-label">Utilisateurs les plus actifs</span>
                <div class="rp-users-group">
                    ${generateTopUsersHTML(stats)}
                </div>
            </div>
        `;

        const rows = data.data || [];
        anomaliesCount.textContent = `${rows.length} anomalie${rows.length > 1 ? 's' : ''}`;

        if (!rows.length) {
            anomaliesTableBody.innerHTML = `
                <tr class="rp-table-empty-row"><td colspan="8">Aucune anomalie sur la période sélectionnée.</td></tr>
            `;
        } else {
            anomaliesTableBody.innerHTML = rows.map((a, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(a.rapporte_par || '-')}</td>
                    <td>${escapeHtml(a.description)}</td>
                    <td>${escapeHtml(a.localisation || '-')}</td>
                    <td>${gravityBadge(a.gravity)}</td>
                    <td>${escapeHtml(a.departement || '-')}</td>
                    <td><span class="rp-structure-tag">${escapeHtml(a.structure || '-')}</span></td>
                    <td>${statusBadge(a.status)}</td>
                </tr>
            `).join('');
        }

        renderCharts(data, type);
    }

    function safeFormatDate(date) {
        if (!date) return 'Inconnue';
        const d = new Date(date);
        return isNaN(d) ? 'Invalide' : `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }

    function formatNumber(n) {
        const v = Number(n ?? 0);
        return isNaN(v) ? '0' : v.toLocaleString('fr-FR');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function generateTopUsersHTML(stats) {
        let html = '';

        if (stats.utilisateurs_top_eres && stats.utilisateurs_top_eres.length > 0) {
            html += '<div>';
            html += '<div class="rp-users-block-label">ERES</div>';
            stats.utilisateurs_top_eres.slice(0, 2).forEach((user, index) => {
                html += `<span class="rp-user-pill">${index + 1}. ${escapeHtml(user.nom || 'Aucun')} (${user.nombre || 0})</span>`;
            });
            html += '</div>';
        }

        if (stats.utilisateurs_top_rast && stats.utilisateurs_top_rast.length > 0) {
            html += '<div>';
            html += '<div class="rp-users-block-label">RAST</div>';
            stats.utilisateurs_top_rast.slice(0, 2).forEach((user, index) => {
                html += `<span class="rp-user-pill rp-rast">${index + 1}. ${escapeHtml(user.nom || 'Aucun')} (${user.nombre || 0})</span>`;
            });
            html += '</div>';
        }

        if (html === '') {
            html = '<div class="rp-users-empty">Aucun utilisateur actif</div>';
        }

        return html;
    }

    // --- Graphiques (affichés à l'écran) ---
    function renderCharts(data, type) {
        if (reportChart) reportChart.destroy();
        if (horizontalChart) horizontalChart.destroy();

        const ctx1 = document.getElementById('reportChart').getContext('2d');
        const ctx2 = document.getElementById('reportChartHorizontal').getContext('2d');
        Chart.register(ChartDataLabels);

        // Gestion de l'affichage du graphique horizontal
        if (type === 'year') {
            horizontalChartContainer.style.display = 'none';
        } else {
            horizontalChartContainer.style.display = 'block';
        }

        if (type === 'month') {
            const labels = Object.keys(data.statistiques?.par_gravite || {});
            const values = Object.values(data.statistiques?.par_gravite || {});
            const total = values.reduce((a,b) => a + b, 0);
            reportChart = new Chart(ctx1, {
                type: 'doughnut',
                data: { labels, datasets: [{ data: values, backgroundColor: ['#15803d','#dc2626','#d97706'] }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom' }, datalabels: { color: '#fff', formatter: val => total ? ((val/total)*100).toFixed(1)+'%' : '' } } },
                plugins: [ChartDataLabels]
            });
        } else {
            const months = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
            const totalAnomalies = data.statistiques?.mensuel || Array(12).fill(0);
            const anomaliesCloturees = data.statistiques?.mensuel_cloturees || Array(12).fill(0);

            reportChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Total Anomalies',
                            data: totalAnomalies,
                            backgroundColor: '#dc2626',
                            borderRadius: 5
                        },
                        {
                            label: 'Anomalies Clôturées',
                            data: anomaliesCloturees,
                            backgroundColor: '#15803d',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            formatter: function(value) {
                                return value > 0 ? value : '';
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        if (type === 'month') {
            const labelsH = ['Totaux du mois'];
            const ouvertes = [data.statistiques?.ouvertes || 0];
            const cloturees = [data.statistiques?.cloturees || 0];

            const cumulOuvertes = [];
            const cumulCloturees = [];
            let totalCumul = 0;
            let cloturesCumul = 0;

            for (let i = 0; i < labelsH.length; i++) {
                totalCumul += (ouvertes[i] + cloturees[i]);
                cloturesCumul += cloturees[i];

                cumulOuvertes.push(totalCumul);
                cumulCloturees.push(cloturesCumul);
            }

            const percentCumulCloturees = cumulCloturees.map((val, i) =>
                cumulOuvertes[i] > 0 ? Math.round((val / cumulOuvertes[i]) * 100) : 0
            );

            horizontalChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labelsH,
                    datasets: [
                        {
                            label: 'Total Anomalies',
                            data: cumulOuvertes,
                            backgroundColor: '#dc2626',
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            borderRadius: 10,
                            borderSkipped: false
                        },
                        {
                            label: 'Anomalies Clôturées',
                            data: cumulCloturees,
                            backgroundColor: '#15803d',
                            borderColor: '#15803d',
                            borderWidth: 1,
                            borderRadius: 10,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    categoryPercentage: 0.1,
                    barPercentage: 0.80,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: "Nombre d'anomalies (Cumulé)" }
                        },
                        x: {
                            title: { display: true, text: 'Période' }
                        }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const i = ctx.dataIndex;
                                    const val = ctx.raw;
                                    if (ctx.dataset.label === 'Total Anomalies') {
                                        return `Total: ${val} anomalies`;
                                    } else {
                                        const pct = percentCumulCloturees[i];
                                        return `Clôturées: ${val} (${pct}%)`;
                                    }
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            formatter: (val, ctx) => {
                                const i = ctx.dataIndex;
                                if (ctx.dataset.label === 'Total Anomalies') {
                                    return val > 0 ? `${val}` : '';
                                } else {
                                    const pct = percentCumulCloturees[i];
                                    return val > 0 ? `${val} (${pct}%)` : '';
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    }

    function exportToPDF(){

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p','mm','a4');

        const pageWidth = doc.internal.pageSize.getWidth();
        let y = 22;

        const periode = currentReportData.periode || {};
        const stats = currentReportData.statistiques || {};
        const selectedStructure = document.getElementById('structureSelect').value;

        /* ================= HEADER ================= */

        const logo = new Image();
        logo.src = '{{ asset('img/ERES.jpg') }}';

        logo.onload = () => {

            doc.addImage(logo,'JPG',10,8,25,12);

            doc.setFontSize(14);
            doc.setFont(undefined,'bold');
            doc.text("Rapport de remontée d'anomalies",pageWidth/2,12,{align:'center'});

            doc.setFontSize(9);
            doc.setFont(undefined,'normal');

            doc.text(
                `Période : ${safeFormatDate(periode.debut)} - ${safeFormatDate(periode.fin)}`,
                pageWidth/2,
                17,
                {align:'center'}
            );

            doc.text(`Structure : ${selectedStructure}`,pageWidth/2,21,{align:'center'});

            y = 28;

            /* ================= STATISTIQUES ================= */

            doc.setFontSize(9);

            doc.text(`Total : ${stats.total ?? 0}`,15,y);
            doc.text(`Ouvertes : ${stats.ouvertes ?? 0}`,60,y);
            doc.text(`Clôturées : ${stats.cloturees ?? 0}`,110,y);

            y += 6;

            /* ================= TABLE DATA ================= */

            const tableData = (currentReportData.data || []).map((a,i)=>[

                i+1,
                (a.rapporte_par || '-').substring(0,15),
                (a.description || '-').substring(0,35),
                (a.localisation || '-').substring(0,15),
                a.gravity || '-',
                (a.departement || '-').substring(0,15),
                a.structure || '-',
                a.status || '-'

            ]);

            /* ================= TABLEAU ================= */

            doc.autoTable({

                startY:y,

                head:[[
                    'N°',
                    'Rapporté par',
                    'Description',
                    'Localisation',
                    'Gravité',
                    'Département',
                    'Structure',
                    'Statut'
                ]],

                body:tableData,

                theme:'grid',

                styles:{
                    fontSize:7,
                    cellPadding:1.5,
                    valign:'middle'
                },

                headStyles:{
                    fillColor:[11,21,38],
                    textColor:255,
                    halign:'center',
                    fontStyle:'bold'
                },

                columnStyles:{
                    0:{cellWidth:8,halign:'center'},
                    1:{cellWidth:25},
                    2:{cellWidth:45},
                    3:{cellWidth:25},
                    4:{cellWidth:15,halign:'center'},
                    5:{cellWidth:25},
                    6:{cellWidth:15,halign:'center'},
                    7:{cellWidth:15,halign:'center'}
                },

                margin:{left:8,right:8,bottom:16},

                pageBreak:'auto'

            });

            /* ================= FOOTER (dessiné sur chaque page une fois le total connu) ================= */
            const pageCount = doc.internal.getNumberOfPages();
            for (let p = 1; p <= pageCount; p++) {
                doc.setPage(p);
                doc.setFontSize(7);

                doc.text(
                    `Rapport généré le ${new Date().toLocaleDateString()}`,
                    10,
                    290
                );

                doc.text(
                    "ERES Risk Alert",
                    pageWidth-40,
                    290
                );

                doc.text(
                    `Page ${p} / ${pageCount}`,
                    pageWidth/2,
                    290,
                    {align:'center'}
                );
            }

            doc.save(`rapport_anomalies_${new Date().toISOString().slice(0,10)}.pdf`);

            toastr.success("PDF exporté avec succès");

        };

    }

    function exportToCSV() {
        if (!currentReportData?.data?.length) {
            toastr.error('Aucune donnée à exporter.');
            return;
        }

        let csv = '\uFEFFNuméro;Rapporté par;Description;Localisation;Gravité;Département;Structure;Statut\n';

        currentReportData.data.forEach((a, i) => {
            csv += `${i+1};"${a.rapporte_par || ''}";"${(a.description || '').replace(/"/g, '""')}";"${a.localisation || ''}";"${a.gravity || ''}";"${a.departement || ''}";"${a.structure || ''}";"${a.status || ''}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = `rapport_remontee_anomalies_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();

        URL.revokeObjectURL(url);
        toastr.success('CSV exporté avec succès !');
    }

});
</script>

@endsection
