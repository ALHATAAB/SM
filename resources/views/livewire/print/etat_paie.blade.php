<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>État de paie</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 12px;
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
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        table.etat th,
        table.etat td {
            font-size: 8.5px;
            padding: 3px 4px;
        }

        table.etat th.sign,
        table.etat td.sign {
            width: 70px;
        }
    </style>
</head>

<body>

    @include('livewire.print._entete_societe')

    @php
        $fmt = fn ($v) => $v ? number_format((float) $v, 0, ',', ' ') : '';
        $nbG = count($etat['gains']);
        $nbR = count($etat['retenues']);
    @endphp

    <div class="title">
        ÉTAT DE PAIE DU PERSONNEL<br>
        {{ $etat['periode'] }}
    </div>

    <table class="etat">
        <thead>
            <tr>
                <th rowspan="2">Matricule</th>
                <th rowspan="2">Nom et prénom</th>
                <th rowspan="2">Poste</th>
                @if ($nbG)
                    <th colspan="{{ $nbG }}" class="center">GAINS</th>
                @endif
                <th rowspan="2" class="right">Total gain</th>
                <th rowspan="2" class="right">Brut imposable</th>
                <th rowspan="2" class="right">Brut non imposable</th>
                @if ($nbR)
                    <th colspan="{{ $nbR }}" class="center">RETENUES</th>
                @endif
                <th rowspan="2" class="right">Total retenue</th>
                <th rowspan="2" class="right">Net à payer</th>
                <th rowspan="2" class="right">CNSS patronale</th>
                <th rowspan="2" class="right">ANPE patronale</th>
                <th rowspan="2" class="sign">Signature</th>
            </tr>
            <tr>
                @foreach ($etat['gains'] as $libelle)
                    <th class="right">{{ $libelle }}</th>
                @endforeach
                @foreach ($etat['retenues'] as $libelle)
                    <th class="right">{{ $libelle }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($etat['fiches'] as $f)
                @php $m = $etat['montants'][$f->employer_id] ?? ['g' => [], 'r' => []]; @endphp
                <tr>
                    <td>{{ $f->employer->matricule }}</td>
                    <td>{{ $f->employer->nom }} {{ $f->employer->prenom }}</td>
                    <td>{{ $f->employer->poste }}</td>
                    @foreach ($etat['gains'] as $rid => $libelle)
                        <td class="right">{{ $fmt($m['g'][$rid] ?? 0) }}</td>
                    @endforeach
                    <td class="right"><strong>{{ number_format($f->total_gain, 0, ',', ' ') }}</strong></td>
                    <td class="right">{{ $fmt($etat['repartitions'][$f->employer_id]['imposable'] ?? 0) }}</td>
                    <td class="right">{{ $fmt($etat['repartitions'][$f->employer_id]['non_imposable'] ?? 0) }}</td>
                    @foreach ($etat['retenues'] as $rid => $libelle)
                        <td class="right">{{ $fmt($m['r'][$rid] ?? 0) }}</td>
                    @endforeach
                    <td class="right"><strong>{{ number_format($f->total_retenu, 0, ',', ' ') }}</strong></td>
                    <td class="right"><strong>{{ number_format($f->salaire_net, 0, ',', ' ') }}</strong></td>
                    <td class="right">{{ $fmt($f->cnss_patronal) }}</td>
                    <td class="right">{{ $fmt($f->anpe_patronal) }}</td>
                    <td class="sign"></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                @foreach ($etat['gains'] as $rid => $libelle)
                    <th class="right">{{ $fmt($etat['totaux']['g'][$rid] ?? 0) }}</th>
                @endforeach
                <th class="right">{{ number_format($etat['totaux']['gain'], 0, ',', ' ') }}</th>
                <th class="right">{{ $fmt($etat['totaux']['imposable']) }}</th>
                <th class="right">{{ $fmt($etat['totaux']['non_imposable']) }}</th>
                @foreach ($etat['retenues'] as $rid => $libelle)
                    <th class="right">{{ $fmt($etat['totaux']['r'][$rid] ?? 0) }}</th>
                @endforeach
                <th class="right">{{ number_format($etat['totaux']['retenu'], 0, ',', ' ') }}</th>
                <th class="right">{{ number_format($etat['totaux']['net'], 0, ',', ' ') }}</th>
                <th class="right">{{ $fmt($etat['totaux']['patronal']) }}</th>
                <th class="right">{{ $fmt($etat['totaux']['anpe']) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
