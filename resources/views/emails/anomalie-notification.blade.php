<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle anomalie signalée</title>
</head>

<body style="
    margin:0;
    padding:20px;
    background-color:#f4f6f8;
    font-family: Arial, Helvetica, sans-serif;
">

    <div style="
        max-width:600px;
        margin:0 auto;
        background:#ffffff;
        border-radius:10px;
        overflow:hidden;
        box-shadow:0 4px 12px rgba(0,0,0,0.08);
    ">

        <!-- En-tête -->
        <div style="
            background:#d32f2f;
            color:#ffffff;
            padding:18px 25px;
        ">
            <h2 style="margin:0; font-size:20px;">
                🚨 Nouvelle anomalie signalée
            </h2>
        </div>

        <!-- Contenu -->
        <div style="padding:25px; color:#333333">

            <p style="margin-top:0;">Bonjour <strong>HSE</strong>,</p>

            <p>
                Une nouvelle anomalie a été enregistrée dans le système
                <strong>ERESriskalert</strong>.  
                Veuillez consulter les informations ci-dessous dans le tableau de bord .
            </p>

            <!-- Tableau -->
            <table style="
                width:100%;
                border-collapse:collapse;
                margin-top:20px;
                font-size:14px;
            ">
                <tr>
                    <td style="padding:8px 0; color:#555;"><strong>Rapporté par</strong></td>
                    <td style="padding:8px 0;">{{ $anomalie->rapporte_par }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#555;"><strong>Département</strong></td>
                    <td style="padding:8px 0;">{{ $anomalie->departement }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#555;"><strong>Localisation</strong></td>
                    <td style="padding:8px 0;">{{ $anomalie->localisation }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#555;"><strong>Gravité</strong></td>
                    <td style="
                        padding:8px 0;
                        font-weight:bold;
                        color:#d32f2f;
                    ">
                        {{ strtoupper($anomalie->gravity) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#555;"><strong>Date</strong></td>
                    <td style="padding:8px 0;">
                        {{ \Carbon\Carbon::parse($anomalie->datetime)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            </table>

            <hr style="
                border:none;
                border-top:1px solid #eeeeee;
                margin:25px 0;
            ">

            <p style="
                font-size:13px;
                color:#777777;
                margin-bottom:5px;
            ">
                Ceci est un message automatique, merci de ne pas répondre.
            </p>

            <p style="
                font-weight:bold;
                color:#0d47a1;
                margin:0;
            ">
                ERES TOGO
            </p>

        </div>
    </div>

</body>
</html>
