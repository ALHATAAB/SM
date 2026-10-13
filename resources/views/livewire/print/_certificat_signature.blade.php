{{-- « Fait à … le … » + cachet et signature. --}}
@php
    $qualite = $certificat->qualite_signataire ?: config('documents.qualite');
@endphp
<div class="fait">Fait à {{ $ville }}, le {{ $certificat->date_emission->format('d/m/Y') }}</div>
<div class="signature">
    <div>{{ $qualite }}</div>
    <div class="espace"></div>
    @if ($certificat->signataire)
        <div class="nom">{{ $certificat->signataire }}</div>
    @endif
    <div style="font-size:9px; color:#555;">(Cachet et signature)</div>
</div>
<div class="pied">
    Certificat n° {{ $certificat->numero }}
    @if ($duplicata) — DUPLICATA @endif
    — {{ $ecole['nom'] }}@if (!empty($ecole['adresse'])) — {{ $ecole['adresse'] }}@endif
</div>
