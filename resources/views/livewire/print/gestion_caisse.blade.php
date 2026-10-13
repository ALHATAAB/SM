<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Reçu de Caisse</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            background-color: #bdc3c7;
            padding: 10px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid #000;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .solde-final {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px;
            margin-top: 10px;
        }
        .card-header img {
            width: 28px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    @include('livewire.print._entete_societe')
    <h2>Gestion de la Caisse - {{ $caisse->caisse }}</h2>
    <p><strong>Date operation :</strong>{{ \Carbon\Carbon::parse( $caisse->date_comptable)->format('d/m/Y') }} </p>

    @if ($caisse->etat == 'ouverte')
        <ul>
            <li><strong>Solde report :</strong> {{ number_format($caisse->repport, 0, ',', ' ') }}</li>
            <li><strong>Nombre opérations entrées :</strong> {{ $countEntree }}</li>
            <li><strong>Nombre opérations sorties :</strong> {{ $countSortie }}</li>
            <li><strong>Montant total entrées :</strong> {{ number_format($montantEntree, 0, ',', ' ') }}</li>
            <li><strong>Montant total sorties :</strong> {{ number_format($montantSortie, 0, ',', ' ') }}</li>
        </ul>

        <div class="solde-final">
            <strong>Solde caisse :</strong> {{ number_format($caisse->solde, 0, ',', ' ') }}
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Entrée</th>
                    <th class="text-right">Sortie</th>
                    <th class="text-right">Solde</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mouvementCaisses as $mvt)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse( $mvt->date_comptable)->format('d/m/Y') }}</td>
                        <td>
                            @if ($mvt->description === 'Solde cloture' || $mvt->description === 'Solde repport')
                                <strong>{{ $mvt->description }}</strong>
                            @else
                                {{ $mvt->description }}
                            @endif
                        </td>
                        <td class="text-right" style="text-align:right">{{ number_format($mvt->entree, 0, ',', ' ') }}</td>
                        <td class="text-right" style="text-align:right">{{ number_format($mvt->sortie, 0, ',', ' ') }}</td>
                        <td class="text-right" style="text-align:right">{{ number_format($mvt->solde, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: red;"><strong>⚠️ Caisse fermée.</strong></p>
    @endif

</body>

</html>
