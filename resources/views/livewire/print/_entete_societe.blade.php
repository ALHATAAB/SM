{{-- En-tête commun des documents PDF : logo + informations de la société (paramètre Société). --}}
@php
    $soc = \App\Services\SocieteService::info();
    $hauteurLogo = $hauteurLogo ?? 60;
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:8px; border-bottom:1.5px solid #333;">
    <tr>
        <td style="width:90px; vertical-align:middle; padding:0 0 6px 0; border:none;">
            @if (file_exists($soc['logo']))
                <img src="{{ $soc['logo'] }}" style="height:{{ $hauteurLogo }}px;" alt="Logo">
            @endif
        </td>
        <td style="text-align:center; vertical-align:middle; padding:0 0 6px 0; border:none; line-height:1.35;">
            <div style="font-size:16px; font-weight:bold; text-transform:uppercase;">{{ $soc['nom'] }}</div>
            @if (!empty($soc['activite']))<div style="font-size:11px;">{{ $soc['activite'] }}</div>@endif
            <div style="font-size:11px;">{{ $soc['adresse'] }}</div>
            @if (!empty($soc['contact']))<div style="font-size:11px;">{{ $soc['contact'] }}</div>@endif
            @if (!empty($soc['legal']))<div style="font-size:10px;">{{ $soc['legal'] }}</div>@endif
        </td>
        <td style="width:90px; border:none;"></td>
    </tr>
</table>
