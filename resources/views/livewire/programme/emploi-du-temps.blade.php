<div>

    <body class="fixed-navbar  ">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')
            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    @if (!empty(Permission('Afficher')))
                        @php
                            $joursVisibles = $this->joursAffiches();
                            $labelAutre = $type === 'classe' ? 'Enseignant' : 'Classe';
                            $nbSeances = collect($grid)->flatten(1)->filter(fn ($c) => ($c['matiere_id'] ?? '') !== '' && ($c['salle_id'] ?? '') !== '' && ($c['autre_id'] ?? '') !== '')->count();
                        @endphp

                        <style>
                            .colonne-heures { position: sticky; left: 0; z-index: 2; width: 180px; min-width: 180px; box-shadow: 2px 0 3px rgba(0,0,0,.08); }
                            .colonne-heures input[type="time"] { min-width: 90px; font-size: 14px; }
                        </style>

                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Planification_seance') }} — tableau croisé</div>
                            </div>
                            <div class="ibox-body">
                                @include('livewire.alert.message')

                                {{-- Choix : par classe ou par enseignant --}}
                                <div class="p-3 mb-3 rounded"
                                    style="background-color:rgb(82, 78, 78); color:rgb(255, 255, 255)">
                                    <p><strong><i class="ti ti-filter"></i> Emploi du temps par classe ou par
                                            enseignant</strong></p>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <select wire:model.live="type" class="form-control">
                                                <option value="classe">Par Classe</option>
                                                <option value="enseignant">Par Enseignant</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <select wire:model.live="selectedId" class="form-control">
                                                <option value="">Sélectionnez</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $type === 'classe' ? $item->classe : $item->nom . ' ' . $item->prenom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($selectedId)
                                            <div class="col-md-3 mb-2">
                                                @if ($type === 'classe')
                                                    <button class="btn btn-secondary"
                                                        wire:click.prevent="printEmpClasse({{ $selectedId }})"><i
                                                            class="fa fa-print"></i> Imprimer</button>
                                                @else
                                                    <button class="btn btn-secondary"
                                                        wire:click.prevent="printEmpEnseignant({{ $selectedId }})"><i
                                                            class="fa fa-print"></i> Imprimer</button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($selectedId)
                                    @if (count($conflits))
                                        <div class="alert alert-danger">
                                            <strong>Enregistrement impossible : conflits d'horaire détectés.</strong>
                                            <ul class="mb-0">
                                                @foreach ($conflits as $c)
                                                    <li>{{ $c }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <p class="text-muted mb-2">
                                        Chaque ligne est un créneau horaire, chaque colonne un jour. Remplissez les
                                        cellules (matière, salle, {{ strtolower($labelAutre) }}) puis enregistrez
                                        <strong>une seule fois</strong>. Une cellule vide = pas de cours.
                                    </p>

                                    {{-- Tableau croisé : 1re colonne = heures, en-tête = jours --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="table-layout:fixed; min-width:{{ 180 + count($joursVisibles) * 175 }}px">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center align-middle colonne-heures" style="width:180px">Heures
                                                    </th>
                                                    @foreach ($joursVisibles as $jour)
                                                        <th class="text-center align-middle">{{ $jour }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($slots as $i => $slot)
                                                    <tr wire:key="slot-{{ $i }}">
                                                        {{-- Colonne des heures (figée à gauche au défilement) --}}
                                                        <td class="align-middle bg-light colonne-heures">
                                                            <div class="input-group input-group-sm mb-1">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text justify-content-center" style="width:34px">De</span>
                                                                </div>
                                                                <input type="time" wire:model="slots.{{ $i }}.debut"
                                                                    class="form-control text-center font-weight-bold" title="Heure de début">
                                                            </div>
                                                            <div class="input-group input-group-sm mb-1">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text justify-content-center" style="width:34px">À</span>
                                                                </div>
                                                                <input type="time" wire:model="slots.{{ $i }}.fin"
                                                                    class="form-control text-center font-weight-bold" title="Heure de fin">
                                                            </div>
                                                            @error("slots.$i")
                                                                <small class="text-danger d-block text-center">{{ $message }}</small>
                                                            @enderror
                                                            <div class="text-center">
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                    title="Recopier la 1re séance de la ligne sur les jours vides"
                                                                    wire:click="copierLigne({{ $i }})"><i
                                                                        class="fa fa-copy"></i> Copier</button>
                                                                <button type="button" class="btn btn-default btn-xs"
                                                                    title="Supprimer ce créneau et ses séances"
                                                                    wire:click="supprimerCreneau({{ $i }})"
                                                                    wire:confirm="Supprimer ce créneau ? Ses séances seront supprimées à l'enregistrement."><i
                                                                        class="fa fa-trash"></i></button>
                                                            </div>
                                                        </td>

                                                        {{-- Une cellule par jour --}}
                                                        @foreach ($joursVisibles as $jour)
                                                            @php($cellule = $grid[$i][$jour] ?? [])
                                                            @php($remplie = ($cellule['matiere_id'] ?? '') !== '' || ($cellule['salle_id'] ?? '') !== '' || ($cellule['autre_id'] ?? '') !== '')
                                                            <td wire:key="cell-{{ $i }}-{{ $jour }}"
                                                                @class(['p-1', 'table-info' => $remplie])
                                                                @style(['background-color:#fff' => !$remplie])>
                                                                <select wire:model.live="grid.{{ $i }}.{{ $jour }}.matiere_id"
                                                                    class="form-control form-control-sm mb-1">
                                                                    <option value="">Matière</option>
                                                                    @foreach ($matieres as $m)
                                                                        <option value="{{ $m->id }}">
                                                                            {{ $m->matiere_etude }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <select wire:model.live="grid.{{ $i }}.{{ $jour }}.salle_id"
                                                                    class="form-control form-control-sm mb-1">
                                                                    <option value="">Salle</option>
                                                                    @foreach ($salles as $s)
                                                                        <option value="{{ $s->id }}">
                                                                            {{ $s->salle }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <select wire:model.live="grid.{{ $i }}.{{ $jour }}.autre_id"
                                                                    class="form-control form-control-sm">
                                                                    <option value="">{{ $labelAutre }}</option>
                                                                    @if ($type === 'classe')
                                                                        @foreach ($enseignants as $e)
                                                                            <option value="{{ $e->id }}">
                                                                                {{ $e->nom }} {{ $e->prenom }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        @foreach ($classes as $c)
                                                                            <option value="{{ $c->id }}">
                                                                                {{ $c->classe }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                @error("grid.$i.$jour")
                                                                    <small class="text-danger d-block">{{ $message }}</small>
                                                                @enderror
                                                                @if ($remplie)
                                                                    <div class="text-right">
                                                                        <a href="#" class="text-muted small"
                                                                            wire:click.prevent="viderCellule({{ $i }}, '{{ $jour }}')"><i
                                                                                class="fa fa-times"></i> vider</a>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-2">
                                        <div>
                                            <button type="button" class="btn btn-outline-primary"
                                                wire:click="ajouterCreneau"><i class="fa fa-plus"></i> Ajouter un
                                                créneau horaire</button>
                                            <button type="button" class="btn btn-outline-secondary"
                                                wire:click="trierCreneaux"><i class="fa fa-sort"></i> Trier par
                                                heure</button>
                                            <label class="ml-3 mb-0">
                                                <input type="checkbox" wire:model.live="afficherDimanche"> Afficher le
                                                dimanche
                                            </label>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary"
                                                wire:click="annuler"
                                                wire:confirm="Annuler les modifications non enregistrées ?">
                                                <i class="fa fa-undo"></i> Annuler</button>
                                            @if (!empty(Permission('Ajouter')) || !empty(Permission('Modifier')))
                                                <button type="button" class="btn btn-primary" wire:click="saveAll"
                                                    wire:loading.attr="disabled" wire:target="saveAll"
                                                    wire:confirm="Enregistrer l'ensemble de l'emploi du temps ? Les cellules vidées et les créneaux supprimés seront retirés.">
                                                    <i class="fa fa-save"></i> Enregistrer l'emploi du temps
                                                    ({{ $nbSeances }})</button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted text-center">Sélectionnez une classe ou un enseignant pour
                                        saisir son emploi du temps.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="ibox ibox-primary">
                            <div class="ibox-body text-center">
                                <h1 class="m-t-20">404</h1>
                                <p class="error-title">PAGE NOT FOUND</p>
                            </div>
                        </div>
                    @endif

                    @include('components.layouts.footer')
                </div>
            </div> {{-- fin .content-wrapper --}}
                @include('components.layouts.loading')
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
