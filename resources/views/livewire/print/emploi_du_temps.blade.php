


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Emploi du Temps</title>
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        margin: 40px;
    }
    .entete {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        border-bottom: 1px solid #333;
        padding-bottom: 10px;
        page-break-inside: avoid;
    }
    .logo {
        width: 80px;
    }
    .societe {
        text-align: center;
        flex-grow: 1;
    }
    .societe h2 {
        margin: 0;
        font-size: 18px;
    }
    .societe p {
        margin: 2px 0;
        font-size: 12px;
    }
    .header {
        text-align: center;
        margin: 25px 0 15px;
        page-break-inside: avoid;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        page-break-inside: avoid;
    }
    thead, tbody, tr, th, td {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    th, td {
        border: 1px solid #999;
        padding: 6px;
        text-align: center;
        vertical-align: top;
        word-wrap: break-word;
    }
    th {
        background-color: #f0f0f0;
    }
    .seance {
        background-color: #fdfdfd;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 6px;
        margin-bottom: 8px;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        text-align: center;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .seance strong {
        display: block;
        font-weight: bold;
        color: #333;
    }
    .seance small {
        display: block;
        color: #555;
    }

    table.croise { table-layout: fixed; }
    table.croise td.heures { background-color: #f7f7f7; vertical-align: middle; font-size: 11px; }
    table.croise .seance { margin-bottom: 4px; }
</style>

</head>
<body>
    @include('livewire.print._entete_societe')

    <!-- Titre -->
    <div class="header">
        <h3>Emploi du Temps - {{ $classe->classe ?? '' }}</h3>
    </div>

    @include('livewire.print._emploi_croise', ['jours' => $jours, 'creneaux' => $creneaux, 'grille' => $grille, 'mode' => 'classe'])
</body>
</html>
