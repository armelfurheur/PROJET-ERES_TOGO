<!DOCTYPE html>
<html>
<head>
    <title>Rapport Propositions - ERES Togo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .logo { width: 60px; height: 60px; }
        .company-info h1 { color: #047857; margin: 0; font-size: 24px; }
        .document-info p { margin: 2px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <img src="{{ public_path('img/ERES.jpg') }}" alt="Logo ERES" class="logo">
            <h1>Rapport Propositions</h1>
        </div>
        <div class="document-info">
            <p>Généré le: {{ $date }}</p>
            <p>Par: {{ $user->name }}</p>
            <p>Email: {{ $user->email }}</p>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID Proposition</th>
                <th>Anomalie ID</th>
                <th>Date Réception</th>
                <th>Action Corrective</th>
                <th>Personne Responsable</th>
                <th>Date Prévue</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->id }}</td>
                    <td>{{ $proposal->anomaly_id }}</td>
                    <td>{{ $proposal->received_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $proposal->action_corrective }}</td>
                    <td>{{ $proposal->personne_responsable }}</td>
                    <td>{{ $proposal->date_prevue->format('d/m/Y') }}</td>
                    <td>{{ $proposal->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>