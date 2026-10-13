<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Stock des Livres</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
        }

        h2,
        h4 {
            text-align: center;
        }
    </style>
</head>

<body>
    @include('livewire.print._entete_societe')
    <h2>État du Stock des Livres</h2>

    @if (!empty($searchTitre) || (!empty($dateDebut) && !empty($dateFin)))
        <h4>Filtres appliqués :</h4>
        <ul>
            @if (!empty($searchTitre))
                <li><strong>Titre contient :</strong> {{ $searchTitre }}</li>
            @endif
            @if (!empty($dateDebut) && !empty($dateFin))
                <li><strong>Période :</strong> du {{ $dateDebut }} au {{ $dateFin }}</li>
            @endif
        </ul>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Total Entrées</th>
                <th>Montant Achat</th>
                <th>Total Sorties</th>
                <th>Montant Vente</th>
                <th>Stock Réel</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($livres as $i => $livre)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $livre->titre }}</td>
                    <td>{{ $livre->total_entree ?? 0 }}</td>
                    <td>{{ $livre->montant_entree ?? 0 }}</td>
                    <td>{{ $livre->total_sortie ?? 0 }}</td>
                    <td>{{ $livre->montant_sortie ?? 0 }}</td>
                    <td><strong>{{ $livre->stock_actuel }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <th colspan="2">TOTAL</th>
                <th>{{ $etat['total_entree'] ?? 0 }}</th>
                <th>{{ number_format($etat['montant_entree'] ?? 0, 0, ',', ' ') }} CFA</th>
                <th>{{ $etat['total_sortie'] ?? 0 }}</th>
                <th>{{ number_format($etat['montant_sortie'] ?? 0, 0, ',', ' ') }} CFA</th>
                <th>{{ $etat['stock_global'] ?? 0 }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
