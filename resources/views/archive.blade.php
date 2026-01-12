@extends('dash')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div id="view-archive" class="">
    <div class="page-header flex flex-col items-center mb-6">
        <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="h-10 w-auto mb-3">
        <h1>Archives ERESriskalert</h1>
        <p>Anomalies clôturées et archivées</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Liste des anomalies archivées</h2>
            <div class="btn-group">
                <button class="btn btn-primary btn-sm" id="exportArchivesCsv">Exporter CSV</button>
                <button class="btn btn-info btn-sm" id="exportArchivesPdf">Exporter PDF</button>
            </div>
        </div>

        <div class="table-container">
            <table class="w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left font-semibold">N°</th>
                        <th class="border px-4 py-3 text-left font-semibold">Date anomalie</th>
                        <th class="border px-4 py-3 text-left font-semibold">Rapporté par</th>
                        <th class="border px-4 py-3 text-left font-semibold">Département</th>
                        <th class="border px-4 py-3 text-left font-semibold">Gravité</th>
                        <th class="border px-4 py-3 text-left font-semibold">Date clôture</th>
                        <th class="border px-4 py-3 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="archivesTableBody">
                    <tr><td colspan="7" class="text-center py-8 text-gray-500">Chargement...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center gap-3 mt-6"></div>
    </div>
