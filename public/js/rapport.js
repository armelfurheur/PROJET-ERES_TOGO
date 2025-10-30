
        // ========== CHARTS ==========
        let gravityChart = null;
        let departmentChart = null;
        let dashboardGravityChart = null;
        let dashboardDepartmentChart = null;

        function createGravityChart(filtered, canvasId, isDashboard = false) {
            const arret = filtered.filter(a => a.statut_anomalie === 'arret').length;
            const precaution = filtered.filter(a => a.statut_anomalie === 'precaution').length;
            const continuer = filtered.filter(a => a.statut_anomalie === 'continuer').length;
            const total = arret + precaution + continuer;
            
            const arretPct = total > 0 ? Math.round((arret / total) * 100) : 0;
            const precautionPct = total > 0 ? Math.round((precaution / total) * 100) : 0;
            const continuerPct = total > 0 ? Math.round((continuer / total) * 100) : 0;
            
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            
            const existingChart = isDashboard ? dashboardGravityChart : gravityChart;
            if (existingChart) existingChart.destroy();
            
            const data = [
                { label: `🚨 Arrêt Imminent`, value: arretPct },
                { label: `⚠️ Précaution`, value: precautionPct },
                { label: `🟢 Continuer`, value: continuerPct }
            ].filter(d => d.value > 0);
            
            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        data: data.map(d => d.value),
                        backgroundColor: ['rgba(239, 68, 68, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(16, 185, 129, 0.7)'],
                        borderColor: ['rgb(239, 68, 68)', 'rgb(245, 158, 11)', 'rgb(16, 185, 129)'],
                        borderWidth: 2
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold' },
                            formatter: (value) => value + '%'
                        }
                    }
                }
            });

            if (isDashboard) {
                dashboardGravityChart = chart;
            } else {
                gravityChart = chart;
            }
        }

    function createDepartmentChart(filtered, canvasId, isDashboard = false) {
    const deptCounts = {
        technique: filtered.filter(a => a.departement.toLowerCase() === 'technique').length,
        logistique: filtered.filter(a => a.departement.toLowerCase() === 'logistique').length,
        commercial: filtered.filter(a => a.departement.toLowerCase() === 'commercial').length,
        administratif: filtered.filter(a => a.departement.toLowerCase() === 'administratif').length,
        achats: filtered.filter(a => a.departement.toLowerCase() === 'achats').length

    };

    const total = Object.values(deptCounts).reduce((sum, count) => sum + count, 0);
    const percentages = {
        technique: total > 0 ? Math.round((deptCounts.technique / total) * 100) : 0,
        logistique: total > 0 ? Math.round((deptCounts.logistique / total) * 100) : 0,
        commercial: total > 0 ? Math.round((deptCounts.commercial / total) * 100) : 0,
        administratif: total > 0 ? Math.round((deptCounts.administratif / total) * 100) : 0,
        achats: total > 0 ? Math.round((deptCounts.achats / total) * 100) : 0

    };

    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas avec l'ID ${canvasId} non trouvé`);
        return;
    }

    const existingChart = isDashboard ? dashboardDepartmentChart : departmentChart;
    if (existingChart) {
        existingChart.destroy();
    }

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Technique', 'Logistique', 'Commercial', 'Administratif' , 'achats'],
            datasets: [{
                label: 'Pourcentage d\'anomalies',
                data: [
                    percentages.technique,
                    percentages.logistique,
                    percentages.commercial,
                    percentages.administratif,
                    percentages.achats
                ],
                backgroundColor: [
                    'rgba(4, 120, 87, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                    'rgba(243, 16, 167, 1)'
                ],
                borderColor: [
                    'rgb(4, 120, 87)',
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(139, 92, 246)',
                    'rgba(243, 16, 167, 1)'

                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const dept = Object.keys(deptCounts)[context.dataIndex];
                            return `${context.parsed.y}% (${deptCounts[dept]} anomalies)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Pourcentage (%)' }
                },
                x: {
                    title: { display: true, text: 'Départements' }
                }
            }
        }
    });

    if (isDashboard) {
        dashboardDepartmentChart = chart;
    } else {
        departmentChart = chart;
    }
}

// Fonction pour rendre les anomalies par utilisateur
function renderUserAnomalies(filteredAnomalies) {
    const tbody = document.getElementById('userAnomaliesTableBody');
    if (!tbody) {
        console.error("Tableau 'userAnomaliesTableBody' non trouvé");
        return;
    }

    // Compter les anomalies par utilisateur
    const userCounts = {};
    filteredAnomalies.forEach(anomaly => {
        const user = anomaly.rapporte_par || 'Inconnu';
        userCounts[user] = (userCounts[user] || 0) + 1;
    });

    // Convertir en tableau pour trier par nombre d'anomalies (décroissant)
    const userArray = Object.entries(userCounts)
        .map(([user, count]) => ({ user, count }))
        .sort((a, b) => b.count - a.count);

    // Vider le tableau
    tbody.innerHTML = '';

    // Cas où il n'y a pas de données
    if (userArray.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" class="empty-state">Aucune anomalie rapportée</td></tr>';
        return;
    }

    // Remplir le tableau
    userArray.forEach(({ user, count }) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${user}</strong></td>
            <td style="text-align: center;">${count}</td>
        `;
        tbody.appendChild(tr);
    });
}

