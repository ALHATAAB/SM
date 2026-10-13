<div>

    <body class="fixed-navbar  ">
        <div class="page-wrapper">
            {{-- HEADER & SIDEBAR --}}
            @include('components.layouts.header')
            @include('components.layouts.sidebar')

            <div class="content-wrapper">
                <div class="page-content fade-in-up">

                    {{-- Messages globaux --}}
                    @include('livewire.alert.message')

                    @if ($countCaisse > 0)
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Inscription') }}</div>
                            </div>

                            @if ($caisse->etat == 'ouverte')
                                @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable)
                                    <div class="ibox-body">
                                        <form wire:submit="addInscription" autocomplete="off">
                                            {{-- Identité rapide --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Information_personnel') }}
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Matricule') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('matricule') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="matricule"
                                                                placeholder="{{ __('message.Matricule') }}">
                                                            @error('matricule')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Nom') }}</label>
                                                            <input type="text" class="form-control" wire:model="nom"
                                                                readonly>
                                                        </div>

                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Prenom') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="prenom" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Sexe') }}</label>
                                                            <input type="text" class="form-control" wire:model="sexe"
                                                                readonly>
                                                        </div>

                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Groupe') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="groupe" readonly>
                                                        </div>

                                                        <div class="form-group col-sm-4">
                                                            <label class="mb-1">{{ __('message.Telephone') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="telephone" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Scolarité / Tarifs --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Scolarite') }}</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-sm-3">
                                                            <label class="mb-1">{{ __('message.Niveau_etude') }}
                                                                <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-control @error('niveau') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="niveau">
                                                                <option value="">—</option>
                                                                @foreach ($niveaux as $niveau)
                                                                    <option value="{{ $niveau->id }}">
                                                                        {{ $niveau->niveau }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('niveau')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-sm-3">
                                                            <label class="mb-1">{{ __('message.Filiere_etude') }}
                                                                <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-control @error('filiere') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="filiere">
                                                                <option value="">—</option>
                                                                @foreach ($filieres as $filiere)
                                                                    <option value="{{ $filiere->filiere_etude_id }}">
                                                                        {{ $filiere->filiereEtude->filiere }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('filiere')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-sm-3">
                                                            <label class="mb-1">{{ __('message.Frais_inscription') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('frais_inscription') is-invalid @enderror"
                                                                wire:model="frais_inscription" placeholder="0">
                                                            @error('frais_inscription')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group col-sm-3">
                                                            <label
                                                                class="mb-1">{{ __('message.Frais_scolaire') }}</label>
                                                            <input type="text"
                                                                class="form-control @error('frais_scolaire') is-invalid @enderror"
                                                                wire:model="frais_scolaire" readonly>
                                                            @error('frais_scolaire')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group col-sm-3">
                                                            <label class="mb-1">{{ __('message.Classe_affecter') }}
                                                                <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-control @error('classe') is-invalid @enderror"
                                                                wire:model="classe">
                                                                <option value="">—</option>
                                                                @foreach ($classes as $classe)
                                                                    <option value="{{ $classe->id }}">
                                                                        {{ $classe->classe }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('classe')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Paiement --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Reglement') }}</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-sm-3">
                                                            <label class="mb-1">{{ __('message.Type_reglement') }}
                                                                <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-control @error('mode_paiement') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="mode_paiement">
                                                                <option value="">—</option>
                                                                @foreach ($mode_paiements as $mode_paiement)
                                                                    <option
                                                                        value="{{ $mode_paiement->mode_paiement }}">
                                                                        {{ $mode_paiement->mode_paiement }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('mode_paiement')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        @if ($mode_paie == 'DEPOT' || $mode_paie == 'TRANSFERT' || $mode_paie == 'CHEQUE')
                                                            <div class="form-group col-sm-3">
                                                                <label class="mb-1">{{ __('message.Banque') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <select
                                                                    class="form-control @error('banque') is-invalid @enderror"
                                                                    wire:model="banque">
                                                                    <option value="">—</option>
                                                                    @foreach ($banques as $banque)
                                                                        <option value="{{ $banque->id }}">
                                                                            {{ $banque->banque }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('banque')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="form-group col-sm-3">
                                                                <label class="mb-1">{{ __('message.Reference') }}
                                                                    {{ $mode_paie }} <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control @error('coordonnee_paiement') is-invalid @enderror"
                                                                    wire:model="coordonnee_paiement"
                                                                    placeholder="REF / N° chèque / N° transaction">
                                                                @error('coordonnee_paiement')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-info px-4" type="submit"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove>
                                                        <i class="fa fa-save mr-1"></i>{{ __('message.Enregistrer') }}
                                                    </span>
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                        {{ __('message.Enregistrer') }}…
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="alert alert-danger alert-bordered m-3">
                                        <strong>Attention !</strong> Regularisé la date comptable.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger alert-bordered m-3">
                                    <strong>Attention !</strong> Caisse Fermée.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-danger alert-bordered m-3">
                            <strong>Attention !</strong> Vous n'avez pas de caisse.
                        </div>
                    @endif

                    @include('components.layouts.footer')
                </div>
            </div>

            @include('components.layouts.loading')
        </div>

        {{-- ==================== Styles utilitaires (déplacer ensuite dans votre CSS global) ==================== --}}
        <style>
            /* :root {
                --bic: #1E88E5;
            } */

            .ibox {
                border: 1px solid #e8e8e8;
                border-radius: 8px;
                background: #fff;
            }

            .ibox+.ibox {
                margin-top: 16px;
            }

            .ibox-head {
                padding: 0;
            }

            .ibox-title {
                font-weight: 700;
                font-size: 11px;
                color: #fff;
                background: var(--bic);
                padding: 10px 14px;
                border-radius: 8px 8px 0 0;
            }

            .ibox-body {
                padding: 18px;
            }

            .page-content {
                background: #f5f6fa;
                padding: 18px;
            }

            label {
                font-weight: 600;
            }

            .text-error,
            .invalid-feedback {
                font-size: 0.85rem;
            }

            .btn-info {
                background-color: var(--bic);
                border-color: var(--bic);
            }

            .btn-info:hover {
                background-color: #0d47a1;
                border-color: #0d47a1;
            }

            @media (max-width: 991.98px) {
                .form-row>[class*="col-"] {
                    margin-bottom: 12px;
                }
            }
        </style>

    </body>
</div>
