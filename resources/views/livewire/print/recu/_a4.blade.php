{{-- A4 : les deux exemplaires sur la même feuille, séparés par un trait de coupe.
     Si le reçu est très long (> 13 lignes), chaque exemplaire passe sur sa propre feuille. --}}
@php $deuxParPage = count($d['lignes']) <= 13; @endphp

@foreach ($exemplaires as $i => $exemplaire)
    <div class="bloc">
        <table class="entete">
            <tr>
                <td class="logo">
                    @if (!empty($ecole['logo']) && file_exists($ecole['logo']))
                        <img src="{{ $ecole['logo'] }}" style="height:48px;" alt="Logo">
                    @endif
                </td>
                <td class="soc">
                    <div class="nom">{{ $ecole['nom'] }}</div>
                    @if (!empty($ecole['activite']))<div>{{ $ecole['activite'] }}</div>@endif
                    <div>{{ $ecole['adresse'] }}</div>
                    @if (!empty($ecole['contact']))<div>{{ $ecole['contact'] }}</div>@endif
                    @if (!empty($ecole['legal']))<div>{{ $ecole['legal'] }}</div>@endif
                </td>
                <td class="ex">
                    <div class="badge">{{ $exemplaire }}</div>
                    <div style="margin-top:2px;">{{ $i + 1 }} / {{ count($exemplaires) }}</div>
                </td>
            </tr>
        </table>
        <div class="sep-entete"></div>

        <table class="titre">
            <tr>
                <td class="t">{{ mb_strtoupper($d['titre']) }}</td>
                <td class="r">N° {{ $d['reference'] }}<br>Date : {{ $d['date']->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <table class="lignes">
            @foreach ($d['lignes'] as $ligne)
                <tr @if (($ligne[2] ?? null) === 'fort') class="fort" @endif>
                    <td class="l">{{ $ligne[0] }}</td>
                    <td class="v">{{ $ligne[1] }}</td>
                </tr>
            @endforeach
        </table>

        @if ($d['montant'] !== null)
            <table class="montant">
                <tr>
                    <td class="lib">{{ $d['montant_libelle'] }}</td>
                    <td class="val">{{ $d['montant_format'] }}</td>
                </tr>
            </table>
            <div class="lettres">{{ $d['lettres'] }}</div>
        @endif

        <table class="signatures">
            <tr>
                <td>{{ $d['signatures'][0] }}@if ($d['emetteur'])<br><span style="font-size:9px;">{{ $d['emetteur'] }}</span>@endif</td>
                <td>{{ $d['signatures'][1] }}</td>
            </tr>
        </table>

        <div class="note">{{ $d['note'] }} — Imprimé le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    @unless ($loop->last)
        @if ($deuxParPage)
            <div class="coupe">- - - - - - - - - - - - - - - - - - - - Couper ici - - - - - - - - - - - - - - - - - - - -</div>
        @else
            <div style="page-break-after: always;"></div>
        @endif
    @endunless
@endforeach
