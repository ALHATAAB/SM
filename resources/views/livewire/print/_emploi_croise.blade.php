{{--
    Tableau croisé d'emploi du temps : 1re colonne = créneaux horaires, en-tête = jours.
    Variables : $jours, $creneaux, $grille, $mode ('classe' => on affiche l'enseignant, 'enseignant' => on affiche la classe)
--}}
@php
    $joursAffiches = collect($jours)->filter(function ($jour) use ($grille) {
        if ($jour !== 'Dimanche') {
            return true;
        }
        return collect($grille)->contains(fn ($ligne) => !empty($ligne['Dimanche'] ?? null));
    })->values();
@endphp

<table class="croise">
    <thead>
        <tr>
            <th style="width: 85px;">Heures</th>
            @foreach ($joursAffiches as $jour)
                <th>{{ $jour }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($creneaux as $creneau)
            <tr>
                <td class="heures"><strong>{{ $creneau['debut'] }}</strong><br>à<br><strong>{{ $creneau['fin'] }}</strong></td>
                @foreach ($joursAffiches as $jour)
                    <td>
                        @foreach ($grille[$creneau['key']][$jour] ?? [] as $seance)
                            <div class="seance">
                                <strong>{{ $seance->matiere->matiere_etude ?? '' }}</strong>
                                @if ($mode === 'classe')
                                    <small>{{ trim(($seance->enseignant->nom ?? '') . ' ' . ($seance->enseignant->prenom ?? '')) }}</small>
                                @else
                                    <small>{{ $seance->classe->classe ?? '' }}</small>
                                @endif
                                <small>Salle : {{ $seance->salle->salle ?? '' }}</small>
                            </div>
                        @endforeach
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ $joursAffiches->count() + 1 }}"><em>Aucune séance</em></td>
            </tr>
        @endforelse
    </tbody>
</table>
