// archives.js
let archives = [];

async function fetchArchives() {
    try {
        const res = await fetch('/api/archives');
        const data = await res.json();
        archives = data.archives || [];
        renderArchives();
    } catch (err) {
        console.error('Erreur archives:', err);
    }
}

function renderArchives() {
    const tbody = document.getElementById('archivesTableBody');
    if (!tbody) return;
    tbody.innerHTML = archives.length ? archives.map(a => `
        <tr>
            <td>ARCH-${a.id}</td>
            <td>${formatDateTime(a.datetime)}</td>
            <td>${a.rapporte_par}</td>
            <td>${a.departement}</td>
            <td>${a.statut}</td>
            <td>${formatDateTime(a.closed_at)}</td>
            <td>${a.closed_by}</td>
            <td>
                <button class="btn-view-archive btn btn-info btn-sm" data-id="${a.id}">👁️</button>
                <button class="btn-restore-archive btn btn-success btn-sm" data-id="${a.id}">♻️</button>
                <button class="btn-delete-archive btn btn-danger btn-sm" data-id="${a.id}">🗑️</button>
            </td>
        </tr>
    `).join('') : `<tr><td colspan="8" class="empty-state">Aucune archive</td></tr>`;
}
