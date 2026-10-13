<div>

    <body class="fixed-navbar ">
        <div class="page-wrapper">
            <!-- START HEADER-->
            @include('components.layouts.header')
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            @include('components.layouts.sidebar')
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <!-- START PAGE CONTENT-->
                <div class="page-content fade-in-up">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="ibox color-danger">
                                <div class="ibox-body text-center">
                                    @livewire('photo-personne', ['type' => 'employer', 'personneId' => $employer->id], key('photo-employer-' . $employer->id))
                                    <h2 class="font-strong m-b-10 m-t-10">{{ $employer->nom }} {{ $employer->prenom }}
                                    </h2>
                                    <div class="m-b-20 text-muted">{{ $employer->poste }}</div>
                                    <a href="{{ url('/fiche/employer/' . encrypt($employer->id)) }}" target="_blank" class="btn btn-warning btn-sm" title="Fiche complète (PDF)"> Fiche complète
                                        <i class="ti ti-printer"></i>
                                    </a>

                                    {{-- <div>
                                        <button class="btn btn-info btn-rounded m-b-5"><i class="fa fa-plus"></i>
                                            Follow</button>
                                        <button class="btn btn-default btn-rounded m-b-5">Message</button>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="ibox">
                                <ul class="list-group list-group-bordered">
                                    <strong>
                                        <h4>
                                            <li class="list-group-item">Code employer
                                                <span class=" float-right">{{ $employer->matricule }}</span>
                                            </li>
                                        </h4>
                                    </strong>
                                    <li class="list-group-item">Date de naissance
                                        <span class=" float-right">{{ $employer->date_naissance }}</span>
                                    </li>
                                    <li class="list-group-item">Lieu de naissance
                                        <span class=" float-right">{{ $employer->lieu_naissance }}</span>
                                    </li>
                                    <li class="list-group-item">Sexe
                                        <span class=" float-right">{{ $employer->sexe }}</span>
                                    </li>
                                    <li class="list-group-item">Poste
                                        <span class=" float-right">{{ $employer->poste }}</span>
                                    </li>
                                    <li class="list-group-item">{{ __('message.Personnes_a_charge') }}
                                        <span class=" float-right">{{ $employer->charge_familliale ?: 0 }}</span>
                                    </li>
                                    {{-- <li class="list-group-item">Salaire de base
                                        <span class=" float-right">{{ $employer->salaire }}</span>
                                    </li> --}}
                                </ul>

                            </div>

                        </div>

                        <div class="col-lg-8 col-md-6">
                            @include('livewire.alert.message')
                            <div class="ibox">
                                <div class="ibox-body">
                                    <ul class="nav nav-tabs tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#tab-1" data-toggle="tab"><i
                                                    class="ti-bar-chart"></i> Paie</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-2" data-toggle="tab"><i
                                                    class="ti-announcement"></i> Historique de paie</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-3" data-toggle="tab"><i
                                                    class="ti-settings"></i> Modifier</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="tab-1">
                                            <div class="row">
                                                <div class="col-xl-12 col-lg-12 col-md-12" style="border-right: 1px solid #eee;">
                                                    {{-- <h5 class="text-info m-b-20 m-t-10"><i class="fa fa-bar-chart"></i>
                                                        Etat de paie</h5> --}}
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="h6 m-0">
                                                                AJOUTER OU RETIRER RUBRIQUE
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 text-right">
                                                            <button class="btn btn-success"
                                                                data-toggle="modal"
                                                                data-target="#AddRubriqueModelBulletin"><i
                                                                    class="fa fa-plus"></i></button>
                                                            <button class="btn btn-danger" data-toggle="modal"
                                                                data-target="#retirerRubriqueModelBulletin"><i
                                                                    class="fa fa-minus"></i></button>

                                                        </div>
                                                    </div>
                                                    {{-- Modal gain --}}
                                                    <div wire:ignore.self class="modal fade"
                                                        id="AddRubriqueModelBulletin" tabindex="-1" role="dialog"
                                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered"
                                                            role="document">
                                                            <form wire:submit='addRubriqueModelBulletin'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Ajouter_rubrique') }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">

                                                                        <div class="row">
                                                                            <div class="col-sm-9 form-group">
                                                                                <label>{{ __('message.Rubrique') }}</label>
                                                                                <select class="form-control"
                                                                                    wire:model.live.debounce.500ms='rubrique_id'>
                                                                                    <option value=""> </option>
                                                                                    @foreach ($rubriques as $rubrique)
                                                                                        <option
                                                                                            value="{{ $rubrique->id }}">
                                                                                            {{ $rubrique->rubrique }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div>
                                                                                    @error('rubrique_id')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            @php
                                                                                $rubAuto = $rubrique_id ? $rubriques->firstWhere('id', $rubrique_id) : null;
                                                                                $rubAuto = $rubAuto && $rubAuto->estAuto() ? $rubAuto : null;
                                                                                $rubF = $rubrique_id ? $rubriques->firstWhere('id', $rubrique_id) : null;
                                                                                $formuleSel = $rubF && $rubF->estFormule() ? $rubF : null;
                                                                            @endphp
                                                                            @if ($rubAuto && !($mode_calcul === 'manuel' && in_array($rubAuto->calcul_auto, ['CNSS', 'ITS', 'FSSP'], true)))
                                                                                <div class="col-sm-3 form-group">
                                                                                    <label>{{ __('message.Montant') }}</label>
                                                                                    <input class="form-control" type="text"
                                                                                        value="{{ number_format((float) $apercu, 0, ',', ' ') }}"
                                                                                        readonly>
                                                                                    <small class="text-muted">{{ __('message.Calcule_automatiquement') }}
                                                                                        ({{ $employer->charge_familliale ?: 0 }} pers. à charge)</small>
                                                                                </div>
                                                                            @elseif ($formuleSel && $mode_calcul !== 'manuel')
                                                                                <div class="col-sm-3 form-group">
                                                                                    <label>{{ __('message.Montant') }}</label>
                                                                                    <input class="form-control" type="text" readonly
                                                                                        value="{{ is_numeric($montant) ? number_format((float) $montant, 0, ',', ' ') : '' }}">
                                                                                    <small class="text-muted">Calculé par la formule</small>
                                                                                </div>
                                                                            @else
                                                                                <div class="col-sm-3 form-group">
                                                                                    <label>{{ __('message.Montant') }}</label>
                                                                                    <input class="form-control" type="text" wire:model='montant'>
                                                                                    <div>
                                                                                        @error('montant')
                                                                                            <span class="text-error">{{ $message }}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        @php
                                                                            $rubSel = $rubrique_id ? $rubriques->firstWhere('id', $rubrique_id) : null;
                                                                            $auto3  = $rubSel && $rubSel->estAuto() && in_array($rubSel->calcul_auto, ['CNSS', 'ITS', 'FSSP'], true);
                                                                            $soumis = $rubSel && !$rubSel->estAuto() && $rubSel->sens_rubrique === 'Gain' && in_array($rubSel->type_rubrique, ['Brute I', 'Brute NI'], true);
                                                                        @endphp
                                                                        @if ($auto3)
                                                                            <div class="row">
                                                                                <div class="col-sm-12 form-group">
                                                                                    <label>Calcul de la rubrique</label><br>
                                                                                    <label class="radio-inline mr-3">
                                                                                        <input type="radio" wire:model.live="mode_calcul" value="auto"> Automatique (selon les barèmes)
                                                                                    </label>
                                                                                    <label class="radio-inline">
                                                                                        <input type="radio" wire:model.live="mode_calcul" value="manuel"> Manuel (je saisis le montant)
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            @if ($mode_calcul === 'manuel')
                                                                                <div class="row">
                                                                                    <div class="col-sm-6 form-group">
                                                                                        <label>Base (facultatif)</label>
                                                                                        <input class="form-control" type="text" wire:model="base_manuelle">
                                                                                        @error('base_manuelle') <span class="text-error">{{ $message }}</span> @enderror
                                                                                    </div>
                                                                                    <div class="col-sm-6 form-group">
                                                                                        <label>Taux % (facultatif)</label>
                                                                                        <input class="form-control" type="text" wire:model="taux_manuel">
                                                                                        @error('taux_manuel') <span class="text-error">{{ $message }}</span> @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <small class="text-muted">Le montant saisi remplace le calcul automatique : il n'est plus recalculé.</small>
                                                                            @endif
                                                                        @endif
@if ($formuleSel)
    @php $varsF = app(\App\Services\FormuleService::class)->variablesUtilisees($formuleSel->formule); @endphp
    <div class="row">
        <div class="col-sm-12 form-group">
            <label>Calcul de la rubrique</label><br>
            <label class="radio-inline mr-3"><input type="radio" wire:model.live="mode_calcul" value="auto"> Automatique (formule : <code>{{ $formuleSel->formule }}</code>)</label>
            <label class="radio-inline"><input type="radio" wire:model.live="mode_calcul" value="manuel"> Manuel (je saisis le montant)</label>
        </div>
    </div>
    <div class="row">
        @php $utilisees = app(\App\Services\FormuleService::class)->constantesUtilisees($formuleSel->formule); @endphp
        @if (!empty($utilisees))
            <div class="col-sm-12 form-group">
                <small class="text-muted">Constantes et paramètres de paie utilisés :
                    @foreach ($utilisees as $code => $val)
                        <code>{{ $code }}</code> = {{ rtrim(rtrim(number_format($val, 4, ',', ' '), '0'), ',') }}@if (!$loop->last) · @endif
                    @endforeach
                </small>
            </div>
        @endif
        @if (in_array('ANCIENNETE', $varsF, true))
            <div class="col-sm-12 form-group">
                <small class="text-muted">Ancienneté prise en compte : <strong>{{ \App\Services\FormuleService::anciennete($employer->date_embauche) }} an(s)</strong> (calculée d'après la date d'embauche)</small>
            </div>
        @endif
        @if (in_array('BASE', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Base</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="base">
                @error('base') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
        @if (in_array('TAUX', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Taux %</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="taux">
                @error('taux') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
        @if (in_array('NOMBRE', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Nombre</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="nombre">
                @error('nombre') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
    </div>
@endif
                                                                        @if ($soumis)
                                                                            <div class="row">
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Dont montant imposable</label>
                                                                                    <input class="form-control" type="text" wire:model.live.debounce.500ms="montant_imposable"
                                                                                        placeholder="{{ $rubSel->type_rubrique === 'Brute I' ? 'Vide = tout imposable' : 'Vide = tout non imposable' }}">
                                                                                    @error('montant_imposable') <span class="text-error">{{ $message }}</span> @enderror
                                                                                </div>
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Dont montant non imposable</label>
                                                                                    @php
                                                                                        $imp = is_numeric($montant_imposable) ? (float) $montant_imposable : ($rubSel->type_rubrique === 'Brute I' ? (float) $montant : 0.0);
                                                                                    @endphp
                                                                                    <input class="form-control" type="text" readonly
                                                                                        value="{{ is_numeric($montant) ? number_format(max(0, (float) $montant - $imp), 0, ',', ' ') : '' }}">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="row">
                                                                            <div class="col-sm-12 form-group">
                                                                                <label>Application de la rubrique</label><br>
                                                                                <label class="radio-inline mr-3">
                                                                                    <input type="radio" wire:model.live="periodicite" value="permanente"> Tout le temps (chaque mois)
                                                                                </label>
                                                                                <label class="radio-inline">
                                                                                    <input type="radio" wire:model.live="periodicite" value="mois"> Ce mois seulement
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @if ($periodicite === 'mois')
                                                                            <div class="row">
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Mois concerné</label>
                                                                                    <select class="form-control" wire:model="mois_application">
                                                                                        @foreach ($mois_liste as $m)
                                                                                            <option value="{{ $m->id }}">{{ $m->mois }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('mois_application') <span class="text-error">{{ $message }}</span> @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        {{-- <div class="row">
                                                                            <div class="col-sm-4 form-group">
                                                                                <label>{{ __('message.Taux') }}</label>
                                                                                <select class="form-control"
                                                                                    wire:model.live.debounce.500ms='rubrique'>
                                                                                    <option value=""> </option>
                                                                                    @foreach ($rubriques as $rubrique)
                                                                                        <option
                                                                                            value="{{ $rubrique->id }}">
                                                                                            {{ $rubrique->rubrique }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div>
                                                                                    @error('rubrique')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4 form-group">
                                                                                <label>{{ __('message.Nombre') }}</label>
                                                                                <select class="form-control"
                                                                                    wire:model.live.debounce.500ms='rubrique'>
                                                                                    <option value=""> </option>
                                                                                    @foreach ($rubriques as $rubrique)
                                                                                        <option
                                                                                            value="{{ $rubrique->id }}">
                                                                                            {{ $rubrique->rubrique }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div>
                                                                                    @error('rubrique')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4 form-group">
                                                                                <label>{{ __('message.Base') }}</label>
                                                                                <select class="form-control"
                                                                                    wire:model.live.debounce.500ms='rubrique'>
                                                                                    <option value=""> </option>
                                                                                    @foreach ($rubriques as $rubrique)
                                                                                        <option
                                                                                            value="{{ $rubrique->id }}">
                                                                                            {{ $rubrique->rubrique }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div>
                                                                                    @error('rubrique')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div> --}}
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    {{-- Modal Retenu --}}
                                                    <div wire:ignore.self class="modal fade"
                                                        id="retirerRubriqueModelBulletin" tabindex="-1" role="dialog"
                                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered"
                                                            role="document">
                                                            <form wire:submit='retirerRubriqueModelBulletin'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Retirer_rubrique') }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">

                                                                        <div class="row">
                                                                            <div class="col-sm-9 form-group">
                                                                                <label>{{ __('message.Rubrique') }}</label>
                                                                                <select class="form-control"
                                                                                    wire:model="ligne_retrait">
                                                                                    <option value=""> </option>
                                                                                    @foreach ($rubrique_model as $ligneModele)
                                                                                        <option value="{{ $ligneModele->id }}">
                                                                                            {{ $ligneModele->rubrique->rubrique }}
                                                                                            @if (!$ligneModele->permanente)
                                                                                                — ce mois seulement ({{ optional($mois_liste->firstWhere('id', $ligneModele->mois_id))->mois }})
                                                                                            @endif
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div>
                                                                                    @error('ligne_retrait')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    {{-- Modal modification d'une rubrique du bulletin modèle --}}
                                                    <div wire:ignore.self class="modal fade" id="editLigneModal" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                            <form wire:submit='updateLigne'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Modifier la rubrique : {{ $ligne_libelle }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @if ($ligne_modifiable)
                                                                            <div class="row">
                                                                                <div class="col-sm-12 form-group">
                                                                                    <label>Calcul de la rubrique</label><br>
                                                                                    <label class="radio-inline mr-3"><input type="radio" wire:model.live="mode_calcul" value="auto"> Automatique (selon les barèmes)</label>
                                                                                    <label class="radio-inline"><input type="radio" wire:model.live="mode_calcul" value="manuel"> Manuel (je saisis le montant)</label>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if (!$ligne_auto || ($ligne_modifiable && $mode_calcul === 'manuel'))
                                                                            <div class="row">
                                                                                <div class="col-sm-4 form-group">
                                                                                    <label>{{ __('message.Montant') }}</label>
                                                                                    <input class="form-control" type="text" wire:model.live.debounce.500ms="montant" @if ($ligne_formule && $mode_calcul !== 'manuel') readonly @endif>
                                                                                    @error('montant') <span class="text-error">{{ $message }}</span> @enderror
                                                                                </div>
                                                                                @if ($ligne_modifiable)
                                                                                    <div class="col-sm-4 form-group">
                                                                                        <label>Base (facultatif)</label>
                                                                                        <input class="form-control" type="text" wire:model="base_manuelle">
                                                                                        @error('base_manuelle') <span class="text-error">{{ $message }}</span> @enderror
                                                                                    </div>
                                                                                    <div class="col-sm-4 form-group">
                                                                                        <label>Taux % (facultatif)</label>
                                                                                        <input class="form-control" type="text" wire:model="taux_manuel">
                                                                                        @error('taux_manuel') <span class="text-error">{{ $message }}</span> @enderror
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @elseif ($ligne_modifiable)
                                                                            <small class="text-muted">Le montant sera recalculé automatiquement d'après les barèmes et les informations de l'employé.</small>
                                                                        @endif
@if ($ligne_formule)
    @php $varsF = app(\App\Services\FormuleService::class)->variablesUtilisees($ligne_formule_texte); @endphp
    <div class="row">
        <div class="col-sm-12 form-group">
            <label>Calcul de la rubrique</label><br>
            <label class="radio-inline mr-3"><input type="radio" wire:model.live="mode_calcul" value="auto"> Automatique (formule : <code>{{ $ligne_formule_texte }}</code>)</label>
            <label class="radio-inline"><input type="radio" wire:model.live="mode_calcul" value="manuel"> Manuel (je saisis le montant)</label>
        </div>
    </div>
    <div class="row">
        @php $utilisees = app(\App\Services\FormuleService::class)->constantesUtilisees($ligne_formule_texte); @endphp
        @if (!empty($utilisees))
            <div class="col-sm-12 form-group">
                <small class="text-muted">Constantes et paramètres de paie utilisés :
                    @foreach ($utilisees as $code => $val)
                        <code>{{ $code }}</code> = {{ rtrim(rtrim(number_format($val, 4, ',', ' '), '0'), ',') }}@if (!$loop->last) · @endif
                    @endforeach
                </small>
            </div>
        @endif
        @if (in_array('ANCIENNETE', $varsF, true))
            <div class="col-sm-12 form-group">
                <small class="text-muted">Ancienneté prise en compte : <strong>{{ \App\Services\FormuleService::anciennete($employer->date_embauche) }} an(s)</strong> (calculée d'après la date d'embauche)</small>
            </div>
        @endif
        @if (in_array('BASE', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Base</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="base">
                @error('base') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
        @if (in_array('TAUX', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Taux %</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="taux">
                @error('taux') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
        @if (in_array('NOMBRE', $varsF, true))
            <div class="col-sm-4 form-group">
                <label>Nombre</label>
                <input class="form-control" type="text" wire:model.live.debounce.500ms="nombre">
                @error('nombre') <span class="text-error">{{ $message }}</span> @enderror
            </div>
        @endif
    </div>
@endif
                                                                        @if ($ligne_soumis)
                                                                            <div class="row">
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Dont montant imposable</label>
                                                                                    <input class="form-control" type="text" wire:model.live.debounce.500ms="montant_imposable" placeholder="Vide = selon le type de la rubrique">
                                                                                    @error('montant_imposable') <span class="text-error">{{ $message }}</span> @enderror
                                                                                </div>
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Dont montant non imposable</label>
                                                                                    <input class="form-control" type="text" readonly
                                                                                        value="{{ is_numeric($montant) && is_numeric($montant_imposable) ? number_format(max(0, (float) $montant - (float) $montant_imposable), 0, ',', ' ') : '' }}">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="row">
                                                                            <div class="col-sm-12 form-group">
                                                                                <label>Application de la rubrique</label><br>
                                                                                <label class="radio-inline mr-3"><input type="radio" wire:model.live="periodicite" value="permanente"> Tout le temps (chaque mois)</label>
                                                                                <label class="radio-inline"><input type="radio" wire:model.live="periodicite" value="mois"> Ce mois seulement</label>
                                                                            </div>
                                                                        </div>
                                                                        @if ($periodicite === 'mois')
                                                                            <div class="row">
                                                                                <div class="col-sm-6 form-group">
                                                                                    <label>Mois concerné</label>
                                                                                    <select class="form-control" wire:model="mois_application">
                                                                                        @foreach ($mois_liste as $m)
                                                                                            <option value="{{ $m->id }}">{{ $m->mois }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('mois_application') <span class="text-error">{{ $message }}</span> @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="submit" class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    @php
                                                        $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');
                                                        $btnEdit = fn ($id) => '<button type="button" class="btn btn-xs btn-info" title="Modifier" data-toggle="modal" data-target="#editLigneModal" wire:click="editLigne('.$id.')"><i class="fa fa-pencil"></i></button>';
                                                    @endphp
                                                    <ul class="list-group list-group-full list-group-divider" style="overflow-x:auto;">
                                                        <table class="table table-hover table-sm" style="width:100%;">
           @php
               $b   = $bulletin_modele;
               $det = $b['details'] ?? [];
               $dec = fn ($v) => $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float) $v, 2, ',', ' '), '0'), ',');
               $lCnss = $model_rubrique_retenu->first(fn ($l) => $l->rubrique->calcul_auto === 'CNSS');
               $lIts  = $model_rubrique_retenu->first(fn ($l) => $l->rubrique->calcul_auto === 'ITS');
               $autresRet = $model_rubrique_retenu->reject(fn ($l) => in_array($l->rubrique->calcul_auto, ['CNSS', 'ITS', 'CNSS_PAT', 'ANPE_PAT'], true));
               $totalSocFisc = (float) ($det['cnss'] ?? 0) + (float) ($det['its'] ?? 0);
               $totalAutres  = (float) $autresRet->sum(fn ($l) => (float) $l->montant_retenu);
               $patronalesAuto = collect($b['patronales'] ?? [])->filter(fn ($l) => in_array($l['auto'] ?? null, ['CNSS_PAT', 'ANPE_PAT'], true));
           @endphp
           <thead>
               <tr>
                   <th>Rubrique</th>
                   <th style="text-align:right">Base</th>
                   <th style="text-align:center">Taux</th>
                   <th style="text-align:center">Nombre</th>
                   <th style="text-align:right">Gain</th>
                   <th style="text-align:right">Retenue</th>
                   <th></th>
               </tr>
           </thead>
           <tbody>
               {{-- Gains --}}
               @foreach ($model_rubrique_gain as $rubrique)
                   @php
                       $soumisLigne = in_array($rubrique->rubrique->type_rubrique, ['Brute I', 'Brute NI'], true);
                       $impLigne = $soumisLigne ? \App\Services\CalculPaiePersonnelService::partImposable($rubrique, (float) $rubrique->montant_gain) : null;
                   @endphp
                   <tr>
                       <td>{{ $rubrique->rubrique->rubrique }}
                           @if ($soumisLigne && ((float) $rubrique->montant_gain - $impLigne) > 0)
                               <br><small class="text-muted">imposable {{ $fmt($impLigne) }} · non imposable {{ $fmt((float) $rubrique->montant_gain - $impLigne) }}</small>
                           @endif
                       </td>
                       <td style="text-align:right">{{ $rubrique->base !== null ? $fmt($rubrique->base) : '' }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->taux) }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->nombre) }}</td>
                       <td class="color-green" style="text-align:right">{{ $fmt($rubrique->montant_gain) }}</td>
                       <td></td>
                       <td style="text-align:right">{!! $btnEdit($rubrique->id) !!}</td>
                   </tr>
               @endforeach
               <tr style="background-color: rgb(32, 208, 55); color:#eee">
                   <td colspan="4">SALAIRE BRUT</td>
                   <td style="text-align:right">{{ $fmt($b['brut'] ?? $somme_gain) }}</td>
                   <td colspan="2"></td>
               </tr>
               <tr>
                   <td colspan="4"><small class="text-muted">dont brut imposable / brut non imposable</small></td>
                   <td colspan="3" style="text-align:left"><small>{{ $fmt($repartition['imposable'] ?? 0) }} / {{ $fmt($repartition['non_imposable'] ?? 0) }}</small></td>
               </tr>

               {{-- Cotisations sociales et fiscales --}}
               <tr style="background-color:#e9ecef; font-weight:bold; text-align:center">
                   <td colspan="7">COTISATIONS SOCIALES ET FISCALES</td>
               </tr>
               @foreach ([$lCnss, $lIts] as $rubrique)
                   @continue(!$rubrique)
                   <tr>
                       <td>
                           {{ $rubrique->rubrique->rubrique }}
                           <span class="badge badge-{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'warning' : 'info' }}">{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'Manuel' : 'Automatique' }}</span>
                           @if ($rubrique->rubrique->calcul_auto === 'ITS')
                               <small class="text-muted">(base retenues ITS)</small>
                           @endif
                       </td>
                       <td style="text-align:right">{{ $rubrique->base !== null ? $fmt($rubrique->base) : '' }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->taux) }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->nombre) }}</td>
                       <td></td>
                       <td class="color-red" style="text-align:right">{{ $fmt($rubrique->montant_retenu) }}</td>
                       <td style="text-align:right">{!! $btnEdit($rubrique->id) !!}</td>
                   </tr>
                   @if ($rubrique->rubrique->calcul_auto === 'CNSS' && ($det['taux_frais_pro'] ?? 0) > 0)
                       <tr>
                           <td><small class="text-muted">Abattement ITS pour frais professionnels ({{ $fmt($det['frais_pro'] ?? 0) }})</small></td>
                           <td></td>
                           <td style="text-align:center"><small>{{ $dec($det['taux_frais_pro']) }}</small></td>
                           <td colspan="4"></td>
                       </tr>
                   @endif
                   @if ($rubrique->rubrique->calcul_auto === 'ITS')
                       <tr>
                           <td><small class="text-muted">Taux abattement ITS pour charges de famille</small></td>
                           <td></td>
                           <td style="text-align:center"><small>{{ $dec($det['taux_abattement'] ?? 0) }}</small></td>
                           <td colspan="4"></td>
                       </tr>
                   @endif
               @endforeach
               <tr style="background-color:#f4f4f4; font-weight:bold">
                   <td colspan="5">TOTAL DES RETENUES SOCIALES ET FISCALES</td>
                   <td style="text-align:right">{{ $fmt($totalSocFisc) }}</td>
                   <td></td>
               </tr>
               <tr style="background-color:#f4f4f4; font-weight:bold">
                   <td colspan="4">SALAIRE NET</td>
                   <td style="text-align:right">{{ $fmt(($b['net'] ?? 0) + $totalAutres) }}</td>
                   <td colspan="2"></td>
               </tr>

               {{-- Retenues sur salaire (FSSP, avances, prêts...) --}}
               <tr style="background-color:#e9ecef; font-weight:bold; text-align:center">
                   <td colspan="7">RETENUES SUR SALAIRE</td>
               </tr>
               @foreach ($autresRet as $rubrique)
                   <tr>
                       <td>
                           {{ $rubrique->rubrique->rubrique }}
                           @if ($rubrique->rubrique->estAuto())
                               <span class="badge badge-{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'warning' : 'info' }}">{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'Manuel' : 'Automatique' }}</span>
                           @endif
                       </td>
                       <td style="text-align:right">{{ $rubrique->base !== null ? $fmt($rubrique->base) : '' }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->taux) }}</td>
                       <td style="text-align:center">{{ $dec($rubrique->nombre) }}</td>
                       <td></td>
                       <td class="color-red" style="text-align:right">{{ $fmt($rubrique->montant_retenu) }}</td>
                       <td style="text-align:right">{!! $btnEdit($rubrique->id) !!}</td>
                   </tr>
               @endforeach
               <tr style="background-color:#f4f4f4; font-weight:bold">
                   <td colspan="5">TOTAL DES RETENUES SUR SALAIRES</td>
                   <td style="text-align:right">{{ $fmt($totalAutres) }}</td>
                   <td></td>
               </tr>

               {{-- Totaux et net à payer --}}
               <tr style="background-color: rgb(176, 47, 47); color:#eee">
                   <td colspan="4">TOTAUX</td>
                   <td style="text-align:right">{{ $fmt($b['total_gain'] ?? $somme_gain) }}</td>
                   <td style="text-align:right">{{ $fmt($b['total_retenu'] ?? $somme_retenu) }}</td>
                   <td></td>
               </tr>
               <tr style="background-color: rgb(107, 105, 105); color:#eee">
                   <td colspan="5"><strong><h4>NET À PAYER</h4></strong></td>
                   <td style="text-align:right"><strong><h4>{{ $fmt($b['net'] ?? ($somme_gain - $somme_retenu)) }}</h4></strong></td>
                   <td></td>
               </tr>

               {{-- Charges patronales (hors net) --}}
               @if ($model_rubrique_patronal->isNotEmpty() || $patronalesAuto->isNotEmpty())
                   <tr style="background-color: rgb(70, 90, 120); color:#eee">
                       <td colspan="7">CHARGES PATRONALES (hors net)</td>
                   </tr>
                   @foreach ($patronalesAuto as $l)
                       <tr style="font-style:italic">
                           <td>{{ $l['libelle'] }}</td>
                           <td style="text-align:right">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                           <td style="text-align:center">{{ $dec($l['taux']) }}</td>
                           <td></td>
                           <td></td>
                           <td style="text-align:right">{{ $fmt($l['montant']) }}</td>
                           <td></td>
                       </tr>
                   @endforeach
                   @foreach ($model_rubrique_patronal as $rubrique)
                       <tr>
                           <td>{{ $rubrique->rubrique->rubrique }}
                               <span class="badge badge-secondary">Patronal</span>
                               @if ($rubrique->rubrique->estFormule())
                                   <span class="badge badge-{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'warning' : 'info' }}">{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'Manuel' : 'Formule' }}</span>
                               @endif
                           </td>
                           <td style="text-align:right">{{ $rubrique->base !== null ? $fmt($rubrique->base) : '' }}</td>
                           <td style="text-align:center">{{ $dec($rubrique->taux) }}</td>
                           <td style="text-align:center">{{ $dec($rubrique->nombre) }}</td>
                           <td></td>
                           <td style="text-align:right">{{ $fmt($rubrique->montant_patronal) }}</td>
                           <td style="text-align:right">{!! $btnEdit($rubrique->id) !!}</td>
                       </tr>
                   @endforeach
               @endif
           </tbody>
       </table>

                                                    </ul>
                                                    @if ($model_rubrique_ponctuelles->isNotEmpty())
                                                        <h6 class="m-t-20">Rubriques « ce mois seulement »</h6>
                                                        <small class="text-muted">Elles ne comptent que pour le mois indiqué ; les totaux ci-dessus ne les incluent pas.</small>
                                                        <table class="table table-hover table-sm" style="width:100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Rubrique</th>
                                                                    <th>Mois</th>
                                                                    <th style="text-align:right">Imposable</th>
                                                                    <th style="text-align:right">Non imposable</th>
                                                                    <th style="text-align:right">Montant</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($model_rubrique_ponctuelles as $rubrique)
                                                                    @php
                                                                        $estGain = (float) $rubrique->montant_gain > 0;
                                                                        $soumisLigne = $estGain && in_array($rubrique->rubrique->type_rubrique, ['Brute I', 'Brute NI'], true);
                                                                        $impLigne = $soumisLigne ? \App\Services\CalculPaiePersonnelService::partImposable($rubrique, (float) $rubrique->montant_gain) : null;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $rubrique->rubrique->rubrique }}
                                                                            @if ($rubrique->rubrique->estAuto())
                                                                                <span class="badge badge-{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'warning' : 'info' }}">{{ ($rubrique->mode_calcul ?? 'auto') === 'manuel' ? 'Manuel' : 'Auto' }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ optional($mois_liste->firstWhere('id', $rubrique->mois_id))->mois }}</td>
                                                                        <td style="text-align:right">{{ $soumisLigne ? $fmt($impLigne) : '—' }}</td>
                                                                        <td style="text-align:right">{{ $soumisLigne ? $fmt((float) $rubrique->montant_gain - $impLigne) : '—' }}</td>
                                                                        <td class="{{ $estGain ? 'color-green' : 'color-red' }}" style="text-align:right">{{ $fmt($estGain ? $rubrique->montant_gain : $rubrique->montant_retenu) }}</td>
                                                                        <td style="text-align:right">{!! $btnEdit($rubrique->id) !!}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif


                                                </div>
                                                {{-- <div class="col-xl-4 col-lg-4 col-md-12">
                                                    <h5 class="text-info m-b-20 m-t-10"><i
                                                            class="fa fa-user-plus"></i>
                                                        État de paie de l'année</h5>
                                                    <ul class="media-list media-list-divider m-0">

                                                        <li class="media">
                                                            <div class="media-body">
                                                                @foreach ($etatsPaies as $etatsPaie)
                                                                    <div class="media-heading">
                                                                        {{ $etatsPaie->mois->mois }}
                                                                        {{ $etatsPaie->annee->annee }}<small
                                                                            class="float-right text-muted">{{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }}</small>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div> --}}
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="tab-2">
                                            <h5 class="text-info m-b-20 m-t-20"><i class="fa fa-bullhorn"></i>
                                                Historique bulletin</h5>
                                            <div>
                                                <ul class="list-group list-group-full list-group-divider">

                                                    <li class="list-group-item"
                                                        style="background-color: rgb(188, 197, 189); color:#000000">
                                                        PAIE DE L'ANNEE :
                                                        <span class="pull-right ">
                                                            {{ number_format($countEtatsPaies, 0, ',', ' ') }}</span>
                                                    </li>

                                                </ul>
                                            </div>
                                            @foreach ($etatsPaies as $etatsPaie)
                                                <ul class="media-list">
                                                    <li class="media">
                                                        <div class="media-body">
                                                            <div class="ibox ">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="ibox-body">
                                                                            <h5>
                                                                                Bulletin de salaire du mois de :
                                                                                <br><br> {{ $etatsPaie->mois->mois }}
                                                                                {{ $etatsPaie->annee->annee }}
                                                                            </h5>
                                                                            <br><br>
                                                                            <div class="col-sm-12 text-center">
                                                                                @if ($etatsPaie->statuts == 'EN COURS')
                                                                                    <div class="h2 m-0"
                                                                                        style=" color:#b51e1e">
                                                                                        {{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }}
                                                                                    </div>
                                                                                    <div>
                                                                                        <small>{{ $etatsPaie->statuts }}</small>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="h2 m-0"
                                                                                        style=" color:#1eb528">
                                                                                        {{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }}
                                                                                    </div>
                                                                                    <div>
                                                                                        <small>{{ $etatsPaie->statuts }}</small>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="ibox-body">
                                                                            <br>
                                                                            <ul
                                                                                class="list-group list-group-full list-group-divider">

                                                                                <li class="list-group-item"
                                                                                    style="background-color: rgb(188, 197, 189); color:#000000">
                                                                                    RUBRIQUE
                                                                                    <span
                                                                                        class="pull-right ">MONTANT</span>
                                                                                </li>

                                                                                @php
                                                                                    $bulletin_calculers = App\Models\PaieRH\BulletinCalculerModel::where(
                                                                                        'employer_id',
                                                                                        $etatsPaie->employer_id,
                                                                                    )
                                                                                        // ->where('annee_id',$etatsPaie->annee_id)
                                                                                        ->where('montant_gain', '!=', 0)
                                                                                        ->where(
                                                                                            'mois_id',
                                                                                            $etatsPaie->mois_id,
                                                                                        )
                                                                                        ->orderBy('id', 'ASC')
                                                                                        ->get();
                                                                                @endphp
                                                                                @foreach ($bulletin_calculers as $bulletin)
                                                                                    <li class="list-group-item">
                                                                                        {{ $bulletin->rubrique->rubrique }}
                                                                                        <span
                                                                                            class="pull-right color-green">{{ number_format($bulletin->montant_gain, 0, ',', ' ') }}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                                <li class="list-group-item "
                                                                                    style="background-color: rgb(128, 232, 140); color:#000000">
                                                                                    Total gain
                                                                                    <span
                                                                                        class="pull-right">{{ number_format($etatsPaie->total_gain, 0, ',', ' ') }}</span>
                                                                                </li>

                                                                                @php
                                                                                    $bulletin_calculers = App\Models\PaieRH\BulletinCalculerModel::where(
                                                                                        'employer_id',
                                                                                        $etatsPaie->employer_id,
                                                                                    )
                                                                                        // ->where('annee_id',$etatsPaie->annee_id)
                                                                                        ->where(
                                                                                            'montant_retenu',
                                                                                            '!=',
                                                                                            0,
                                                                                        )
                                                                                        ->where(
                                                                                            'mois_id',
                                                                                            $etatsPaie->mois_id,
                                                                                        )
                                                                                        ->orderBy('id', 'ASC')
                                                                                        ->get();
                                                                                @endphp
                                                                                @foreach ($bulletin_calculers as $bulletin)
                                                                                    <li class="list-group-item">
                                                                                        {{ $bulletin->rubrique->rubrique }}
                                                                                        <span
                                                                                            class="pull-right color-red">{{ number_format($bulletin->montant_retenu, 0, ',', ' ') }}</span>
                                                                                    </li>
                                                                                @endforeach
                                                                                <li class="list-group-item "
                                                                                    style="background-color: rgb(248, 116, 116); color:#000000">
                                                                                    Total retenue
                                                                                    <span
                                                                                        class="pull-right">{{ number_format($etatsPaie->total_retenu, 0, ',', ' ') }}</span>
                                                                                </li>
                                                                            </ul>
                                                                            <br>
                                                                            <div class="col-sm-12 text-right">
                                                                                <div class="h2 m-0">
                                                                                    {{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }}
                                                                                </div>
                                                                                <div><small>Net a
                                                                                        payé</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="tab-3">
                                            <form wire:submit='editerEmployer'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('message.Modifier_employer') }}</h5>
                                                        {{-- <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            {{-- <div class="col-sm-3 form-group">
                                                    <label>{{ __('message.Matricule')}}</label>
                                                    <input class="form-control" type="text" wire:model='matricule' readonly>
                                                    <div>
                                                        @error('matricule')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Prenom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='prenom'>
                                                                <div>
                                                                    @error('prenom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-8 form-group">
                                                                <label>{{ __('message.Nom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='nom'>
                                                                <div>
                                                                    @error('nom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_naissance') }}</label>
                                                                <input class="form-control" type="date"
                                                                    wire:model='date_naissance'>
                                                                <div>
                                                                    @error('date_naissance')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Lieu_naissance') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='lieu_naissance'>
                                                                <div>
                                                                    @error('lieu_naissance')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Sexe') }} </label>
                                                            <select class="form-control" wire:model="sexe">
                                                                <option value=""></option>
                                                                <option value="Masculin">Masculin</option>
                                                                <option value="Feminin">Feminin</option>
                                                            </select>
                                                            <div>
                                                                @error('sexe')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Adresse') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='adresse'>
                                                                <div>
                                                                    @error('adresse')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Email') }}</label>
                                                    <input class="form-control" type="email" wire:model='email'>
                                                    <div>
                                                        @error('email')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Telephone') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='telephone'>
                                                                <div>
                                                                    @error('telephone')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Personnes_a_charge') }}</label>
                                                                <input class="form-control" type="number" min="0"
                                                                    wire:model='charge_familliale'>
                                                                <small class="text-muted">Sert au calcul de l'ITS (abattement).</small>
                                                                <div>
                                                                    @error('charge_familliale')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Poste') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='poste'>
                                                            <div>
                                                                @error('poste')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            {{-- <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Salaire_base') }}</label>
                                                    <input class="form-control" type="text" wire:model='salaire'>
                                                    <div>
                                                        @error('salaire')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_embauche') }}</label>
                                                                <input class="form-control" type="date"
                                                                    wire:model='date_embauche'>
                                                                <div>
                                                                    @error('date_embauche')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_depart') }}</label>
                                                                <input class="form-control" type="date"
                                                                    wire:model='date_depart'>
                                                                <div>
                                                                    @error('date_depart')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        @if (!empty(Permission('Modifier')))
                                                            <button type="Submit"
                                                                class="btn btn-info">{{ __('message.Modifier') }}
                                                            </button>
                                                        @endif

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .profile-social a {
                            font-size: 16px;
                            margin: 0 10px;
                            color: #999;
                        }

                        .profile-social a:hover {
                            color: #485b6f;
                        }

                        .profile-stat-count {
                            font-size: 22px
                        }
                    </style>

                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->

    </body>

    </html>
</div>