// Fonction pour rendre le tableau de bord
function renderDashboard() {
    const total = anomalies.length;
    const open = anomalies.filter(a => a.status === 'Ouverte').length;
    const closed = total - open;
    const totalProposals = anomalies.reduce((sum, a) => sum + (a.proposals?.length || 0), 0);
    
    document.getElementById('dashboardTotalAnomalies').textContent = total;
    document.getElementById('dashboardOpenAnomalies').textContent = open;
    document.getElementById('dashboardClosedAnomalies').textContent = closed;
    document.getElementById('dashboardTotalProposals').textContent = totalProposals;

    createGravityChart(anomalies, 'dashboardGravityChart', true);
    createDepartmentChart(anomalies, 'dashboardDepartmentChart', true);
    
    // Appeler renderUserAnomalies avec toutes les anomalies pour le tableau de bord
    renderUserAnomalies(anomalies);
}



// Écouteur pour générer le rapport
document.getElementById('generateReport').addEventListener('click', () => {
    const month = document.getElementById('reportMonth').value;
    const year = document.getElementById('reportYear').value;
    
    const filtered = anomalies.filter(a => {
        if (month === 'all' && year === 'all') return true;
        const d = new Date(a.datetime);
        const monthMatch = (month === 'all') || (d.getMonth() + 1 === Number(month));
        const yearMatch = (year === 'all') || (d.getFullYear() === Number(year));
        return monthMatch && yearMatch;
    });
    
    const total = filtered.length;
    const closed = filtered.filter(a => a.status === 'Clos').length;
    const open = total - closed;
    const arret = filtered.filter(a => a.statut_anomalie === 'arret').length;
    const precaution = filtered.filter(a => a.statut_anomalie === 'precaution').length;
    const continuer = filtered.filter(a => a.statut_anomalie === 'continuer').length;
    const totalSeverity = arret + precaution + continuer;
    const arretPct = totalSeverity > 0 ? Math.round((arret / totalSeverity) * 100) : 0;
    const precautionPct = totalSeverity > 0 ? Math.round((precaution / totalSeverity) * 100) : 0;
    const continuerPct = totalSeverity > 0 ? Math.round((continuer / totalSeverity) * 100) : 0;
    
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const periodText = month === 'all' && year === 'all' ? 'Toutes périodes' :
                       month === 'all' ? `Année ${year}` :
                       year === 'all' ? monthNames[parseInt(month)-1] :
                       `${monthNames[parseInt(month)-1]} ${year}`;
    
    currentReportData = { period: periodText, total, open, closed, arret: arretPct, precaution: precautionPct, continuer: continuerPct, filtered };
    
    document.getElementById('reportStats').innerHTML = `
        <div class="stat-card">
            <h4>Total anomalie(s)</h4>
            <div class="value">${total}</div>
        </div>
        <div class="stat-card warning">
            <h4>Non corrigé(s)</h4>
            <div class="value">${open}</div>
        </div>
        <div class="stat-card success">
            <h4>Corrigé(s)</h4>
            <div class="value">${closed}</div>
        </div>
    `;
    
    createGravityChart(filtered, 'gravityChartCanvas');
    createDepartmentChart(filtered, 'departmentChartCanvas');
    
    // Appeler renderUserAnomalies avec les anomalies filtrées
    renderUserAnomalies(filtered);
    
    document.getElementById('chartsContainer').style.display = 'block';
    
    // Exportation CSV du rapport complet
    document.getElementById('exportReportCsv').onclick = () => {
        const rows = filtered.map(a => ({
            id: a.id,
            datetime: formatDateTime(a.datetime),
            rapporte_par: a.rapporte_par,
            departement: a.departement,
            localisation: a.localisation,
            gravite: a.statut_anomalie,
            status: a.status
        }));
        downloadCSV(`rapport_hse_${month}_${year}_eres_togo.csv`, rows, ['id','datetime','rapporte_par','departement','localisation','gravite','status']);
    };
    
    // Exportation CSV des anomalies par utilisateur
    document.getElementById('exportUserAnomaliesCsv').onclick = () => {
        const userCounts = {};
        filtered.forEach(anomaly => {
            const user = anomaly.rapporte_par || 'Inconnu';
            userCounts[user] = (userCounts[user] || 0) + 1;
        });
        const userArray = Object.entries(userCounts)
            .map(([user, count]) => ({ user, count }))
            .sort((a, b) => b.count - a.count);
        downloadCSV(`anomalies_par_utilisateur_${month}_${year}_eres_togo.csv`, userArray, ['user','count']);
    };
    
    // Exportation PDF
    document.getElementById('exportReportPdf').onclick = () => {
        const userCounts = {};
        filtered.forEach(anomaly => {
            const user = anomaly.rapporte_par || 'Inconnu';
            userCounts[user] = (userCounts[user] || 0) + 1;
        });
        const userArray = Object.entries(userCounts)
            .map(([user, count]) => ({ user, count }))
            .sort((a, b) => b.count - a.count);

        const html = `
            <h1>📈 Rapport d'Activité HSE</h1>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">${total}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">${open}</div>
                    <div class="stat-label">Non corrigés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">${closed}</div>
                    <div class="stat-label">Corrigés</div>
                </div>
            </div>
            <h3>Répartition par gravité</h3>
            <p>🚨 Arrêt: ${arretPct}% | ⚠️ Précaution: ${precautionPct}% | 🟢 Continuer: ${continuerPct}%</p>
            <h3>Anomalies par utilisateur</h3>
            <table>
                <thead>
                    <tr><th>Utilisateur</th><th>Nombre d'anomalies rapportées</th></tr>
                </thead>
                <tbody>
                    ${userArray.length > 0 ? userArray.map(({ user, count }) => `
                        <tr><td>${user}</td><td>${count}</td></tr>
                    `).join('') : '<tr><td colspan="2">Aucune anomalie rapportée</td></tr>'}
                </tbody>
            </table>
            <h3>Détails des anomalies</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>Date/Heure</th><th>Rapporté par</th><th>Département</th><th>Gravité</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    ${filtered.map(a => `
                        <tr>
                            <td>${a.id.slice(0,12)}</td>
                            <td>${formatDateTime(a.datetime)}</td>
                            <td>${a.rapporte_par}</td>
                            <td>${a.departement}</td>
                            <td>${a.statut_anomalie === 'arret' ? '🚨 Arrêt' : a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td>
                            <td>${a.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        openPrintable('Rapport HSE', html);
    };
});

// Appeler fetchAnomalies au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    fetchAnomalies();
});


        // ========== EMAIL AVEC PDF ==========
