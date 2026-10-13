<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cartes</title>
    <style>
        @page { margin: 8mm; }
        body { font-family: DejaVu Sans, sans-serif; color: #111; margin: 0; }
        table { border-collapse: collapse; }
        .ligne { width: 100%; }
        .ligne td.cellule { padding: 0 0 4mm 0; vertical-align: top; }
        .ligne td.vide { width: 6mm; }
        .carte { width: 85.6mm; height: 53.9mm; border: 0.25mm solid #888; overflow: hidden; position: relative; background: #fff; }
        .bandeau td { color: #fff; height: 11mm; padding: 0 2mm; }
        .bandeau .nom { font-size: 6.6pt; font-weight: bold; text-align: right; line-height: 1.15; }
        .bandeau .titre { font-size: 5.6pt; letter-spacing: .4pt; text-align: right; }
        .corps { width: 100%; }
        .corps td { vertical-align: top; }
        .photo { width: 22mm; height: 27mm; border: 0.25mm solid #999; background: #eef1f5; text-align: center; color: #9aa5b1; font-size: 6pt; }
        .photo img { width: 22mm; height: 27mm; }
        .identite { padding-left: 3mm; }
        .identite .n { font-size: 9.5pt; font-weight: bold; line-height: 1.1; }
        .identite .p { font-size: 8.5pt; margin-bottom: 1.5mm; }
        .identite .d { font-size: 6.8pt; line-height: 1.5; }
        .identite .d b { color: #444; }
        .bas { position: absolute; left: 0; right: 0; bottom: 0; text-align: center; padding-bottom: 1.2mm; }
        .bas img { width: 58mm; height: 6.5mm; }
        .bas .num { font-size: 6pt; letter-spacing: .6pt; }
        .verso-titre { color: #fff; text-align: center; font-size: 7pt; font-weight: bold; padding: 1.6mm 0; }
        .verso td { font-size: 6.8pt; padding: 0.7mm 3mm; }
        .verso td.l { color: #555; width: 24mm; }
        .mention { font-size: 5.8pt; color: #444; padding: 1.5mm 3mm 0; text-align: center; line-height: 1.35; }
        .signature { position: absolute; left: 3mm; right: 3mm; bottom: 2mm; font-size: 5.8pt; text-align: right; }
    </style>
</head>
<body>
@foreach (array_chunk($cartes, 4) as $indexPage => $page)
    <div @if (!$loop->last) style="page-break-after: always;" @endif>
        @foreach ($page as $c)
            @php $ecole = $c['ecole']; @endphp
            <table class="ligne"><tr>
                {{-- RECTO --}}
                <td class="cellule" style="width: 85.6mm;">
                    <div class="carte">
                        <table style="width:100%; background: {{ $c['couleur'] }};" class="bandeau"><tr>
                            <td style="width: 12mm;">
                                @if (!empty($ecole['logo']) && file_exists($ecole['logo']))
                                    <img src="{{ $ecole['logo'] }}" style="height: 9mm;" alt="">
                                @endif
                            </td>
                            <td>
                                <div class="nom">{{ $ecole['nom'] ?? '' }}</div>
                                <div class="titre">{{ $c['titre'] }}</div>
                            </td>
                        </tr></table>
                        <table class="corps" style="margin: 2.2mm 3mm 0; width: auto;"><tr>
                            <td style="width: 22mm;">
                                <div class="photo">
                                    @if ($c['photo'])<img src="{{ $c['photo'] }}" alt="">@else<div style="padding-top: 11mm;">PHOTO</div>@endif
                                </div>
                            </td>
                            <td class="identite" style="width: 52mm;">
                                <div class="n">{{ $c['nom'] }}</div>
                                <div class="p">{{ $c['prenom'] }}</div>
                                <div class="d">
                                    @foreach ($c['details'] as $d)<b>{{ $d[0] }} :</b> {{ $d[1] }}<br>@endforeach
                                    @if ($c['annee'])<b>Année scolaire : </b>{{ date('Y', strtotime(AnneeScolaire()->date_debut)) . ' - ' . date('Y', strtotime(AnneeScolaire()->date_fin)) }}<br>@endif
                                </div>
                            </td>
                        </tr></table>
                        <div class="bas">
                            <img src="{{ $c['barcode'] }}" alt="">
                            <div class="num">{{ $c['numero'] }}</div>
                        </div>
                    </div>
                </td>
                <td class="vide"></td>
                {{-- VERSO --}}
                <td class="cellule" style="width: 85.6mm;">
                    <div class="carte">
                        <div class="verso-titre" style="background: {{ $c['couleur'] }};">{{ $c['titre'] }}</div>
                        <table style="width: 100%; margin-top: 1.5mm;"><tr>
                            <td style="width: 59mm; vertical-align: top; padding: 0;">
                        <table class="verso" style="width: 100%;">
                            <tr><td class="l">N° de carte</td><td><b>{{ $c['numero'] }}</b></td></tr>
                            @if ($c['naissance'])<tr><td class="l">Né(e) le</td><td>{{ $c['naissance'] }}</td></tr>@endif
                            <tr><td class="l">Émise le</td><td>{{ $c['emission'] }}</td></tr>
                            <tr><td class="l">Valable jusqu'au</td><td><b>{{ $c['expiration'] }}</b></td></tr>
                        </table>
                            </td>
                            <td style="vertical-align: top; text-align: center; padding: 0.5mm 2mm 0 0;">
                                <img src="{{ $c['qrcode'] }}" style="width: 20mm; height: 20mm;" alt="">
                                <div style="font-size: 4.8pt; color: #555; line-height: 1.2;">Scanner pour<br>vérifier</div>
                            </td>
                        </tr></table>
                        @if (!empty($ecole['telephone']) || !empty($ecole['adresse']))
                            <div class="mention" style="padding-top: 0.8mm; color: #222;">
                                @if (!empty($ecole['telephone']))Tél : {{ $ecole['telephone'] }}@endif
                                @if (!empty($ecole['telephone']) && !empty($ecole['adresse'])) — @endif
                                @if (!empty($ecole['adresse'])){{ $ecole['adresse'] }}@endif
                            </div>
                        @endif
                        <div class="mention">Cette carte est strictement personnelle. En cas de perte ou de vol, prière de le signaler à l'établissement.</div>
                        <div class="signature">La Direction</div>
                    </div>
                </td>
            </tr></table>
        @endforeach
    </div>
@endforeach
</body>
</html>
