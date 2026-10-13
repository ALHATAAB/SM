{{-- Gabarit commun des reçus : A4 (2 exemplaires sur la feuille) ou ticket thermique (1 ticket par exemplaire). --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $d['titre'] }} {{ $d['reference'] }}</title>
    @if ($mode === 'a4')
        <style>
            @page { margin: 10mm; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; margin: 0; padding: 0; }
            table { border-collapse: collapse; width: 100%; }
            td { font-size: 11px; }
            .bloc { width: 100%; }
            .entete td { vertical-align: middle; padding: 0 0 5px 0; }
            .entete .logo { width: 70px; }
            .entete .soc { text-align: center; line-height: 1.3; font-size: 10px; }
            .entete .soc .nom { font-size: 14px; font-weight: bold; text-transform: uppercase; }
            .entete .ex { width: 120px; text-align: right; font-size: 9px; }
            .badge { border: 1px solid #000; padding: 3px 5px; font-weight: bold; font-size: 10px; text-align: center; }
            .sep-entete { border-top: 1.5px solid #333; margin: 2px 0 6px 0; }
            .titre td { padding: 5px 6px; background: #eeeeee; border: 1px solid #999; }
            .titre .t { font-size: 14px; font-weight: bold; }
            .titre .r { text-align: right; font-size: 10px; width: 170px; }
            .lignes { margin-top: 6px; }
            .lignes td { padding: 3px 4px; border-bottom: 1px dotted #888; }
            .lignes .l { width: 38%; color: #333; }
            .lignes .v { font-weight: bold; }
            .lignes tr.fort td { border-top: 1px solid #000; border-bottom: 1px solid #000; font-size: 1.1em; font-weight: bold; color: #000; }
            .montant { margin-top: 8px; border: 1.5px solid #000; }
            .montant td { padding: 5px 8px; }
            .montant .lib { font-size: 11px; }
            .montant .val { text-align: right; font-size: 15px; font-weight: bold; }
            .lettres { margin-top: 4px; font-size: 10px; font-style: italic; }
            .signatures { margin-top: 10px; }
            .signatures td { width: 50%; text-align: center; font-size: 10px; height: 38px; vertical-align: top; }
            .note { margin-top: 4px; font-size: 9px; font-style: italic; text-align: center; color: #333; }
            .coupe { border-top: 1px dashed #000; margin: 10px 0; text-align: center; font-size: 8px; color: #555; }
        </style>
    @else
        <style>
            @page { margin: 3mm; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #000; margin: 0; padding: 0; }
            table { border-collapse: collapse; width: 100%; }
            td { font-size: 8px; }
            .centre { text-align: center; }
            .nom { font-size: 10px; font-weight: bold; text-transform: uppercase; }
            .petit { font-size: 7px; line-height: 1.25; }
            .sep { border-top: 1px dashed #000; margin: 4px 0; }
            .titre { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
            .bandeau { border: 1px solid #000; padding: 2px; margin: 4px 0; font-weight: bold; text-align: center; font-size: 8px; }
            .lignes td { padding: 1.5px 0; border-bottom: 1px dotted #777; vertical-align: top; }
            .lignes .l { width: 42%; }
            .lignes .v { width: 58%; text-align: right; font-weight: bold; }
            .lignes tr.fort td { border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; color: #000; }
            .montant { border: 1px solid #000; margin-top: 5px; padding: 3px; text-align: center; }
            .montant .val { font-size: 12px; font-weight: bold; }
            .lettres { margin-top: 3px; font-size: 7px; font-style: italic; text-align: center; }
            .signatures td { width: 50%; text-align: center; font-size: 7px; height: 30px; vertical-align: top; }
            .note { margin-top: 3px; font-size: 7px; font-style: italic; text-align: center; }
        </style>
    @endif
</head>

<body>
    @if ($mode === 'a4')
        @include('livewire.print.recu._a4')
    @else
        @foreach ($exemplaires as $i => $exemplaire)
            <div @if (!$loop->last) style="page-break-after: always;" @endif>
                @include('livewire.print.recu._thermique', ['exemplaire' => $exemplaire, 'rang' => $i + 1, 'total' => count($exemplaires)])
            </div>
        @endforeach
    @endif
</body>

</html>
