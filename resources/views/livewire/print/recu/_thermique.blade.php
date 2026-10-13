{{-- Ticket thermique (80 / 85 mm) : un exemplaire. --}}
<div class="centre">
    @if (!empty($ecole['logo']) && file_exists($ecole['logo']))
        <img src="{{ $ecole['logo'] }}" style="height:32px;" alt="Logo">
    @endif
    <div class="nom">{{ $ecole['nom'] }}</div>
    @if (!empty($ecole['activite']))<div class="petit">{{ $ecole['activite'] }}</div>@endif
    <div class="petit">{{ $ecole['adresse'] }}</div>
    @if (!empty($ecole['contact']))<div class="petit">{{ $ecole['contact'] }}</div>@endif
    @if (!empty($ecole['legal']))<div class="petit">{{ $ecole['legal'] }}</div>@endif
</div>

<div class="sep"></div>

<div class="centre">
    <div class="titre">{{ $d['titre'] }}</div>
    <div class="petit">N° {{ $d['reference'] }}</div>
    <div class="petit">Date : {{ $d['date']->format('d/m/Y H:i') }}</div>
</div>

<div class="bandeau">{{ $exemplaire }} ({{ $rang }}/{{ $total }})</div>

<table class="lignes">
    @foreach ($d['lignes'] as $ligne)
        <tr @if (($ligne[2] ?? null) === 'fort') class="fort" @endif>
            <td class="l">{{ $ligne[0] }}</td>
            <td class="v">{{ $ligne[1] }}</td>
        </tr>
    @endforeach
</table>

@if ($d['montant'] !== null)
    <div class="montant">
        <div>{{ $d['montant_libelle'] }}</div>
        <div class="val">{{ $d['montant_format'] }}</div>
    </div>
    <div class="lettres">{{ $d['lettres'] }}</div>
@endif

<table class="signatures">
    <tr>
        <td>{{ $d['signatures'][0] }}@if ($d['emetteur'])<br>{{ $d['emetteur'] }}@endif</td>
        <td>{{ $d['signatures'][1] }}</td>
    </tr>
</table>

<div class="sep"></div>
<div class="note">{{ $d['note'] }}</div>
<div class="note">Imprimé le {{ now()->format('d/m/Y H:i') }}</div>
