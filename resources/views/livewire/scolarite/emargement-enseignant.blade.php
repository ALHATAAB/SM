<div>

    <body class="fixed-navbar  ">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')
            <div class="content-wrapper">

                <div class="page-content fade-in-up">
                    <div class="row">

                        {{-- ===================== SAISIE GROUPÉE ===================== --}}
                        <div class="col-md-12">
                            <div class="ibox ibox-success">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('menu.Emargement_enseignant') }} — saisie groupée
                                    </div>
                                </div>
                                <div class="ibox-body">
                                    @include('livewire.alert.message')

                                    @unless ($anneeScolaireActive ?? true)
                                        <div class="alert alert-warning" role="alert">
                                            Aucune année scolaire active n'est configurée. Veuillez activer une
                                            année scolaire dans les paramètres avant d'enregistrer un émargement.
                                        </div>
                                    @endunless

                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>{{ __('message.Date') }}</label>
                                            <input type="date" wire:model.live="date" class="form-control">
                                            @error('date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Émargement par</label>
                                            <select wire:model.live="mode" class="form-control">
                                                <option value="enseignant">Enseignant</option>
                                                <option value="classe">Classe</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            @if ($mode === 'enseignant')
                                                <label>{{ __('message.Enseignant') }}</label>
                                                <select class="form-control" wire:model.live="enseignant_id">
                                                    <option value="">-- Enseignant --</option>
                                                    @foreach ($teachers as $t)
                                                        <option value="{{ $t->id }}">{{ $t->nom }}
                                                            {{ $t->prenom }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <label>Classe</label>
                                                <select class="form-control" wire:model.live="classe_id">
                                                    <option value="">-- Classe --</option>
                                                    @foreach ($classes as $c)
                                                        <option value="{{ $c->id }}">{{ $c->classe }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        @if ($date)
                                            <div class="col-sm-2 form-group">
                                                <label>&nbsp;</label>
                                                <div class="form-control-plaintext">
                                                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if (count($lignes))
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-success"
                                                wire:click="tousPresents"><i class="fa fa-check"></i> Tous
                                                présents</button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="tousAbsents"><i class="fa fa-times"></i> Tous
                                                absents</button>
                                            <button type="button" class="btn btn-sm btn-default"
                                                wire:click="reinitialiserHoraires"><i class="fa fa-clock-o"></i>
                                                Horaires prévus</button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Horaire prévu</th>
                                                        @if ($mode === 'classe')
                                                            <th>{{ __('message.Enseignant') }}</th>
                                                        @else
                                                            <th>Classe</th>
                                                        @endif
                                                        <th>{{ __('message.Matiere') }}</th>
                                                        <th>{{ __('message.Salle_etude') }}</th>
                                                        <th style="width:120px">Arrivée</th>
                                                        <th style="width:120px">Départ</th>
                                                        <th style="width:150px">{{ __('message.Statut') }}</th>
                                                        <th style="width:90px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($lignes as $i => $l)
                                                        <tr wire:key="ligne-{{ $l['seance_id'] }}"
                                                            @class([
                                                                'table-success' => $l['status'] === 'present',
                                                                'table-danger' => $l['status'] === 'absent',
                                                            ])>
                                                            <td class="align-middle">{{ $l['debut_prevu'] }} –
                                                                {{ $l['fin_prevue'] }}</td>
                                                            <td class="align-middle">
                                                                {{ $mode === 'classe' ? $l['enseignant'] : $l['classe'] }}
                                                            </td>
                                                            <td class="align-middle">{{ $l['matiere'] }}</td>
                                                            <td class="align-middle">{{ $l['salle'] }}</td>
                                                            <td>
                                                                <input type="time"
                                                                    wire:model="lignes.{{ $i }}.heure_arrivee"
                                                                    class="form-control form-control-sm">
                                                                @error("lignes.$i.heure_arrivee")
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="time"
                                                                    wire:model="lignes.{{ $i }}.heure_depart"
                                                                    class="form-control form-control-sm">
                                                                @error("lignes.$i.heure_depart")
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <select wire:model.live="lignes.{{ $i }}.status"
                                                                    class="form-control form-control-sm">
                                                                    <option value="">-- Statut --</option>
                                                                    <option value="present">Présent</option>
                                                                    <option value="absent">Absent</option>
                                                                </select>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($l['emargement_id'])
                                                                    <span class="badge badge-secondary"
                                                                        title="Déjà émargé : l'enregistrement mettra à jour">déjà
                                                                        saisi</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-group">
                                            <button class="btn btn-primary" type="button" wire:click="saveAll"
                                                wire:loading.attr="disabled" wire:target="saveAll"
                                                @disabled(!($anneeScolaireActive ?? true))>
                                                <i class="fa fa-save"></i>
                                                Enregistrer l'émargement ({{ collect($lignes)->whereIn('status', ['present', 'absent'])->count() }}/{{ count($lignes) }})
                                            </button>
                                            <small class="text-muted ml-2">Les lignes sans statut ne sont pas
                                                enregistrées.</small>
                                        </div>
                                    @elseif (($mode === 'enseignant' ? $enseignant_id : $classe_id) && $date)
                                        <div class="alert alert-info">Aucune séance prévue à l'emploi du temps ce
                                            jour-là.</div>
                                    @else
                                        <p class="text-muted">Choisissez une date et un
                                            {{ $mode === 'enseignant' ? 'enseignant' : 'une classe' }} pour afficher
                                            les séances à émarger.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ===================== HISTORIQUE ===================== --}}
                        <div class="col-md-12">
                            <div class="ibox ibox-primary">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Recherche') }}</div>
                                </div>
                                <div class="ibox-body">
                                    <span><strong><i class="ti ti-filter"></i> Filtre</strong></span>
                                    <form class="form-inline" onsubmit="return false;">
                                        <select class="mb-2 form-control mr-sm-2 mb-sm-0"
                                            wire:model.live="enseignantfilter">
                                            <option value="">-- Enseignant --</option>
                                            @foreach ($teachers as $t)
                                                <option value="{{ $t->id }}">{{ $t->nom }}
                                                    {{ $t->prenom }}</option>
                                            @endforeach
                                        </select>
                                        <select class="mb-2 form-control mr-sm-2 mb-sm-0"
                                            wire:model.live="classefilter">
                                            <option value="">-- Classe --</option>
                                            @foreach ($classes as $c)
                                                <option value="{{ $c->id }}">{{ $c->classe }}</option>
                                            @endforeach
                                        </select>
                                        <input class="mb-2 form-control mr-sm-2 mb-sm-0" type="date"
                                            wire:model.live="dateDebut" placeholder="{{ __('message.Date_debut') }}">
                                        <input class="mb-2 form-control mr-sm-2 mb-sm-0" type="date"
                                            wire:model.live="dateFin" placeholder="{{ __('message.Date_fin') }}">
                                        @if (!empty(Permission('Supprimer')) && count($selection))
                                            <button type="button" class="btn btn-danger mb-2 mb-sm-0"
                                                wire:click="supprimerSelection"
                                                wire:confirm="Supprimer les {{ count($selection) }} émargement(s) sélectionné(s) ?">
                                                <i class="fa fa-trash"></i> Supprimer la sélection
                                                ({{ count($selection) }})</button>
                                        @endif
                                    </form>
                                    <br>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th width="50px"></th>
                                                    <th>{{ __('message.Enseignant') }}</th>
                                                    <th>Classe</th>
                                                    <th>{{ __('message.Matiere') }}</th>
                                                    <th>{{ __('message.Date') }}</th>
                                                    <th>{{ __('message.heure_debut') }}</th>
                                                    <th>{{ __('message.heure_fin') }}</th>
                                                    <th>{{ __('message.Salle_etude') }}</th>
                                                    <th>{{ __('message.Statuts') }}</th>
                                                    @if (!empty(Permission('Supprimer')))
                                                        <th>{{ __('message.Actions') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($emargements as $emargement)
                                                    @php
                                                        // Paie du mois déjà payée pour cet enseignant => verrouillé
                                                        $verrouille = isset($moisPayes[$emargement->enseignant_id . '-' . Carbon\Carbon::parse($emargement->date)->month]);
                                                    @endphp
                                                    <tr wire:key="em-{{ $emargement->id }}">
                                                        <td>
                                                            @if (!empty(Permission('Supprimer')) && !$verrouille)
                                                                <label class="ui-checkbox">
                                                                    <input type="checkbox" wire:model.live="selection"
                                                                        value="{{ $emargement->id }}">
                                                                    <span class="input-span"></span>
                                                                </label>
                                                            @endif
                                                        </td>
                                                        <td>{{ $emargement->enseignant->nom }}
                                                            {{ $emargement->enseignant->prenom }}</td>
                                                        <td>{{ $emargement->classe->classe ?? '' }}</td>
                                                        <td>{{ $emargement->matiere->matiere_etude }}</td>
                                                        <td>{{ Carbon\Carbon::parse($emargement->date)->translatedFormat('l d F Y') }}
                                                        </td>
                                                        <td>{{ $emargement->heure_arrivee }}</td>
                                                        <td>{{ $emargement->heure_depart }}</td>
                                                        <td>{{ $emargement->salle->salle }}</td>
                                                        <td>
                                                            @if ($emargement->status === 'present')
                                                                <span
                                                                    class="badge badge-success m-r-5 m-b-5">PRESENT</span>
                                                            @endif
                                                            @if ($emargement->status === 'absent')
                                                                <span class="badge badge-danger m-b-5">ABSENT</span>
                                                            @endif
                                                            @if ($emargement->status === 'retard')
                                                                <span
                                                                    class="badge badge-warning m-r-5 m-b-5">RETARD</span>
                                                            @endif
                                                        </td>
                                                        @if (!empty(Permission('Supprimer')))
                                                            <td>
                                                                @if ($verrouille)
                                                                    <span class="text-muted"
                                                                        title="Paie du mois déjà payée : suppression impossible">
                                                                        <i class="fa fa-lock font-14"></i></span>
                                                                @else
                                                                    <button class="btn btn-default btn-xs"
                                                                        wire:click.prevent="DeleteEmargement({{ $emargement->id }})"
                                                                        wire:confirm="Supprimer cet émargement ? Le calcul de paie en attente de cet enseignant pour ce mois sera réinitialisé.">
                                                                        <i class="fa fa-trash font-14"></i></button>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div>{{ $emargements->links() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('components.layouts.footer')
            </div>
        </div>
        @include('components.layouts.loading')

    </body>

</div>