</div>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- jsPDF + autoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    window.routes = {
        closedAnomalies: "{{ route('anomalies.closed') }}"
    };
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const { jsPDF } = window.jspdf;
    const tbody = document.getElementById('archivesTableBody');
    const paginationDiv = document.getElementById('pagination');

    function loadArchives(page = 1) {
        fetch(`${window.routes.closedAnomalies}?page=${page}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                tbody.innerHTML = '';
                if (!data.anomalies || data.anomalies.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-8 text-gray-500">Aucune anomalie clôturée.</td></tr>`;
                    paginationDiv.innerHTML = '';
                    return;
                }

                data.anomalies.forEach(anomaly => {
                    const row = document.createElement('tr');
                    row.id = `archive-${anomaly.id}`;
                    row.classList.add('hover:bg-gray-50', 'transition-colors');

                    const dateAnomalie = new Date(anomaly.created_at).toLocaleDateString('fr-FR');
                    const dateCloture = anomaly.updated_at ? new Date(anomaly.updated_at).toLocaleDateString('fr-FR') : '-';

                    // Déterminer la couleur de la gravité
                    const gravityColors = {
                        'Faible': 'bg-green-100 text-green-800 border border-green-200',
                        'Moyenne': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                        'Haute': 'bg-orange-100 text-orange-800 border border-orange-200',
                        'Critique': 'bg-red-100 text-red-800 border border-red-200'
                    };
                    const gravityClass = gravityColors[anomaly.gravity] || 'bg-gray-100 text-gray-800 border border-gray-200';

                    row.innerHTML = `
                        <td class="border px-4 py-3 font-medium text-gray-900">${anomaly.id}</td>
                        <td class="border px-4 py-3 text-gray-700">${dateAnomalie}</td>
                        <td class="border px-4 py-3 text-gray-700">${anomaly.rapporte_par}</td>
                        <td class="border px-4 py-3 text-gray-700">${anomaly.departement}</td>
                        <td class="border px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${gravityClass}">
                                <span class="w-2 h-2 rounded-full mr-2 ${anomaly.gravity === 'Faible' ? 'bg-green-500' : anomaly.gravity === 'Moyenne' ? 'bg-yellow-500' : anomaly.gravity === 'Haute' ? 'bg-orange-500' : 'bg-red-500'}"></span>
                                ${anomaly.gravity}
                            </span>
                        </td>
                        <td class="border px-4 py-3 text-gray-700">
                            <span class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 rounded text-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ${dateCloture}
                            </span>
                        </td>
                        <td class="border px-4 py-3 text-center">
                            <button onclick="toggleProposals(${anomaly.id})" 
                                    class="view-details-btn inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir le plan
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);

                    // Ligne cachée des propositions
                    const propRow = document.createElement('tr');
                    propRow.id = `props-${anomaly.id}`;
                    propRow.classList.add('hidden');

                    const hasPropositions = Array.isArray(anomaly.propositions) && anomaly.propositions.length > 0;

                    const propositionsHTML = hasPropositions
                        ? anomaly.propositions.map((p, index) => {
                            const isPastDue = new Date(p.date) < new Date();
                            const dueStatus = isPastDue ? 
                                'bg-red-50 text-red-700 border-red-200' : 
                                'bg-green-50 text-green-700 border-green-200';
                            const statusClass = isPastDue ? 
                                'bg-red-100 text-red-600 border-red-300' : 
                                'bg-green-100 text-green-600 border-green-300';
                            
                            return `
                            <tr class="hover:bg-blue-50 transition-all duration-200 group border-b border-gray-200 last:border-b-0">
                                <td class="px-6 py-6 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <span class="flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-full font-bold text-lg group-hover:bg-blue-200 transition-colors shadow-sm">
                                            ${index + 1}
                                        </span>
                                      
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="bg-white border border-gray-200 rounded-xl p-5 group-hover:shadow-md transition-all duration-200 h-full">
                                        <div class="flex items-start justify-between mb-3">
                                            <h4 class="font-bold text-gray-900 text-base flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Action Corrective
                                            </h4>
                                        </div>
                                        <p class="text-gray-700 text-sm leading-relaxed bg-gray-50 rounded-lg p-3 border border-gray-100">
                                            ${p.action}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="bg-white border border-gray-200 rounded-xl p-5 group-hover:shadow-md transition-all duration-200 h-full">
                                        <h4 class="font-bold text-gray-900 text-base mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Responsable
                                        </h4>
                                        <div class="flex items-center text-gray-700 bg-purple-50 rounded-lg p-3 border border-purple-100">
                                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-gray-900">${p.person}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="bg-white border border-gray-200 rounded-xl p-5 group-hover:shadow-md transition-all duration-200 h-full">
                                        <h4 class="font-bold text-gray-900 text-base mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Date Prévue
                                        </h4>
                                        <div class="flex items-center ${dueStatus} px-4 py-3 rounded-lg border font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm">${new Date(p.date).toLocaleDateString('fr-FR')}</span>
                                        </div>
                                        ${isPastDue ? `
                                        <div class="mt-2 flex items-center text-red-600 text-xs">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Échéance dépassée
                                        </div>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                            `;
                        }).join('')
                        : `<tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500 mb-2">Aucune action corrective enregistrée</p>
                                    <p class="text-sm text-gray-400">Aucune proposition n'a été ajoutée pour cette anomalie</p>
                                </div>
                            </td>
                           </tr>`;

                    propRow.innerHTML = `
                        <td colspan="7" class="px-0 py-0 bg-gradient-to-br from-blue-50 to-indigo-50 border-t-4 border-blue-400">
                            <div class="p-8">
                                <!-- En-tête avec badge de statut -->
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 rounded-2xl shadow-lg">
                                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-1">Plan d'Actions Correctives</h3>
                                            <div class="flex items-center space-x-4">
                                                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold border border-green-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Anomalie Clôturée
                                                </span>
                                                <span class="text-sm text-gray-600 font-medium">#${anomaly.id}</span>
                                                <span class="text-sm text-gray-600">•</span>
                                                <span class="text-sm text-gray-600 font-medium">${anomaly.departement}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="toggleProposals(${anomaly.id})" 
                                            class="flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Réduire
                                    </button>
                                </div>

                                <!-- Tableau des actions correctives -->
                                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                                    <div class="px-8 py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-xl font-bold text-gray-900 flex items-center">
                                                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                                Détail des Actions Correctives
                                            </h4>
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                                ${hasPropositions ? anomaly.propositions.length : '0'} action(s)
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr>
                                                    <th class="px-8 py-5 text-center text-sm font-bold text-gray-700 uppercase tracking-wider w-24">N°</th>
                                                    <th class="px-8 py-5 text-left text-sm font-bold text-gray-700 uppercase tracking-wider w-2/5">Action Corrective</th>
                                                    <th class="px-8 py-5 text-left text-sm font-bold text-gray-700 uppercase tracking-wider w-1/5">Responsable</th>
                                                    <th class="px-8 py-5 text-left text-sm font-bold text-gray-700 uppercase tracking-wider w-1/5">Date Prévue</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                ${propositionsHTML}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Résumé -->
                                ${hasPropositions ? `
                                <div class="mt-6 flex justify-end">
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl px-6 py-4 shadow-lg">
                                        <p class="text-sm font-semibold flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="font-bold">${anomaly.propositions.length}</span> 
                                            action(s) corrective(s) définie(s) pour résoudre cette anomalie
                                        </p>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </td>
                    `;

                    tbody.appendChild(propRow);
                });

                // Pagination
                paginationDiv.innerHTML = `
                    <button ${data.current_page <= 1 ? 'disabled' : ''}
                        class="px-5 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center shadow-sm hover:shadow-md font-medium"
                        onclick="loadArchives(${data.current_page - 1})">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Précédent
                    </button>
                    <span class="font-semibold text-gray-700 mx-4 bg-white px-5 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                        Page <span class="text-blue-600 font-bold text-lg">${data.current_page}</span> sur ${data.last_page}
                    </span>
                    <button ${data.current_page >= data.last_page ? 'disabled' : ''}
                        class="px-5 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center shadow-sm hover:shadow-md font-medium"
                        onclick="loadArchives(${data.current_page + 1})">
                        Suivant
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                `;
            })
            .catch(err => {
                console.error(err);
                toastr.error("Erreur de chargement des archives.");
                tbody.innerHTML = `<tr><td colspan="7" class="text-red-500 text-center py-8">Erreur lors du chargement des données.</td></tr>`;
            });
    }

    // Toggle propositions avec animation
    window.toggleProposals = function(id) {
        const row = document.getElementById(`props-${id}`);
        const btn = document.querySelector(`#archive-${id} .view-details-btn`);
        
        if (row) {
            row.classList.toggle('hidden');
            
            // Animation du bouton
            if (btn) {
                if (row.classList.contains('hidden')) {
                    btn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir le plan
                    `;
                    btn.className = btn.className.replace('from-gray-500 to-gray-600', 'from-blue-500 to-blue-600')
                                                .replace('hover:from-gray-600 hover:to-gray-700', 'hover:from-blue-600 hover:to-blue-700');
                } else {
                    btn.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Masquer
                    `;
                    btn.className = btn.className.replace('from-blue-500 to-blue-600', 'from-gray-500 to-gray-600')
                                                .replace('hover:from-blue-600 hover:to-blue-700', 'hover:from-gray-600 hover:to-gray-700');
                }
            }
        }
    };

    // Les fonctions d'export restent identiques...
    // === EXPORT CSV ===
    document.getElementById('exportArchivesCsv').addEventListener('click', function () {
        fetch(`${window.routes.closedAnomalies}?page=all`)
            .then(res => res.json())
            .then(data => {
                const csv = generateCSV(data.anomalies);
                downloadFile(csv, `archives_anomalies_${new Date().toISOString().slice(0,10)}.csv`, 'text/csv');
                toastr.success('CSV exporté avec succès !');
            })
            .catch(() => toastr.error('Erreur lors de l\'export CSV'));
    });

    function generateCSV(anomalies) {
        const headers = ['ID', 'Date anomalie', 'Rapporté par', 'Département', 'Gravité', 'Date clôture', 'Propositions'];
        const rows = anomalies.map(a => [
            a.id,
            new Date(a.datetime).toLocaleDateString('fr-FR'),
            a.rapporte_par,
            a.departement,
            a.gravity,
            a.updated_at ? new Date(a.updated_at).toLocaleDateString('fr-FR') : '',
            a.propositions?.map(p => `${p.action} (${p.person})`).join('; ') || 'Aucune'
        ]);
        return [headers, ...rows].map(r => r.map(cell => `"${cell}"`).join(',')).join('\n');
    }

    // === EXPORT PDF ===
    document.getElementById('exportArchivesPdf').addEventListener('click', function () {
        fetch(`${window.routes.closedAnomalies}?page=all`)
            .then(res => res.json())
            .then(data => {
                generatePDF(data.anomalies);
                toastr.success('PDF généré avec succès !');
            })
            .catch(() => toastr.error('Erreur lors de l\'export PDF'));
    });

    function generatePDF(anomalies) {
        const doc = new jsPDF({ orientation: 'landscape' });
        doc.setFontSize(16);
        doc.text('Archives des Anomalies Clôturées', 14, 15);
        doc.setFontSize(10);
        doc.text(`Généré le ${new Date().toLocaleString('fr-FR')}`, 14, 22);

        const tableData = anomalies.map(a => [
            a.id,
            new Date(a.datetime).toLocaleDateString('fr-FR'),
            a.rapporte_par,
            a.departement,
            a.gravity,
            a.updated_at ? new Date(a.updated_at).toLocaleDateString('fr-FR') : '-',
            a.propositions?.map(p => `${p.action} (${p.person})`).join('\n') || 'Aucune'
        ]);

        doc.autoTable({
            head: [['ID', 'Date', 'Rapporté par', 'Département', 'Gravité', 'Clôturé le', 'Propositions']],
            body: tableData,
            startY: 30,
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [41, 128, 185] },
            columnStyles: {
                6: { cellWidth: 50 }
            }
        });

        doc.save(`archives_anomalies_${new Date().toISOString().slice(0,10)}.pdf`);
    }

    // === Téléchargement générique ===
    function downloadFile(content, filename, type) {
        const blob = new Blob([content], { type });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    }

    // Lancer
    loadArchives();
});
</script>

<style>
.view-details-btn {
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.view-details-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.hidden {
    display: none;
}

/* Animation douce pour l'apparition */
tr[id^="props-"] {
    transition: all 0.3s ease-in-out;
}

/* Style pour les cartes internes */
.bg-white.rounded-xl {
    transition: all 0.3s ease-in-out;
}

.bg-white.rounded-xl:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
}
</style>

@endsection