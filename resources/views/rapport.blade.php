@extends('dash')
@section('content')

<div id="view-reports" class="p-4">
    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <!-- Header -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('img/ERES.jpg') }}" alt="Logo ERES" class="h-10 w-auto mb-3">
            <h2 class="text-2xl font-bold text-gray-700">Rapports de remontée d’anomalies</h2>
            <p class="text-gray-500 text-sm mt-1">Générez et exportez vos rapports selon la période souhaitée.</p>
        </div>

        <!-- Filtres + Bouton Générer -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-6">
            <div>
                <label for="dateType" class="text-sm font-medium text-gray-600">Type de période</label>
                <select id="dateType" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="month">Par mois</option>
                    <option value="year">Par année</option>
                </select>
            </div>

            <div id="monthGroup">
                <label for="reportMonth" class="text-sm font-medium text-gray-600">Mois</label>
                <input id="reportMonth" type="month" name="reportMonth" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
            </div>

            <div id="yearGroup" style="display: none;">
                <label for="reportYear" class="text-sm font-medium text-gray-600">Année</label>
                <select id="reportYear" name="reportYear" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"></select>
            </div>

            <div>
                <button id="generateReport" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition">
                    Générer le rapport
                </button>
            </div>
        </div>

        <!-- Zone pour les boutons d'export (créés dynamiquement) -->
        <div id="exportButtonsContainer" class="flex gap-2 justify-end mb-6"></div>

        <!-- Résultats -->
        <div id="reportResult" class="mt-10 hidden bg-gray-50 p-6 rounded-lg border border-gray-200">
            <div id="reportStats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-center mb-8"></div>

            <!-- Graphique principal -->
            <div class="bg-white p-4 shadow rounded-lg mb-6 max-w-3xl mx-auto">
                <h3 class="font-semibold text-gray-700 mb-2 text-center">Statistiques visuelles</h3>
                <canvas id="reportChart" height="80"></canvas>
            </div>

            <!-- Graphique horizontal -->
            <div class="bg-white p-4 shadow rounded-lg mb-6 max-w-3xl mx-auto">
                <h3 class="font-semibold text-gray-700 mb-2 text-center">Totaux anomalies ouvertes vs clôturées</h3>
                <canvas id="reportChartHorizontal" height="120"></canvas>
            </div>

            <!-- Tableau -->
            <div class="bg-white p-4 shadow rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">Liste des anomalies</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-2 text-gray-700 text-sm">N°</th>
                                <th class="border px-2 py-2 text-gray-700 text-sm">Description</th>
                                <th class="border px-2 py-2 text-gray-700 text-sm">Localisation</th>
                                <th class="border px-2 py-2 text-gray-700 text-sm">Gravité</th>
                                <th class="border px-2 py-2 text-gray-700 text-sm">Département</th>
                                <th class="border px-2 py-2 text-gray-700 text-sm">Statut</th>
                            </tr>
                        </thead>
                        <tbody id="anomaliesTableBody" class="text-sm"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dépendances -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };

    const dateType = document.getElementById('dateType');
    const monthGroup = document.getElementById('monthGroup');
    const yearGroup = document.getElementById('yearGroup');
    const yearSelect = document.getElementById('reportYear');
    const generateBtn = document.getElementById('generateReport');
    const reportResult = document.getElementById('reportResult');
    const reportStats = document.getElementById('reportStats');
    const anomaliesTableBody = document.getElementById('anomaliesTableBody');
    const exportButtonsContainer = document.getElementById('exportButtonsContainer');

    let reportChart = null, horizontalChart = null, currentReportData = null;

    // Remplir les années
    const currentYear = new Date().getFullYear();
    for (let y = currentYear - 5; y <= currentYear + 10; y++) {
        yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }
    yearSelect.value = currentYear;

    // Gestion du type de période
    dateType.addEventListener('change', () => {
        monthGroup.style.display = dateType.value === 'month' ? 'block' : 'none';
        yearGroup.style.display = dateType.value === 'year' ? 'block' : 'none';
    });

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

        fetch('{{ route("generate.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type, reportMonth: month, reportYear: year })
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
        .catch(() => toastr.error('Erreur de connexion au serveur.'));
    });

    function createExportButtons() {
        exportButtonsContainer.innerHTML = '';

        const pdfBtn = document.createElement('button');
        pdfBtn.id = 'exportReportPdf';
        pdfBtn.className = 'bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition';
        pdfBtn.innerHTML = 'Exporter en PDF';
        pdfBtn.addEventListener('click', exportToPDF);

        const csvBtn = document.createElement('button');
        csvBtn.id = 'exportReportCsv';
        csvBtn.className = 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition ml-2';
        csvBtn.innerHTML = 'Exporter en CSV';
        csvBtn.addEventListener('click', exportToCSV);

        exportButtonsContainer.appendChild(pdfBtn);
        exportButtonsContainer.appendChild(csvBtn);
    }

    function displayReport(data, type) {
        const stats = data.statistiques || {};
        const periode = data.periode || {};

        reportStats.innerHTML = `
            <div class="bg-white p-4 rounded-lg shadow text-gray-700">
                <p class="text-xs text-gray-500 uppercase">Période</p>
                <p class="font-semibold">${safeFormatDate(periode.debut)} - ${safeFormatDate(periode.fin)}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg shadow text-blue-700">
                <p class="text-xs uppercase">Total</p>
                <p class="font-bold text-xl">${stats.total ?? 0}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg shadow text-green-700">
                <p class="text-xs uppercase">Clôturées</p>
                <p class="font-bold text-xl">${stats.cloturees ?? 0}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg shadow text-yellow-700">
                <p class="text-xs uppercase">Ouvertes</p>
                <p class="font-bold text-xl">${stats.ouvertes ?? 0}</p>
            </div>
            <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg shadow flex items-center justify-center gap-2">
                <span class="font-semibold">Utilisateur le plus actif :</span>
                <span class="bg-indigo-200 text-indigo-900 px-2 py-1 rounded-full text-sm font-medium">
                    ${escapeHtml(stats.utilisateur_top?.nom || 'Aucun')} (${stats.utilisateur_top?.nombre || 0})
                </span>
            </div>
        `;

        anomaliesTableBody.innerHTML = (data.data || []).map((a, i) => `
            <tr class="hover:bg-gray-50">
                <td class="border px-2 py-1 text-center">${i + 1}</td>
                <td class="border px-2 py-1">${escapeHtml(a.description)}</td>
                <td class="border px-2 py-1">${a.localisation || '-'}</td>
                <td class="border px-2 py-1">${a.gravity || '-'}</td>
                <td class="border px-2 py-1">${a.departement || '-'}</td>
                <td class="border px-2 py-1">${a.status || '-'}</td>
            </tr>
        `).join('');

        renderCharts(data, type);
    }

    function safeFormatDate(date) {
        if (!date) return 'Inconnue';
        const d = new Date(date);
        return isNaN(d) ? 'Invalide' : `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // --- Graphiques (affichés à l'écran) ---
    function renderCharts(data, type) {
        if (reportChart) reportChart.destroy();
        if (horizontalChart) horizontalChart.destroy();

        const ctx1 = document.getElementById('reportChart').getContext('2d');
        const ctx2 = document.getElementById('reportChartHorizontal').getContext('2d');
        Chart.register(ChartDataLabels);

        if (type === 'month') {
            const labels = Object.keys(data.statistiques?.par_gravite || {});
            const values = Object.values(data.statistiques?.par_gravite || {});
            const total = values.reduce((a,b) => a + b, 0);
            reportChart = new Chart(ctx1, {
                type: 'doughnut',
                data: { labels, datasets: [{ data: values, backgroundColor: ['#ddda14ff','#cc1717ff','#08ea53ff','#22c55e'] }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom' }, datalabels: { color: '#fff', formatter: val => total ? ((val/total)*100).toFixed(1)+'%' : '' } } },
                plugins: [ChartDataLabels]
            });
        } else {
            const months = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
            const values = data.statistiques?.mensuel || Array(12).fill(0);
            const total = values.reduce((a,b) => a + b, 0);
            reportChart = new Chart(ctx1, {
                type: 'bar',
                data: { labels: months, datasets: [{ label: 'Anomalies', data: values, backgroundColor: '#3b82f6' }] },
                options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { datalabels: { anchor: 'end', align: 'top', color: '#333', formatter: val => total ? ((val/total)*100).toFixed(1)+'%' : '' } } },
                plugins: [ChartDataLabels]
            });
        }

        const labelsH = type === 'year' ? ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'] : ['Totaux du mois'];
        const ouvertes = type === 'year' ? (data.statistiques?.mensuel_ouvertes || Array(12).fill(0)) : [data.statistiques?.ouvertes || 0];
        const cloturees = type === 'year' ? (data.statistiques?.mensuel_cloturees || Array(12).fill(0)) : [data.statistiques?.cloturees || 0];
        const totaux = ouvertes.map((v, i) => v + (cloturees[i] || 0));
        const percentOuvertes = ouvertes.map((v, i) => totaux[i] ? ((v / totaux[i]) * 100).toFixed(1) : 0);
        const percentCloturees = cloturees.map((v, i) => totaux[i] ? ((v / totaux[i]) * 100).toFixed(1) : 0);

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
                backgroundColor: '#f50b0bff',
                borderColor: '#f50b0bff',
                borderWidth: 1,
                borderRadius: 10,
                borderSkipped: false
            },
            {
                label: 'Anomalies Clôturées',
                data: cumulCloturees,
                backgroundColor: '#22c55e',
                borderColor: '#22c55e',
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
                title: { display: true, text: type === 'year' ? 'Mois' : 'Période' }
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

 
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();
        let y = 20;
        let pdfGenerated = false;

        const generatePdfContent = () => {
            if (pdfGenerated) return;
            pdfGenerated = true;

            // Titre
            doc.setFontSize(16);
            doc.text("Rapport de remontée d’anomalies", pageWidth / 2, y, { align: 'center' });
            y += 12;

            // Période
            const periode = currentReportData.periode || {};
            doc.setFontSize(10);
            doc.text(`Période : ${safeFormatDate(periode.debut)} - ${safeFormatDate(periode.fin)}`, pageWidth / 2, y, { align: 'center' });
            y += 15;

            // Statistiques
            const stats = currentReportData.statistiques || {};
            doc.setFontSize(10);
            doc.text(`Total anomalies : ${stats.total ?? 0}`, 20, y);
            y += 6;
            doc.text(`Ouvertes : ${stats.ouvertes ?? 0} | Clôturées : ${stats.cloturees ?? 0}`, 20, y);
            y += 15;

            // Tableau
            const tableData = (currentReportData.data || []).map((a, i) => [
                i + 1,
                (a.description || '').substring(0, 60) + (a.description?.length > 60 ? '...' : ''),
                a.localisation || '',
                a.gravity || '',
                a.departement || '',
                a.status || ''
            ]);

            doc.autoTable({
                head: [['N°', 'Description', 'Localisation', 'Gravité', 'Département', 'Statut']],
                body: tableData,
                startY: y,
                theme: 'grid',
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [55, 65, 81], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 10 },
                    1: { cellWidth: 60 },
                    2: { cellWidth: 30 },
                    3: { cellWidth: 20 },
                    4: { cellWidth: 30 },
                    5: { cellWidth: 20 }
                }
            });

            const fileName = `rapport_remontee_anomalies_${new Date().toISOString().slice(0,10)}.pdf`;
            doc.save(fileName);
            toastr.success('PDF exporté avec succès !');
        };

        const logo = new Image();
        logo.crossOrigin = 'Anonymous';
        logo.src = '{{ asset('img/ERES.jpg') }}';

        logo.onload = () => {
            doc.addImage(logo, 'JPG', 20, 10, 30, 15);
            generatePdfContent();
        };

        // Fallback si logo ne charge pas
        setTimeout(() => {
            if (!pdfGenerated) {
                generatePdfContent();
            }
        }, 1000);
    }

 
    function exportToCSV() {
        if (!currentReportData?.data?.length) {
            toastr.error('Aucune donnée à exporter.');
            return;
        }

        let csv = '\uFEFFNuméro,Description,Localisation,Gravité,Département,Statut\n';
        currentReportData.data.forEach((a, i) => {
            csv += `${i+1},"${(a.description || '').replace(/"/g, '""')}","${a.localisation || ''}","${a.gravity || ''}","${a.departement || ''}","${a.status || ''}"\n`;
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