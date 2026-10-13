@php
    $f = $personne['feminin'];
    $qualite = $certificat->qualite_signataire ?: config('documents.qualite');
    $cursus = trim(($classe ?? '') . ($niveau ? '  ( Niveau ' . $niveau . ($filiere ? ' - ' . $filiere : '') . ')' : ''));
    //  $cursus = trim(($niveau ?? '') );
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $titre }} {{ $certificat->numero }}</title>
    @include('livewire.print._certificat_styles')
</head>

<body>
    @include('livewire.print._certificat_entete')

    <div class="titre">{{ $titre }}</div>
    <div class="numero">N° {{ $certificat->numero }}</div>
    @if ($duplicata)<div class="duplicata">DUPLICATA</div>@endif

    <p class="texte">
        {{ $qualite }} de l'établissement <strong>{{ $ecole['nom'] }}</strong> certifie que :
    </p>
<p class="texte"> 
    <strong>{{ $personne['nom'] }} {{ $personne['prenom'] }}</strong>
        {{ $f ? 'Née' : 'Né' }} le {{ $personne['naissance'] }}@if ($personne['lieu']) à {{ $personne['lieu'] }}@endif</td></tr>
        @if ($personne['matricule'])
            Matricule {{ $personne['matricule'] }}
        @endif
        {{ $f ? 'est régulièrement inscrite' : 'est régulièrement inscrit' }}
        @if ($cursus) en classe de <strong>{{ $cursus }}</strong>@endif
        @if (!empty($annee)) au titre de l'année scolaire <strong>{{ date('Y', strtotime(AnneeScolaire()->date_debut)) . ' - ' . date('Y', strtotime(AnneeScolaire()->date_fin)) }}</strong>@endif
        dans notre établissement.
    </p>
    <p class="texte">
        En foi de quoi, le présent certificat lui est délivré pour servir et valoir ce que de droit.
    </p>

    @include('livewire.print._certificat_signature')
</body>

</html>
