<!DOCTYPE html>
<html>
<head>
    <title>Rapport Anomalies - ERES Togo</title>
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
            <h1>Rapport Anomalies</h1>
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
                <th>ID</th>
                <th>Date/Heure</th>
                <th>Rapporté par</th>
                <th>Département</th>
                <th>Localisation</th>
                <th>Gravité</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anomalies as $anomaly)
                <tr>
                    <td>{{ $anomaly->id }}</td>
                    <td>{{ $anomaly->datetime->format('d/m/Y H:i') }}</td>
                    <td>{{ $anomaly->user ? $anomaly->user->name : $anomaly->rapporte_par }}</td>
                    <td>{{ $anomaly->departement }}</td>
                    <td>{{ $anomaly->localisation }}</td>
                    <td>{{ $anomaly->statut }}</td>
                    <td>{{ $anomaly->statut }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>