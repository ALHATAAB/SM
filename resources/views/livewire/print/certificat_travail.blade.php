@php
    $f = $personne['feminin'];
    $c = $certificat;
    $qualite = $c->qualite_signataire ?: config('documents.qualite');
    $termine = $c->date_fin && $c->date_fin->lt(\Carbon\Carbon::today());
    $il = $f ? 'Elle' : 'Il';
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $titre }} {{ $c->numero }}</title>
    @include('livewire.print._certificat_styles')
</head>

<body>
    @include('livewire.print._certificat_entete')

    <div class="titre">{{ $titre }}</div>
    <div class="numero">N° {{ $c->numero }}</div>
    @if ($duplicata)<div class="duplicata">DUPLICATA</div>@endif

    <p class="texte">
        {{ $qualite }} de l'établissement <strong>{{ $ecole['nom'] }}</strong>@if (!empty($ecole['adresse'])), situé à {{ $ecole['adresse'] }},@endif
        certifie que :
    </p>
<p class="texte"> 
    
    <strong>{{ $f ? 'Mme/Mlle.' : 'Mr.' }} {{ $personne['nom'] }} {{ $personne['prenom'] }}</strong>
        {{ $f ? 'Née' : 'Né' }} le {{ $personne['naissance'] }}@if ($personne['lieu']) à {{ $personne['lieu'] }}@endif</td></tr>
        @if ($personne['matricule'])
            Matricule {{ $personne['matricule'] }}
        @endif
    
        {{ $termine ? 'a travaillé' : 'travaille' }} au sein de notre établissement en qualité de
        <strong>{{ $c->fonction }}</strong>
        @if ($c->date_fin)
            du <strong>{{ $c->date_debut->format('d/m/Y') }}</strong> au <strong>{{ $c->date_fin->format('d/m/Y') }}</strong>.
        @else
            depuis le <strong>{{ $c->date_debut->format('d/m/Y') }}</strong> à ce jour.
        @endif
        @if ($c->libre_engagement)
            {{ $il }} nous quitte libre de tout engagement.
        @endif
    </p>

    <p class="texte">
        En foi de quoi, le présent certificat lui est délivré pour servir et valoir ce que de droit.
    </p>

    @include('livewire.print._certificat_signature')
</body>

</html>
