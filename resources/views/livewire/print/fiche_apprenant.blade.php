<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche élève - {{ $f['nom_complet'] }}</title>
    @include('livewire.print._fiche_styles')
</head>
<body>
    @php $fmt = fn ($v) => number_format((float) $v, 0, ',', ' '); @endphp
    @include('livewire.print._entete_societe')

    <h1 class="titre">Fiche de l'élève</h1>
    <div class="sous-titre">{{ $f['nom_complet'] }} — édité le {{ now()->format('d/m/Y') }}</div>

    <h2 class="section">Identité</h2>
    @include('livewire.print._fiche_identite', ['lignes' => $f['identite'], 'photo' => $f['photo'] ?? null])

    <h2 class="section">Parent / tuteur</h2>
    @include('livewire.print._fiche_infos', ['lignes' => $f['parent']])

    @if (!empty($f['documents']))
        <h2 class="section">Documents du dossier</h2>
        @include('livewire.print._fiche_infos', ['lignes' => $f['documents']])
    @endif

    <h2 class="section">Parcours scolaire</h2>
    @if (count($f['inscriptions']))
        <table class="liste">
            <thead><tr>
                <th>Année</th><th>Matricule</th><th>Niveau</th><th>Filière</th><th>Classe</th>
                <th class="droite">Inscription</th><th class="droite">Scolarité</th><th class="droite">Remise</th>
            </tr></thead>
            <tbody>
                @foreach ($f['inscriptions'] as $i)
                    <tr>
                        <td>{{ $i['annee'] }}</td><td>{{ $i['matricule'] }}</td><td>{{ $i['niveau'] }}</td>
                        <td>{{ $i['filiere'] }}</td><td>{{ $i['classe'] }}</td>
                        <td class="droite">{{ $fmt($i['inscription']) }}</td>
                        <td class="droite">{{ $fmt($i['scolarite']) }}</td>
                        <td class="droite">{{ $fmt($i['remise']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune inscription.</div>
    @endif

    <h2 class="section">Situation financière (scolarité)</h2>
    <table class="resume"><tr>
        <td><span class="val">{{ $fmt($f['resume']['du']) }}</span><span class="lab">Total dû (F CFA)</span></td>
        <td><span class="val">{{ $fmt($f['resume']['remise']) }}</span><span class="lab">Remises</span></td>
        <td><span class="val">{{ $fmt($f['resume']['paye']) }}</span><span class="lab">Total payé</span></td>
        <td><span class="val">{{ $fmt($f['resume']['reste']) }}</span><span class="lab">Reste à payer</span></td>
    </tr></table>

    <h2 class="section">Paiements de scolarité</h2>
    @if (count($f['paiements']))
        <table class="liste">
            <thead><tr><th>Date</th><th>Année</th><th>Libellé</th><th>Mode</th><th class="droite">Montant</th></tr></thead>
            <tbody>
                @foreach ($f['paiements'] as $p)
                    <tr><td>{{ $p['date'] }}</td><td>{{ $p['annee'] }}</td><td>{{ $p['libelle'] }}</td>
                        <td>{{ $p['mode'] }}</td><td class="droite">{{ $fmt($p['montant']) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="4" class="droite">Total payé</th><th class="droite">{{ $fmt($f['resume']['paye']) }}</th></tr></tfoot>
        </table>
    @else
        <div class="vide">Aucun paiement enregistré.</div>
    @endif

    <h2 class="section">Abonnements (cantine, transport…)</h2>
    @if (count($f['abonnements']))
        <table class="liste">
            <thead><tr><th>Année</th><th>Type</th><th>Période</th><th>Du</th><th>Au</th><th>Mode</th><th class="droite">Montant</th></tr></thead>
            <tbody>
                @foreach ($f['abonnements'] as $a)
                    <tr><td>{{ $a['annee'] }}</td><td>{{ $a['type'] }}</td><td>{{ $a['periode'] }}</td>
                        <td>{{ $a['debut'] }}</td><td>{{ $a['fin'] }}</td><td>{{ $a['mode'] }}</td>
                        <td class="droite">{{ $fmt($a['montant']) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucun abonnement.</div>
    @endif

    <h2 class="section">Notes</h2>
    @if (count($f['notes']))
        <table class="liste">
            <thead><tr><th>Trimestre</th><th>Classe</th><th>Matière</th><th>Type</th><th class="droite">Note</th></tr></thead>
            <tbody>
                @foreach ($f['notes'] as $n)
                    <tr><td>{{ $n['trimestre'] }}</td><td>{{ $n['classe'] }}</td><td>{{ $n['matiere'] }}</td>
                        <td>{{ $n['type'] }}</td><td class="droite">{{ rtrim(rtrim(number_format($n['note'], 2, ',', ' '), '0'), ',') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune note enregistrée.</div>
    @endif

    <h2 class="section">Absences ({{ $f['resume']['absences'] }} dont {{ $f['resume']['absences_non_justifiees'] }} non justifiée(s))</h2>
    @if (count($f['absences']))
        <table class="liste">
            <thead><tr><th>Date</th><th>Horaire</th><th>Matière</th><th>Enseignant</th><th>Motif</th><th>Justifiée</th></tr></thead>
            <tbody>
                @foreach ($f['absences'] as $a)
                    <tr><td>{{ $a['date'] }}</td><td>{{ $a['horaire'] }}</td><td>{{ $a['matiere'] }}</td>
                        <td>{{ $a['enseignant'] }}</td><td>{{ $a['motif'] }}</td>
                        <td class="centre">{{ $a['justifiee'] ? 'Oui' : 'Non' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune absence.</div>
    @endif

    <table class="signatures"><tr><td>Le parent / tuteur</td><td>La Direction</td></tr></table>
    <div class="pied">{{ $ecole['nom'] }} — Fiche élève éditée le {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
