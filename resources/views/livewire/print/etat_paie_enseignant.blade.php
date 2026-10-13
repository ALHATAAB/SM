<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>État de paie</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .logo {
            float: left;
            width: 20%;
        }

        .company-details {
            float: right;
            width: 75%;
            text-align: right;
            font-size: 11px;
        }

        .clear {
            clear: both;
        }

        .title {
            text-align: center;
            margin: 20px 0 10px;
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    @include('livewire.print._entete_societe')

    <div class="title">
        ÉTAT DE PAIE ENSEIGNANT(S)<br>
        Mois : {{ $etatsPaies->first()->mois->mois ?? '—' }} /
        Année : {{ date('Y', strtotime(AnneeScolaire()->date_debut)).' - '.date('Y', strtotime(AnneeScolaire()->date_fin))
          ?? '—' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th class="center">Heures</th>
                <th class="right">Salaire fixe</th>
                <th class="right">Salaire horaire</th>
                <th class="right">Total brut</th>
                <th class="right">Retenue ITS</th>
                <th class="right">Autres retenues / Avance</th>
                <th class="right">Salaire net</th>
                <th class="center">Signature</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($etatsPaies as $etatsPaie)
                <tr>
                    <td>{{ $etatsPaie->enseignant->matricule ?? '' }}</td>
                    <td>{{ $etatsPaie->enseignant->nom ?? '' }}</td>
                    <td>{{ $etatsPaie->enseignant->prenom ?? '' }}</td>
                    <td class="center">{{ $etatsPaie->heures_sup ?? 0 }}</td>
                    <td class="right">{{ number_format($etatsPaie->montant_base, 0, ',', ' ') }}</td>
                    <td class="right">
                        {{ $etatsPaie->heures_sup > 0
                            ? number_format($etatsPaie->montant_heures_sup / $etatsPaie->heures_sup, 0, ',', ' ')
                            : '0' }}
                    </td>
                    @php
                        $rIts    = $retenues[$etatsPaie->enseignant_id]['its'] ?? 0;
                        $rAutres = $retenues[$etatsPaie->enseignant_id]['autres'] ?? 0;
                    @endphp
                    <td class="right">{{ number_format($etatsPaie->total_paye, 0, ',', ' ') }}</td>
                    <td class="right">{{ number_format($rIts, 0, ',', ' ') }}</td>
                    <td class="right">{{ number_format($rAutres, 0, ',', ' ') }}</td>
                    <td class="right"><strong>{{ number_format($etatsPaie->total_paye - $rIts - $rAutres, 0, ',', ' ') }}</strong></td>
                    <td class="center">..................</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <th colspan="6">TOTAL</th>
                <th style="text-align:right">{{ number_format($totalPaie ?? 0, 0, ',', ' ') }}</th>
                <th style="text-align:right">{{ number_format($totalIts ?? 0, 0, ',', ' ') }}</th>
                <th style="text-align:right">{{ number_format($totalAutres ?? 0, 0, ',', ' ') }}</th>
                <th style="text-align:right">{{ number_format($totalNet ?? 0, 0, ',', ' ') }}</th>
                <th colspan="3">...</th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
