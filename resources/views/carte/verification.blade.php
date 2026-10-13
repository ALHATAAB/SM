<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de carte {{ $c['numero'] }}</title>
    @php
        $valide = $statut === 'active';
        $couleur = $valide ? '#1e7e34' : '#b02a37';
        $libelles = \App\Services\CarteService::STATUTS;
        $libelle = $valide ? 'CARTE VALABLE' : 'CARTE NON VALABLE : ' . mb_strtoupper($libelles[$statut] ?? $statut);
    @endphp
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f2f4f7; margin: 0; padding: 16px; color: #222; }
        .boite { max-width: 420px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.15); }
        .etat { background: {{ $couleur }}; color: #fff; text-align: center; font-weight: bold; padding: 14px 10px; font-size: 18px; }
        .ecole { text-align: center; padding: 12px 10px 0; color: #555; font-size: 14px; }
        .photo { text-align: center; padding: 12px 0 0; }
        .photo img { width: 110px; height: 135px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px; }
        h1 { font-size: 20px; text-align: center; margin: 10px 10px 2px; }
        .type { text-align: center; color: {{ $c['couleur'] }}; font-weight: bold; font-size: 13px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
        td { padding: 7px 16px; border-top: 1px solid #eee; font-size: 14px; }
        td.l { color: #666; width: 42%; }
        .retour { text-align: center; padding: 0 0 16px; font-size: 13px; }
        .retour a { color: #1f4e79; }
    </style>
</head>
<body>
<div class="boite">
    <div class="etat">{{ $libelle }}</div>
    <div class="ecole">{{ $c['ecole']['nom'] ?? '' }}</div>
    @if ($c['photo'])
        <div class="photo"><img src="{{ url('/photo/' . $c['personne_type'] . '/' . $c['personne_id']) }}" alt=""></div>
    @endif
    <h1>{{ $c['nom'] }} {{ $c['prenom'] }}</h1>
    <div class="type">{{ $c['titre'] }}</div>
    <table>
        <tr><td class="l">N° de carte</td><td><b>{{ $c['numero'] }}</b></td></tr>
        @foreach ($c['details'] as $d)
            <tr><td class="l">{{ $d[0] }}</td><td>{{ $d[1] }}</td></tr>
        @endforeach
        @if ($c['annee'])<tr><td class="l">Année scolaire</td><td>{{ $c['annee'] }}</td></tr>@endif
        <tr><td class="l">Émise le</td><td>{{ $c['emission'] }}</td></tr>
        <tr><td class="l">Valable jusqu'au</td><td><b>{{ $c['expiration'] }}</b></td></tr>
    </table>
    <div class="retour"><a href="{{ url('/home') }}">Retour à l'application</a></div>
</div>
</body>
</html>
