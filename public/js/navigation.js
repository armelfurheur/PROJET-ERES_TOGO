// navigation.js
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
    else if (currentView === 'archive') renderArchives();
    else if (currentView === 'reports' && currentReportData) {
        createGravityChart(currentReportData.filtered, 'gravityChartCanvas');
        createDepartmentChart(currentReportData.filtered, 'departmentChartCanvas');
    }
}
