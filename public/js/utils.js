// utils.js
const formatDateTime = iso => new Date(iso).toLocaleString('fr-FR');

function downloadCSV(filename, rows, headers) {
    const csv = [headers.join(',')]
        .concat(rows.map(r => headers.map(h => `"${(r[h] ?? '').replace(/"/g,'""')}"`).join(',')))
        .join('\r\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    saveAs(blob, filename);
}
