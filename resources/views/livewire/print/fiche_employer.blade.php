<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche personnel - {{ $f['nom_complet'] }}</title>
    @include('livewire.print._fiche_styles')
</head>
<body>
    @php $fmt = fn ($v) => number_format((float) $v, 0, ',', ' '); @endphp
    @include('livewire.print._entete_societe')

    <h1 class="titre">Fiche du personnel</h1>
    <div class="sous-titre">{{ $f['nom_complet'] }} — édité le {{ now()->format('d/m/Y') }}</div>

    <h2 class="section">Identité</h2>
    @include('livewire.print._fiche_identite', ['lignes' => $f['identite'], 'photo' => $f['photo'] ?? null])

    <h2 class="section">Situation professionnelle</h2>
    @include('livewire.print._fiche_infos', ['lignes' => $f['professionnel']])

    <h2 class="section">Bulletin modèle (rubriques de paie)</h2>
    @if (count($f['gains']) || count($f['retenues']))
        <table class="liste">
            <thead><tr><th>Rubrique</th><th>Nature</th><th class="droite">Montant</th></tr></thead>
            <tbody>
                @foreach ($f['gains'] as $g)
                    <tr><td>{{ $g['libelle'] }}</td><td>Gain</td><td class="droite">{{ $fmt($g['montant']) }}</td></tr>
                @endforeach
                @foreach ($f['retenues'] as $r)
                    <tr><td>{{ $r['libelle'] }}</td><td>Retenue</td><td class="droite">{{ $fmt($r['montant']) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune rubrique sur le bulletin modèle.</div>
    @endif

    <h2 class="section">Historique des fiches de paie</h2>
    @if (count($f['fiches']))
        <table class="liste">
            <thead><tr><th>Période</th><th class="droite">Total gain</th><th class="droite">Total retenue</th>
                <th class="droite">Net à payer</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach ($f['fiches'] as $x)
                    <tr><td>{{ $x['periode'] }}</td><td class="droite">{{ $fmt($x['gain']) }}</td>
                        <td class="droite">{{ $fmt($x['retenu']) }}</td><td class="droite">{{ $fmt($x['net']) }}</td>
                        <td>{{ $x['statut'] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune fiche de paie calculée.</div>
    @endif

    <h2 class="section">Salaires réglés</h2>
    @if (count($f['paiements']))
        <table class="liste">
            <thead><tr><th>Date</th><th>Période</th><th>Mode</th><th class="droite">Montant</th></tr></thead>
            <tbody>
                @foreach ($f['paiements'] as $p)
                    <tr><td>{{ $p['date'] }}</td><td>{{ $p['periode'] }}</td><td>{{ $p['mode'] }}</td>
                        <td class="droite">{{ $fmt($p['montant']) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="3" class="droite">Total réglé</th><th class="droite">{{ $fmt($f['total_net_paye']) }}</th></tr></tfoot>
        </table>
    @else
        <div class="vide">Aucun règlement enregistré.</div>
    @endif

    <table class="signatures"><tr><td>L'employé</td><td>Le Responsable RH</td></tr></table>
    <div class="pied">{{ $ecole['nom'] }} — Fiche personnel éditée le {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