async function sendReportByEmail(reportData) {
    try {
        showToast('Génération du PDF en cours...', 'info');
        
        // Générer le PDF
        const pdfBlob = await generateReportPDF(reportData);
        
        // Convertir le blob en base64
        const reader = new FileReader();
        reader.readAsDataURL(pdfBlob);
        
        reader.onloadend = function() {
            const base64data = reader.result.split(',')[1];
            
            // Créer le corps de l'email simplifié
            const subject = `Rapport HSE Mensuel - ${reportData.period}`;
            const body = `Cher Directeur,

Veuillez trouver ci-joint le rapport de remontée d'anomalie mensuel pour la période : ${reportData.period}.



Cordialement,
${currentUser.name}
Responsable HSE - ERES-TOGO


`;

            // Ouvrir le client email avec le lien de téléchargement du PDF
            // Note: Les clients email ne supportent pas l'attachement direct via mailto
            // Solution alternative : télécharger le PDF et informer l'utilisateur
            
            // Télécharger le PDF
            saveAs(pdfBlob, `rapport_hse_${reportData.period.replace(/\s/g, '_')}_${new Date().toISOString().split('T')[0]}.pdf`);
            
            // Ouvrir le client email
            window.location.href = `mailto:${params.email}?cc=${params.email_cc}&subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            
            showToast('PDF généré et téléchargé ! Veuillez l\'attacher manuellement à l\'email.', 'success');
        };
        
    } catch (error) {
        console.error('Erreur lors de la génération du PDF:', error);
        showToast('Erreur lors de la génération du PDF', 'error');
    }
}

// ========== GÉNÉRATION PDF RAPPORT ==========
async function generateReportPDF(reportData) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    const openAnomalies = reportData.filtered.filter(a => a.status === 'Ouverte');
    const closedAnomalies = reportData.filtered.filter(a => a.status === 'Clos');
    
    let yPos = 20;
    const leftMargin = 15;
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    
    // Helper function pour vérifier si on doit ajouter une nouvelle page
    const checkPageBreak = (neededSpace) => {
        if (yPos + neededSpace > pageHeight - 20) {
            doc.addPage();
            yPos = 20;
            return true;
        }
        return false;
    };
    
    // ===== EN-TÊTE =====
    doc.setFillColor(4, 120, 87);
    doc.rect(0, 0, pageWidth, 40, 'F');
    
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(22);
    doc.setFont('helvetica', 'bold');
    doc.text('ERES-TOGO', leftMargin, 15);
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    
    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('Rapport HSE Mensuel de remontée d anomalie ', leftMargin, 33);
    
    // Date et période
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    const dateText = `Généré le: ${new Date().toLocaleDateString('fr-FR')}`;
    const periodText = `Période: ${reportData.period}`;
    doc.text(dateText, pageWidth - leftMargin - doc.getTextWidth(dateText), 15);
    doc.text(periodText, pageWidth - leftMargin - doc.getTextWidth(periodText), 22);
    
    yPos = 50;
    doc.setTextColor(0, 0, 0);
    
    // ===== RÉSUMÉ DES STATISTIQUES =====
    doc.setFillColor(240, 240, 240);
    doc.rect(leftMargin, yPos, pageWidth - 2 * leftMargin, 8, 'F');
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('RÉSUMÉ DES STATISTIQUES', leftMargin + 2, yPos + 5.5);
    
    yPos += 12;
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    
    const stats = [
        `Total d'anomalies traitées : ${reportData.total}`,
        `Anomalies non corrigées (ouvertes) : ${reportData.open}`,
        `Anomalies corrigées (clôturées) : ${reportData.closed}`
    ];
    
    stats.forEach(stat => {
        doc.text(`• ${stat}`, leftMargin + 5, yPos);
        yPos += 6;
    });
    
    yPos += 5;
    
    // ===== RÉPARTITION PAR NIVEAU DE GRAVITÉ =====
    checkPageBreak(30);
    
    doc.setFillColor(240, 240, 240);
    doc.rect(leftMargin, yPos, pageWidth - 2 * leftMargin, 8, 'F');
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('RÉPARTITION PAR NIVEAU DE GRAVITÉ', leftMargin + 2, yPos + 5.5);
    
    yPos += 12;
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    
    const gravityStats = [
        { icon: '🚨', label: `Arrêt Imminent : ${reportData.arret}%`, color: [239, 68, 68] },
        { icon: '⚠️', label: `Précaution : ${reportData.precaution}%`, color: [245, 158, 11] },
        { icon: '🟢', label: `Continuer : ${reportData.continuer}%`, color: [16, 185, 129] }
    ];
    
    gravityStats.forEach(item => {
        doc.setTextColor(...item.color);
        doc.setFont('helvetica', 'bold');
        doc.text(`• ${item.label}`, leftMargin + 5, yPos);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(0, 0, 0);
        yPos += 6;
    });
    
    yPos += 10;
    
    // ===== FONCTION POUR CRÉER UN TABLEAU D'ANOMALIES =====
    const createAnomaliesTable = (anomalies, title) => {
        checkPageBreak(20);
        
        doc.setFillColor(240, 240, 240);
        doc.rect(leftMargin, yPos, pageWidth - 2 * leftMargin, 8, 'F');
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(0, 0, 0);
        doc.text(title, leftMargin + 2, yPos + 5.5);
        
        yPos += 12;
        
        if (anomalies.length === 0) {
            doc.setFontSize(10);
            doc.setFont('helvetica', 'italic');
            doc.text('Aucune anomalie pour cette catégorie.', leftMargin + 5, yPos);
            yPos += 10;
            return;
        }
        
        // En-têtes du tableau
        const colWidths = [25, 65, 80];
        const headers = ['ID', 'Description', 'Gravité'];
        
        doc.setFillColor(4, 120, 87);
        doc.rect(leftMargin, yPos, pageWidth - 2 * leftMargin, 7, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        
        let xPos = leftMargin + 2;
        headers.forEach((header, i) => {
            doc.text(header, xPos, yPos + 5);
            xPos += colWidths[i];
        });
        
        yPos += 7;
        doc.setTextColor(0, 0, 0);
        doc.setFont('helvetica', 'normal');
        
        // Lignes du tableau
        anomalies.forEach((anomaly, index) => {
            checkPageBreak(10);
            
            // Alternance de couleur pour les lignes
            if (index % 2 === 0) {
                doc.setFillColor(250, 250, 250);
                doc.rect(leftMargin, yPos, pageWidth - 2 * leftMargin, 8, 'F');
            }
            
            const id = anomaly.id.slice(0, 12);
            const desc = anomaly.description.slice(0, 45) + (anomaly.description.length > 45 ? '...' : '');
            const gravite = anomaly.statut_anomalie === 'arret' ? 'Arrêt' : 
                           anomaly.statut_anomalie === 'precaution' ? 'Précaution' : 'Continuer';
            
            xPos = leftMargin + 2;
            doc.setFontSize(8);
            doc.text(id, xPos, yPos + 5.5);
            xPos += colWidths[0];
            doc.text(desc, xPos, yPos + 5.5, { maxWidth: colWidths[1] - 4 });
            xPos += colWidths[1];
            doc.text(gravite, xPos, yPos + 5.5);
            
            yPos += 8;
        });
        
        yPos += 5;
    };
    
    // ===== DÉTAILS DES ANOMALIES NON CORRIGÉES =====
    createAnomaliesTable(openAnomalies, `DÉTAILS DES ANOMALIES NON CORRIGÉES (${openAnomalies.length})`);
    
    // ===== DÉTAILS DES ANOMALIES CORRIGÉES =====
    createAnomaliesTable(closedAnomalies, `DÉTAILS DES ANOMALIES CORRIGÉES (${closedAnomalies.length})`);
    
    // ===== PIED DE PAGE =====
    const totalPages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128, 128, 128);
        doc.text(
            `ERES-TOGO - Rapport HSE de remontée d'anomalie | Page ${i} sur ${totalPages}`,
            pageWidth / 2,
            pageHeight - 10,
            { align: 'center' }
        );
    }
    
    // Retourner le blob du PDF
    return doc.output('blob');
}
      
        // ========== RENDER DASHBOARD ==========
        function renderDashboard() {
            const total = anomalies.length;
            const open = anomalies.filter(a => a.status === 'Ouverte').length;
            const closed = total - open;
            const totalProposals = anomalies.reduce((sum, a) => sum + (a.proposals?.length || 0), 0);
            
            document.getElementById('dashboardTotalAnomalies').textContent = total;
            document.getElementById('dashboardOpenAnomalies').textContent = open;
            document.getElementById('dashboardClosedAnomalies').textContent = closed;
            document.getElementById('dashboardTotalProposals').textContent = totalProposals;

            createGravityChart(anomalies, 'dashboardGravityChart', true);
            createDepartmentChart(anomalies, 'dashboardDepartmentChart', true);
        }

        // ========== RENDER ANOMALIES ==========
        let filteredAnomalies = [];

        function renderAnomalies() {
            const tbody = document.getElementById('anomaliesTableBody');
            const statusFilter = document.getElementById('filterStatus').value;
            const priorityFilter = document.getElementById('filterPriority').value;
            const searchDept = document.getElementById('searchDepartment').value.toLowerCase();
            const searchDate = document.getElementById('searchDate').value;
            
            filteredAnomalies = anomalies.filter(a => {
                const statusMatch = statusFilter === 'all' || a.status === statusFilter;
                const priorityMatch = priorityFilter === 'all' || a.statut_anomalie === priorityFilter;
                const deptMatch = !searchDept || a.departement.toLowerCase().includes(searchDept);
                const dateMatch = !searchDate || new Date(a.datetime).toISOString().split('T')[0] === searchDate;
                return statusMatch && priorityMatch && deptMatch && dateMatch;
            });
            
            tbody.innerHTML = '';
            document.getElementById('anomalyCount').textContent = `(${filteredAnomalies.length})`;
            
            if (!filteredAnomalies.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Aucune anomalie trouvée</td></tr>';
                return;
            }
            
            filteredAnomalies.forEach((a) => {
                const priorityClass = a.statut_anomalie === 'arret' ? 'badge-arret' : 
                                    a.statut_anomalie === 'precaution' ? 'badge-precaution' : 'badge-continuer';
                const statusClass = a.status === 'Clos' ? 'badge-closed' : 'badge-open';
                const priorityText = a.statut_anomalie === 'arret' ? '🚨 Arrêt' : 
                                    a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
                const hasProposal = a.proposals && a.proposals.length > 0;
                
                const tr = document.createElement('tr');
                if (!a.read) tr.classList.add('unread');
                
                tr.innerHTML = `
                    <td><strong>${a.id.slice(0, 12)}</strong></td>
                    <td>${formatDateTime(a.datetime)}</td>
                    <td>${a.rapporte_par}</td>
                    <td>${a.departement}</td>
                    <td>${a.localisation}</td>
                    <td style="text-align: center;"><span class="badge ${priorityClass}">${priorityText}</span></td>
                    <td style="text-align: center;"><span class="badge ${statusClass}">${a.status}</span></td>
                    <td style="text-align: center;">
                        <button class="btn btn-info btn-sm btn-view-anomaly" data-id="${a.id}">👁️</button>
                        ${a.status !== 'Clos' ? `
                            <button class="btn btn-primary btn-sm btn-propose-action" data-id="${a.id}" ${hasProposal ? 'disabled title="Action déjà proposée"' : ''}>📝</button>
                            <button class="btn btn-success btn-sm btn-close-anomaly" data-id="${a.id}" ${!hasProposal ? 'disabled title="Proposition d\'action requise avant clôture"' : ''}>✓</button>
                        ` : ''}
                        <button class="btn btn-danger btn-sm btn-delete-anomaly" data-id="${a.id}">🗑️</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            updateNotifications();
        }

        // ========== VIEW ANOMALY DETAILS ==========
        function viewAnomalyDetails(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (!anomaly) return;
            
            if (!anomaly.read) {
                anomaly.read = true;
                store.saveAnomalies(anomalies);
                updateNotifications();
            }
            
            const priorityText = anomaly.statut_anomalie === 'arret' ? '🚨 Arrêt Imminent' : 
                                anomaly.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer';
            const priorityClass = anomaly.statut_anomalie === 'arret' ? 'badge-arret' : 
                                anomaly.statut_anomalie === 'precaution' ? 'badge-precaution' : 'badge-continuer';
            const hasProposal = anomaly.proposals && anomaly.proposals.length > 0;
            
            let proposalsHtml = '';
            if (hasProposal) {
                proposalsHtml = `
                    <div class="detail-full">
                        <label>Propositions d'actions</label>
                        <div class="value">
                            ${anomaly.proposals.map(p => `
                                <div style="margin-bottom: 1rem; padding: 0.75rem; background: var(--gray-100); border-radius: 6px;">
                                    <strong>Action:</strong> ${p.action}<br>
                                    <strong>Responsable:</strong> ${p.person}<br>
                                    <strong>Date prévue:</strong> ${p.date}<br>
                                    <strong>Statut:</strong> <span class="badge badge-proposed">${p.status}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('modalBody').innerHTML = `
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>ID Anomalie</label>
                        <div class="value">${anomaly.id.slice(0, 12)}</div>
                    </div>
                    <div class="detail-item">
                        <label>Date & Heure</label>
                        <div class="value">${formatDateTime(anomaly.datetime)}</div>
                    </div>
                    <div class="detail-item">
                        <label>Rapporté par</label>
                        <div class="value">${anomaly.rapporte_par}</div>
                    </div>
                    <div class="detail-item">
                        <label>Département</label>
                        <div class="value">${anomaly.departement}</div>
                    </div>
                    <div class="detail-item">
                        <label>Localisation</label>
                        <div class="value">${anomaly.localisation}</div>
                    </div>
                    <div class="detail-item">
                        <label>Gravité</label>
                        <div class="value"><span class="badge ${priorityClass}">${priorityText}</span></div>
                    </div>
                </div>
                
                <div class="detail-full">
                    <label>Description</label>
                    <div class="value">${anomaly.description}</div>
                </div>
                
                <div class="detail-full">
                    <label>Action immédiate</label>
                    <div class="value">${anomaly.action}</div>
                </div>
                
                ${anomaly.preuve_url ? `<div class="detail-full"><label>Preuve</label><img src="${anomaly.preuve_url}" class="proof-image"></div>` : '<div class="detail-full"><label>Preuve</label><div class="value">Aucune preuve</div></div>'}
                
                ${proposalsHtml}
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    ${anomaly.status !== 'Clos' ? `
                        ${!hasProposal ? `<button class="btn btn-primary" onclick="proposeActionFromModal('${anomaly.id}')">📝 Proposer action</button>` : ''}
                        ${hasProposal ? `<button class="btn btn-success" onclick="closeAnomalyFromModal('${anomaly.id}')">✓ Clôturer</button>` : ''}
                    ` : '<div class="badge badge-closed" style="padding: 0.5rem 1rem;">Clôturée</div>'}
                    <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
                </div>
            `;
            
            document.getElementById('anomalyModal').classList.add('active');
        }

        window.proposeActionFromModal = function(id) {
            openProposalModal(id);
        };

        window.closeAnomalyFromModal = function(id) {
            const anomaly = anomalies.find(a => a.id === id);
            if (anomaly && anomaly.proposals && anomaly.proposals.length > 0) {
                anomaly.status = 'Clos';
                store.saveAnomalies(anomalies);
                closeModal();
                renderAnomalies();
                renderProposals();
                renderDashboard();
                showToast('Anomalie clôturée !', 'success');
            } else {
                showToast('Une proposition d\'action est requise avant clôture', 'warning');
            }
        };

        window.closeModal = function() {
            document.getElementById('anomalyModal').classList.remove('active');
        }
           // ========== REPORTS ==========
        let currentReportData = null;

        document.getElementById('generateReport').addEventListener('click', () => {
            const month = document.getElementById('reportMonth').value;
            const year = document.getElementById('reportYear').value;
            
            const filtered = anomalies.filter(a => {
                if (month === 'all' && year === 'all') return true;
                const d = new Date(a.datetime);
                const monthMatch = (month === 'all') || (d.getMonth() + 1 === Number(month));
                const yearMatch = (year === 'all') || (d.getFullYear() === Number(year));
                return monthMatch && yearMatch;
            });
            
            const total = filtered.length;
            const closed = filtered.filter(a => a.status === 'Clos').length;
            const open = total - closed;
            const arret = filtered.filter(a => a.statut_anomalie === 'arret').length;
            const precaution = filtered.filter(a => a.statut_anomalie === 'precaution').length;
            const continuer = filtered.filter(a => a.statut_anomalie === 'continuer').length;
            const totalSeverity = arret + precaution + continuer;
            const arretPct = totalSeverity > 0 ? Math.round((arret / totalSeverity) * 100) : 0;
            const precautionPct = totalSeverity > 0 ? Math.round((precaution / totalSeverity) * 100) : 0;
            const continuerPct = totalSeverity > 0 ? Math.round((continuer / totalSeverity) * 100) : 0;
            
            const periodText = month === 'all' && year === 'all' ? 'Toutes périodes' :
                            month === 'all' ? `Année ${year}` :
                            year === 'all' ? monthNames[parseInt(month)-1] :
                            `${monthNames[parseInt(month)-1]} ${year}`;
            
            currentReportData = { period: periodText, total, open, closed, arret: arretPct, precaution: precautionPct, continuer: continuerPct, filtered };
            
            document.getElementById('reportStats').innerHTML = `
                <div class="stat-card">
                    <h4>Total anomalie(s)</h4>
                    <div class="value">${total}</div>
                </div>
                <div class="stat-card warning">
                    <h4>Non corrigé(s)</h4>
                    <div class="value">${open}</div>
                </div>
                <div class="stat-card success">
                    <h4>Corrigé(s)</h4>
                    <div class="value">${closed}</div>
                </div>
            `;
            
            createGravityChart(filtered, 'gravityChartCanvas');
            createDepartmentChart(filtered, 'departmentChartCanvas');
            
            document.getElementById('chartsContainer').style.display = 'block';
            
            document.getElementById('exportReportCsv').onclick = () => {
                const rows = filtered.map(a => ({
                    id: a.id,
                    datetime: formatDateTime(a.datetime),
                    rapporte_par: a.rapporte_par,
                    departement: a.departement,
                    localisation: a.localisation,
                    gravite: a.statut_anomalie,
                    status: a.status
                }));
                downloadCSV(`rapport_hse_${month}_${year}_eres_togo.csv`, rows, ['id','datetime','rapporte_par','departement','localisation','gravite','status']);
            };
            
            document.getElementById('exportReportPdf').onclick = () => {
                const html = `
                    <h1>📈 Rapport d'Activité HSE</h1>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">${total}</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${open}</div>
                            <div class="stat-label">Non corrigés</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${closed}</div>
                            <div class="stat-label">Corrigés</div>
                        </div>
                    </div>
                    <h3>Répartition par gravité</h3>
                    <p>🚨 Arrêt: ${arretPct}% | ⚠️ Précaution: ${precautionPct}% | 🟢 Continuer: ${continuerPct}%</p>
                    <table>
                        <thead><tr><th>ID</th><th>Date/Heure</th><th>Rapporté par</th><th>Département</th><th>Gravité</th><th>Statut</th></tr></thead>
                        <tbody>${filtered.map(a => `<tr><td>${a.id.slice(0,12)}</td><td>${formatDateTime(a.datetime)}</td><td>${a.rapporte_par}</td><td>${a.departement}</td><td>${a.statut_anomalie === 'arret' ? '🚨 Arrêt' : a.statut_anomalie === 'precaution' ? '⚠️ Précaution' : '🟢 Continuer'}</td><td>${a.status}</td></tr>`).join('')}</tbody>
                    </table>
                `;
                openPrintable('Rapport HSE', html);
            };
        });

        document.getElementById('toggleCharts').addEventListener('click', () => {
            const chartsContainer = document.getElementById('chartsContainer');
            const btn = document.getElementById('toggleCharts');
            
            if (chartsContainer.style.display === 'none' || !chartsContainer.style.display) {
                if (currentReportData) {
                    chartsContainer.style.display = 'block';
                    btn.textContent = '📊 Masquer diagrammes';
                } else {
                    showToast('Veuillez d\'abord générer un rapport', 'warning');
                }
            } else {
                chartsContainer.style.display = 'none';
                btn.textContent = '📊 Afficher diagrammes';
            }
        });

        document.getElementById('sendReportEmail').addEventListener('click', () => {
            if (!currentReportData) {
                showToast('Veuillez d\'abord générer un rapport', 'warning');
                return;
            }
            sendReportByEmail(currentReportData);
        });