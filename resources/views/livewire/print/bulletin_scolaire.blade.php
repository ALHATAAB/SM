<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 22px 28px; }
        body { font-family: sans-serif; font-size: 10px; color: #111; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        .entete { width: 100%; border-bottom: 2px solid #222; padding-bottom: 6px; margin-bottom: 8px; }
        .entete td { vertical-align: middle; }
        .ecole { text-align: center; }
        .ecole h2 { margin: 0; font-size: 17px; letter-spacing: 1px; }
        .ecole p { margin: 1px 0; font-size: 9px; }

        .titre { text-align: center; margin: 6px 0 8px; }
        .titre h3 { margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .titre p { margin: 2px 0 0; font-size: 10px; }

        table.infos { width: 100%; border: 1px solid #444; border-collapse: collapse; margin-bottom: 8px; }
        table.infos td { padding: 3px 6px; font-size: 10px; }

        table.notes { width: 100%; border-collapse: collapse; }
        table.notes th, table.notes td { border: 1px solid #555; padding: 3px 4px; font-size: 9.5px; }
        table.notes th { background: #e9ecef; text-align: center; }
        table.notes td.c { text-align: center; }
        table.notes tr.total td { background: #f4f4f4; font-weight: bold; }

        table.resume { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.resume td { border: 1px solid #555; padding: 4px 6px; font-size: 10px; }
        table.resume td.k { background: #e9ecef; font-weight: bold; width: 22%; }

        .moy { font-size: 13px; font-weight: bold; }
        .admis { color: #146c2e; font-weight: bold; }
        .nonadmis { color: #a11; font-weight: bold; }

        table.sign { width: 100%; margin-top: 10px; border-collapse: collapse; }
        table.sign td { border: 1px solid #555; height: 62px; vertical-align: top; padding: 4px 6px; font-size: 9.5px; width: 33.33%; }
        .pied { text-align: center; font-size: 8px; color: #666; margin-top: 6px; }
    </style>
</head>

<body>
@php
    $f = fn ($v) => $v === null ? '-' : number_format((float) $v, 2, ',', '');
    $rang = fn ($r) => $r === null ? '-' : ($r === 1 ? '1er' : $r . 'e');
    $nbTri = count($trimestres);
    $max = config('bulletin.note_max', 20);
@endphp

@foreach ($bulletins as $b)
    <div class="page">

        @include('livewire.print._entete_societe')

        <div class="titre">
            <h3>{{ $annuel ? 'Bulletin annuel' : 'Bulletin de notes' }}</h3>
            <p>
                @if (!$annuel) {{ $periodeLabel }} — @endif
                Année scolaire {{ $anneeLabel }}
            </p>
        </div>

        <table class="infos">
            <tr>
                <td><strong>Nom :</strong> {{ $b['nom'] }}</td>
                <td><strong>Prénom :</strong> {{ $b['prenom'] }}</td>
                <td><strong>Matricule :</strong> {{ $b['matricule'] }}</td>
            </tr>
            <tr>
                <td>
                    <strong>Né(e) le :</strong>
                    {{ !empty($b['date_naissance']) ? \Carbon\Carbon::parse($b['date_naissance'])->format('d/m/Y') : '-' }}
                    @if (!empty($b['lieu_naissance'])) à {{ $b['lieu_naissance'] }} @endif
                </td>
                <td><strong>Sexe :</strong> {{ $b['sexe'] ?: '-' }}</td>
                <td><strong>Classe :</strong> {{ $classeNom }} &nbsp; <strong>Effectif :</strong> {{ $b['effectif'] }}</td>
            </tr>
        </table>

        <table class="notes">
            <thead>
                <tr>
                    <th style="text-align:left;">Matières</th>
                    <th>Coef.</th>
                    @if ($annuel)
                        @foreach ($trimestres as $t)
                            <th>{{ $t['nom'] }}</th>
                        @endforeach
                        <th>Moy. annuelle</th>
                    @else
                        <th>Note de classe</th>
                        <th>Composition</th>
                        <th>Moyenne</th>
                    @endif
                    <th>Moy. × Coef.</th>
                    <th>Moy. classe</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($b['lignes'] as $l)
                    <tr>
                        <td>{{ $l['matiere'] }}</td>
                        <td class="c">{{ rtrim(rtrim(number_format($l['coef'], 2, ',', ''), '0'), ',') }}</td>
                        @if ($annuel)
                            @foreach ($trimestres as $t)
                                <td class="c">{{ $f($l['trimestres'][$t['id']] ?? null) }}</td>
                            @endforeach
                            <td class="c"><strong>{{ $f($l['moyenne']) }}</strong></td>
                        @else
                            <td class="c">{{ $f($l['classe']) }}</td>
                            <td class="c">{{ $f($l['composition']) }}</td>
                            <td class="c"><strong>{{ $f($l['moyenne']) }}</strong></td>
                        @endif
                        <td class="c">{{ $f($l['total']) }}</td>
                        <td class="c">{{ $f($l['moy_classe']) }}</td>
                        <td class="c">{{ $l['appreciation'] ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td>TOTAL</td>
                    <td class="c">{{ rtrim(rtrim(number_format($b['total_coef'], 2, ',', ''), '0'), ',') ?: '0' }}</td>
                    <td colspan="{{ $annuel ? $nbTri + 1 : 3 }}"></td>
                    <td class="c">{{ $f($b['total_points']) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <table class="resume">
            <tr>
                <td class="k">Moyenne générale</td>
                <td><span class="moy">{{ $f($b['moyenne']) }}</span> / {{ $max }}</td>
                <td class="k">Rang</td>
                <td>{{ $rang($b['rang']) }} sur {{ $b['effectif'] }}</td>
            </tr>
            <tr>
                <td class="k">Moyenne de la classe</td>
                <td>{{ $f($stats['moyenne']) }}</td>
                <td class="k">Plus forte / plus faible</td>
                <td>{{ $f($stats['max']) }} / {{ $f($stats['min']) }}</td>
            </tr>
            <tr>
                <td class="k">Appréciation</td>
                <td>{{ $b['appreciation'] ?? '-' }}</td>
                <td class="k">Résultat</td>
                <td>
                    @if ($b['moyenne'] === null)
                        -
                    @elseif ($b['admis'])
                        <span class="admis">Moyenne atteinte</span>
                    @else
                        <span class="nonadmis">Moyenne non atteinte</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="k">Absences</td>
                <td colspan="3">
                    {{ $b['absences']['total'] }} séance(s), dont {{ $b['absences']['justifiees'] }} justifiée(s)
                </td>
            </tr>
        </table>

        <table class="sign">
            <tr>
                <td><strong>Observations du conseil de classe</strong></td>
                <td><strong>Le Directeur</strong></td>
                <td><strong>Le Parent / Tuteur</strong></td>
            </tr>
        </table>

        <div class="pied">Édité le {{ now()->format('d/m/Y') }}</div>
    </div>
@endforeach

</body>

</html>
