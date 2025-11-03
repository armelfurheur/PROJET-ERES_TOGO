@extends('dash')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div id="view-archive" class="">
    <div class="page-header">
        <h1>Archives</h1>
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
                        <th class="border px-2 py-1">ID</th>
                        <th class="border px-2 py-1">Date anomalie</th>
                        <th class="border px-2 py-1">Rapporté par</th>
                        <th class="border px-2 py-1">Département</th>
                        <th class="border px-2 py-1">Gravité</th>
                        <th class="border px-2 py-1">Date clôture</th>
                        <th class="border px-2 py-1 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="archivesTableBody">
                    <tr><td colspan="7" class="text-center py-4 text-gray-500">Chargement...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center gap-3 mt-4"></div>
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
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">Aucune anomalie clôturée.</td></tr>`;
                    paginationDiv.innerHTML = '';
                    return;
                }

                data.anomalies.forEach(anomaly => {
                    const row = document.createElement('tr');
                    row.id = `archive-${anomaly.id}`;

                    const dateAnomalie = new Date(anomaly.created_at).toLocaleString();
                    const dateCloture = anomaly.updated_at ? new Date(anomaly.updated_at).toLocaleDateString() : '-';

                    let propsSummary = '';
                    if (anomaly.propositions && anomaly.propositions.length > 0) {
                        propsSummary = anomaly.propositions.map(p => `${p.action} (${p.person})`).join('; ');
                    } else {
                        propsSummary = 'Aucune';
                    }

                    row.innerHTML = `
                        <td class="border px-2 py-1">${anomaly.id}</td>
                        <td class="border px-2 py-1">${dateAnomalie}</td>
                        <td class="border px-2 py-1">${anomaly.rapporte_par}</td>
                        <td class="border px-2 py-1">${anomaly.departement}</td>
                        <td class="border px-2 py-1">${anomaly.gravity}</td>
                        <td class="border px-2 py-1">${dateCloture}</td>
                        <td class="border px-2 py-1 text-center">
                            <button onclick="toggleProposals(${anomaly.id})" class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Voir</button>
                        </td>
                    `;
                    tbody.appendChild(row);

                    // Ligne cachée des propositions
                    const propRow = document.createElement('tr');
                    propRow.id = `props-${anomaly.id}`;
                    propRow.classList.add('hidden', 'bg-gray-50');
                    propRow.innerHTML = `
                        <td colspan="7" class="px-4 py-2 border">
                            <strong>Propositions :</strong><br>
                            ${anomaly.propositions && anomaly.propositions.length > 0
                                ? anomaly.propositions.map(p => `
                                    <div class="text-xs">
                                        • <strong>${p.action}</strong> — ${p.person} <em>(${new Date(p.date).toLocaleDateString()})</em>
                                    </div>
                                `).join('')
                                : '<em class="text-gray-500">Aucune proposition</em>'
                            }
                        </td>
                    `;
                    tbody.appendChild(propRow);
                });

                // Pagination
                paginationDiv.innerHTML = `
                    <button ${data.current_page <= 1 ? 'disabled' : ''}
                        class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                        onclick="loadArchives(${data.current_page - 1})">Précédent</button>
                    <span class="font-medium mx-2">Page ${data.current_page} / ${data.last_page}</span>
                    <button ${data.current_page >= data.last_page ? 'disabled' : ''}
                        class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                        onclick="loadArchives(${data.current_page + 1})">Suivant</button>
                `;
            })
            .catch(err => {
                console.error(err);
                toastr.error("Erreur de chargement des archives.");
                tbody.innerHTML = `<tr><td colspan="7" class="text-red-500 text-center py-4">Erreur réseau.</td></tr>`;
            });
    }

    // Toggle propositions
    window.toggleProposals = function(id) {
        const row = document.getElementById(`props-${id}`);
        if (row) row.classList.toggle('hidden');
    };

    // === EXPORT CSV ===
    document.getElementById('exportArchivesCsv').addEventListener('click', function () {
        fetch(`${window.routes.closedAnomalies}?page=all`)
            .then(res => res.json())
            .then(data => {
                const csv = generateCSV(data.anomalies);
                downloadFile(csv, `archives_anomalies_${new Date().toISOString().slice(0,10)}.csv`, 'text/csv');
                toastr.success('CSV exporté !');
            })
            .catch(() => toastr.error('Erreur export CSV'));
    });

    function generateCSV(anomalies) {
        const headers = ['ID', 'Date anomalie', 'Rapporté par', 'Département', 'Gravité', 'Date clôture', 'Propositions'];
        const rows = anomalies.map(a => [
            a.id,
            new Date(a.datetime).toLocaleString(),
            a.rapporte_par,
            a.departement,
            a.gravity,
            a.updated_at ? new Date(a.updated_at).toLocaleDateString() : '',
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
                toastr.success('PDF généré !');
            })
            .catch(() => toastr.error('Erreur export PDF'));
    });

    function generatePDF(anomalies) {
        const doc = new jsPDF({ orientation: 'landscape' });
        doc.setFontSize(16);
        doc.text('Archives des Anomalies Clôturées', 14, 15);
        doc.setFontSize(10);
        doc.text(`Généré le ${new Date().toLocaleString()}`, 14, 22);

        const tableData = anomalies.map(a => [
            a.id,
            new Date(a.datetime).toLocaleDateString(),
            a.rapporte_par,
            a.departement,
            a.gravity,
            a.updated_at ? new Date(a.updated_at).toLocaleDateString() : '-',
            a.propositions?.map(p => `${p.action} (${p.person})`).join('\n') || 'Aucune'
        ]);

        doc.autoTable({
            head: [['ID', 'Date', 'Rapporté par', 'Département', 'Gravité', 'Clôturé le', 'Propositions']],
            body: tableData,
            startY: 30,
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [41, 128, 185] },
            columnStyles: {
                6: { cellWidth: 50 } // Propositions plus large
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

@endsection