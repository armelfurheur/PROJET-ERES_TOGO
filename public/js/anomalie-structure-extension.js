document.addEventListener('DOMContentLoaded', () => {
    const structureSelect = document.getElementById('filterStructure');
    if (!structureSelect) return;

    // Interception propre de buildUrl
    const originalBuildUrl = window.buildUrl;

    if (typeof originalBuildUrl === 'function') {
        window.buildUrl = function (page) {
            let url = originalBuildUrl(page);
            const structure = structureSelect.value;

            if (structure) {
                const sep = url.includes('?') ? '&' : '?';
                url += `${sep}structure=${encodeURIComponent(structure)}`;
            }
            return url;
        };
    }

    // Recharger automatiquement quand la structure change
    structureSelect.addEventListener('change', () => {
        if (typeof window.loadAnomalies === 'function') {
            window.loadAnomalies(1);
        }
    });
});
