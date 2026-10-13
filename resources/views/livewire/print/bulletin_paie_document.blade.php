<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 26px 34px; }
        body { font-family: sans-serif; font-size: 11px; color: #111; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        .entete { width: 100%; border-bottom: 2px solid #222; padding-bottom: 6px; margin-bottom: 10px; }
        .entete td { vertical-align: middle; }
        .ecole { text-align: center; }
        .ecole h2 { margin: 0; font-size: 18px; letter-spacing: 1px; }
        .ecole p { margin: 1px 0; font-size: 9.5px; }

        .titre { text-align: center; margin: 4px 0 10px; }
        .titre h3 { margin: 0; font-size: 15px; text-transform: uppercase; letter-spacing: 1px; }
        .titre p { margin: 3px 0 0; font-size: 11px; }

        table.infos { width: 100%; border: 1px solid #444; border-collapse: collapse; margin-bottom: 12px; }
        table.infos td { padding: 4px 8px; font-size: 10.5px; width: 50%; vertical-align: top; }

        table.lignes { width: 100%; border-collapse: collapse; }
        table.lignes th, table.lignes td { border: 1px solid #555; padding: 4px 6px; font-size: 10.5px; }
        table.lignes th { background: #e9ecef; text-align: center; }
        table.lignes td.d { text-align: right; }
        table.lignes td.c { text-align: center; }
        table.lignes tr.total td { background: #f4f4f4; font-weight: bold; }
        table.lignes td.vide { height: 18px; }

        table.net { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.net td { border: 1px solid #555; padding: 7px 8px; }
        table.net td.k { background: #e9ecef; font-weight: bold; width: 22%; }
        .montant { font-size: 15px; font-weight: bold; }
        .paye { color: #146c2e; font-weight: bold; }
        .attente { color: #a15c00; font-weight: bold; }

        table.sign { width: 100%; margin-top: 16px; border-collapse: collapse; }
        table.sign td { border: 1px solid #555; height: 80px; vertical-align: top; padding: 5px 8px; width: 50%; font-size: 10.5px; }
        .pied { text-align: center; font-size: 8.5px; color: #666; margin-top: 8px; }
    </style>
</head>

<body>
@php
    $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');
    $dec = fn ($v) => $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, ',', ' '), '0'), ',');
@endphp

@foreach ($bulletins as $b)
    <div class="page">
        @if ($b['type'] === 'personnel')
            @include('livewire.print._bulletin_format')
        @else

            @include('livewire.print._entete_societe')

            <div class="titre">
                <h3>Bulletin de paie</h3>
                <p>{{ $b['type'] === 'enseignant' ? 'Personnel enseignant' : 'Personnel administratif' }} — {{ $b['periode'] }}</p>
            </div>

            @include('livewire.print._bulletin_corps')

            <table class="sign">
                <tr>
                    <td><strong>{{ $b['signataires'][0] }}</strong><br><span style="font-size:9px;color:#555;">Signature</span></td>
                    <td><strong>{{ $b['signataires'][1] }}</strong><br><span style="font-size:9px;color:#555;">Signature et cachet</span></td>
                </tr>
            </table>

            <div class="pied">Édité le {{ now()->format('d/m/Y') }}</div>
        @endif
    </div>
@endforeach

</body>

</html>
