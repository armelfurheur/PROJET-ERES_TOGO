// main.js
document.addEventListener('DOMContentLoaded', async () => {
    loadUserData();
    setMinDateForProposals();
    await fetchAnomalies();
    await fetchArchives();
    updateNotifications();
    autoCleanTrash();
    
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    document.querySelector('[data-view="dashboard"]').click();
});
