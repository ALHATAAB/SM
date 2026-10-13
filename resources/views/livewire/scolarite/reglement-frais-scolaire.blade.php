<div>
    <body class="fixed-navbar">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')

            <div class="content-wrapper">
                @if ($countCaisse > 0)
                    <div class="page-content fade-in-up">
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Reglement_frais_scolaire') }}</div>
                            </div>

                            @include('livewire.alert.message')

                            @if ($caisse->etat == 'ouverte')
                                @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable)
                                    <form wire:submit="addScolarite" class="needs-validation" novalidate>
                                        <div class="row">
                                            {{-- ================== COLONNE GAUCHE ================== --}}
                                            <div class="col-lg-8">
                                                <div class="ibox">
                                                    <div class="ibox-body">

                                                        {{-- SECTION: Identité --}}
                                                        
                                                        <h6 class="text-uppercase text-muted mb-3">
                                                            {{ __('message.Identite') ?? 'Identité' }}
                                                        </h6>
                                                        <div class="form-row">
                                                            <div class="form-group col-sm-4">
                                                                <label class="mb-1">
                                                                    {{ __('message.Matricule') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    wire:model.live.debounce.700ms="matricule"
                                                                    autocomplete="off"
                                                                    required
                                                                >
                                                            </div>

                                                            <div class="form-group col-sm-4">
                                                                <label class="mb-1">{{ __('message.Nom') }}</label>
                                                                <input type="text" class="form-control" wire:model="nom" readonly>
                                                            </div>

                                                            <div class="form-group col-sm-4">
                                                                <label class="mb-1">{{ __('message.Prenom') }}</label>
                                                                <input type="text" class="form-control" wire:model="prenom" readonly>
                                                            </div>
                                                        </div>

                                                        {{-- SECTION: Études --}}
                                                        <h6 class="text-uppercase text-muted mt-3 mb-3">{{ __('message.Etudes') ?? 'Études' }}</h6>
                                                        <div class="form-row">
                                                            <div class="form-group col-sm-6">
                                                                <label class="mb-1">
                                                                    {{ __('message.Niveau_etude') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" wire:model.live.debounce.500ms="niveau" required>
                                                                    <option value="">{{ __('message.Choisir') ?? '—' }}</option>
                                                                    @foreach ($niveaux as $n)
                                                                        <option value="{{ $n->niveau->id }}">{{ $n->niveau->niveau }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-sm-6">
                                                                <label class="mb-1">
                                                                    {{ __('message.Filiere_etude') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" wire:model.live="filiere" required>
                                                                    <option value="">{{ __('message.Choisir') ?? '—' }}</option>
                                                                    @foreach ($filieres as $f)
                                                                        <option value="{{ $f->filiere->id }}">{{ $f->filiere->filiere }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- SECTION: Paiement --}}
                                                        @php $resteAPayer = ($frais_scolarite - $montant_remise) != $payer; @endphp
                                                        @if ($resteAPayer)
                                                            <h6 class="text-uppercase text-muted mt-3 mb-3">{{ __('message.Paiement') }}</h6>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="mb-1">
                                                                        {{ __('message.Montant') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        inputmode="numeric"
                                                                        pattern="^[0-9\s]+$"
                                                                        placeholder="0"
                                                                        wire:model="montant"
                                                                        required
                                                                    >
                                                                    @error('montant') <small class="text-danger">{{ $message }}</small> @enderror
                                                                </div>

                                                                <div class="form-group col-md-3">
                                                                    <label class="mb-1">{{ __('message.Remise') }}</label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        inputmode="numeric"
                                                                        pattern="^[0-9\s]+$"
                                                                        placeholder="0"
                                                                        wire:model="remise"
                                                                    >
                                                                    @error('remise') <small class="text-danger">{{ $message }}</small> @enderror
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="mb-1">
                                                                        {{ __('message.Type_reglement') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select
                                                                        class="form-control"
                                                                        wire:model.live.debounce.500ms="mode_paiement"
                                                                        required
                                                                    >
                                                                        <option value="">{{ __('message.Choisir') ?? '—' }}</option>
                                                                        @foreach ($mode_paiements as $mp)
                                                                            <option value="{{ $mp->mode_paiement }}">{{ $mp->mode_paiement }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('mode_paiement') <small class="text-danger">{{ $message }}</small> @enderror
                                                                </div>

                                                                @if (in_array($mode_paie, ['DEPOT', 'TRANSFERT', 'CHEQUE']))
                                                                    <div class="form-group col-md-3">
                                                                        <label class="mb-1">
                                                                            {{ __('message.Banque') }} {{ $mode_paie }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <select class="form-control" wire:model="banque" required>
                                                                            <option value="">{{ __('message.Choisir') ?? '—' }}</option>
                                                                            @foreach ($banques as $b)
                                                                                <option value="{{ $b->id }}">{{ $b->banque }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('banque') <small class="text-danger">{{ $message }}</small> @enderror
                                                                    </div>

                                                                    <div class="form-group col-md-3">
                                                                        <label class="mb-1">
                                                                            {{ __('message.Numero') }} {{ $mode_paie }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            type="text"
                                                                            class="form-control"
                                                                            wire:model="coordonnee_paiement"
                                                                            required
                                                                        >
                                                                        @error('coordonnee_paiement') <small class="text-danger">{{ $message }}</small> @enderror
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ================== COLONNE DROITE ================== --}}
                                            <div class="col-lg-4">
                                                <div class="ibox">
                                                    <div class="ibox-body">
                                                        <h6 class="text-uppercase text-muted mb-3">{{ __('message.Resume') ?? 'Résumé' }}</h6>

                                                        <div class="form-group mb-3">
                                                            <label class="col-form-label">{{ __('message.Frais_scolaire') }}</label>
                                                            <div class="input-group-icon right">
                                                                <div class="input-icon"><i class="fa fa-info-circle"></i></div>
                                                                <input type="text" class="form-control" wire:model="frais_scolarite" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3 has-success">
                                                            <label class="col-form-label">{{ __('message.Frais_payer') }}</label>
                                                            <div class="input-group-icon right">
                                                                <div class="input-icon"><i class="fa fa-check"></i></div>
                                                                <input type="text" class="form-control" wire:model="payer" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3 has-warning">
                                                            <label class="col-form-label">{{ __('message.Remise') }}</label>
                                                            <div class="input-group-icon right">
                                                                <div class="input-icon"><i class="fa fa-percent"></i></div>
                                                                <input type="text" class="form-control" wire:model="montant_remise" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-0 has-error">
                                                            <label class="col-form-label">{{ __('message.Frais_rester') }}</label>
                                                            <div class="input-group-icon right">
                                                                <div class="input-icon"><i class="fa fa-exclamation"></i></div>
                                                                <input type="text" class="form-control" wire:model="rester" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Bouton d’action --}}
                                        <div class="ibox-footer d-flex justify-content-end">
                                            @if ($resteAPayer)
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary px-4"
                                                    wire:loading.attr="disabled"
                                                    wire:target="addScolarite,matricule,niveau,filiere,mode_paiement"
                                                >
                                                    <span wire:loading.remove>
                                                        <i class="fa fa-save mr-1"></i>{{ __('message.Enregistrer') }}
                                                    </span>
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                        {{ __('message.Enregistrer') }}…
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-danger alert-bordered">
                                        <strong>Attention !</strong> {{ __('message.Date_comptable') }} : {{ __('message.Reglariser_date') ?? 'Régularisez la date comptable.' }}
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger alert-bordered">
                                    <strong>Attention !</strong> {{ __('message.Caisse_Fermer') ?? 'Caisse fermée.' }}
                                </div>
                            @endif
                        </div>

                        @include('components.layouts.footer')
                    </div>
                @else
                    <div class="page-content fade-in-up">
                        <div class="alert alert-danger alert-bordered m-0">
                            <strong>Attention !</strong> {{ __('message.Aucune_caisse') ?? "Vous n'avez pas de caisse." }}
                        </div>
                    </div>
                @endif
            </div>

            @include('components.layouts.loading')
        </div>
    </body>
</div>
