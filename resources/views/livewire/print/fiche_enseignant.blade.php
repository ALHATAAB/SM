<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche enseignant - {{ $f['nom_complet'] }}</title>
    @include('livewire.print._fiche_styles')
</head>
<body>
    @php $fmt = fn ($v) => number_format((float) $v, 0, ',', ' '); @endphp
    @include('livewire.print._entete_societe')

    <h1 class="titre">Fiche de l'enseignant</h1>
    <div class="sous-titre">{{ $f['nom_complet'] }} — édité le {{ now()->format('d/m/Y') }}</div>

    <h2 class="section">Identité</h2>
    @include('livewire.print._fiche_identite', ['lignes' => $f['identite'], 'photo' => $f['photo'] ?? null])

    <h2 class="section">Situation professionnelle</h2>
    @include('livewire.print._fiche_infos', ['lignes' => $f['professionnel']])

    <h2 class="section">Emploi du temps</h2>
    @if (count($f['seances']))
        <table class="liste">
            <thead><tr><th>Jour</th><th>Horaire</th><th>Matière</th><th>Classe</th><th>Salle</th></tr></thead>
            <tbody>
                @foreach ($f['seances'] as $s)
                    <tr><td>{{ $s['jour'] }}</td><td>{{ $s['horaire'] }}</td><td>{{ $s['matiere'] }}</td>
                        <td>{{ $s['classe'] }}</td><td>{{ $s['salle'] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucune séance programmée.</div>
    @endif

    <h2 class="section">Présence{{ $f['annee'] ? ' — année ' . $f['annee'] : '' }}</h2>
    <table class="resume"><tr>
        <td><span class="val">{{ $f['presence']['presents'] }}</span><span class="lab">Présences</span></td>
        <td><span class="val">{{ $f['presence']['absents'] }}</span><span class="lab">Absences</span></td>
        <td><span class="val">{{ rtrim(rtrim(number_format($f['presence']['heures'], 2, ',', ' '), '0'), ',') }}</span><span class="lab">Heures effectuées</span></td>
        <td><span class="val">{{ count($f['heures_par_matiere']) }}</span><span class="lab">Matière(s) / classe(s)</span></td>
    </tr></table>

    @if (count($f['heures_par_matiere']))
        <h2 class="section">Heures effectuées par matière et classe</h2>
        <table class="liste">
            <thead><tr><th>Matière</th><th>Classe</th><th class="droite">Séances</th><th class="droite">Heures</th></tr></thead>
            <tbody>
                @foreach ($f['heures_par_matiere'] as $h)
                    <tr><td>{{ $h['matiere'] }}</td><td>{{ $h['classe'] }}</td>
                        <td class="droite">{{ $h['seances'] }}</td>
                        <td class="droite">{{ rtrim(rtrim(number_format($h['heures'], 2, ',', ' '), '0'), ',') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2 class="section">États de paie</h2>
    @if (count($f['etats_paie']))
        <table class="liste">
            <thead><tr><th>Période</th><th class="droite">Montant de base</th><th class="droite">H. sup.</th>
                <th class="droite">Montant H. sup.</th><th class="droite">Total</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach ($f['etats_paie'] as $e)
                    <tr><td>{{ $e['periode'] }}</td><td class="droite">{{ $fmt($e['base']) }}</td>
                        <td class="droite">{{ $fmt($e['hsup']) }}</td><td class="droite">{{ $fmt($e['montant_hsup']) }}</td>
                        <td class="droite">{{ $fmt($e['total']) }}</td><td>{{ $e['statut'] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="vide">Aucun état de paie.</div>
    @endif

    <h2 class="section">Règlements effectués</h2>
    @if (count($f['paiements']))
        <table class="liste">
            <thead><tr><th>Date</th><th>Période</th><th>Mode</th><th class="droite">Montant</th></tr></thead>
            <tbody>
                @foreach ($f['paiements'] as $p)
                    <tr><td>{{ $p['date'] }}</td><td>{{ $p['periode'] }}</td><td>{{ $p['mode'] }}</td>
                        <td class="droite">{{ $fmt($p['montant']) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="3" class="droite">Total réglé</th><th class="droite">{{ $fmt($f['total_paye']) }}</th></tr></tfoot>
        </table>
    @else
        <div class="vide">Aucun règlement enregistré.</div>
    @endif

    <table class="signatures"><tr><td>L'enseignant</td><td>La Direction</td></tr></table>
    <div class="pied">{{ $ecole['nom'] }} — Fiche enseignant éditée le {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
