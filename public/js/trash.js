// trash.js
function renderTrashView() {
    const trash = getTrash();
    const container = document.getElementById('view-trash');
    if (!container) return;

    container.innerHTML = trash.length ? `
        <div class="card">
            <div class="card-header">
                <h2>🗑️ Corbeille</h2>
                <div class="btn-group">
                    <button id="restoreAllTrashBtn" class="btn btn-success btn-sm">♻️ Tout restaurer</button>
                    <button id="emptyTrashBtn" class="btn btn-danger btn-sm">⚠️ Vider</button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Type</th><th>Contenu</th><th>Supprimé le</th><th>Par</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        ${trash.map(item => `
                            <tr>
                                <td>${item.type}</td>
                                <td>${item.data.description || item.data.action}</td>
                                <td>${formatDateTime(item.deletedAt)}</td>
                                <td>${item.deletedBy}</td>
                                <td>
                                    <button data-id="${item.id}" class="btn-restore btn btn-success btn-sm">♻️</button>
                                    <button data-id="${item.id}" class="btn-delete-permanent btn btn-danger btn-sm">🗑️</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>` 
    : `<p class="empty-state">Corbeille vide</p>`;

    document.querySelectorAll('.btn-restore').forEach(b => b.addEventListener('click', e => restoreFromTrash(e.target.dataset.id)));
    document.querySelectorAll('.btn-delete-permanent').forEach(b => b.addEventListener('click', e => deleteFromTrash(e.target.dataset.id)));
}
