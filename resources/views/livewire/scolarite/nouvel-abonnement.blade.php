<div>

    <body class="fixed-navbar  ">
        <div class="page-wrapper">
            <!-- START HEADER-->
            @include('components.layouts.header')
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            @include('components.layouts.sidebar')
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    <!-- START PAGE CONTENT-->
                    @if ($countCaisse > 0)
                        <div class="page-content fade-in-up">
                            <div class="ibox ibox-primary">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Nouvel_abonnement') }}</div>
                                </div>
                                @include('livewire.alert.message')

                                @if ($caisse->etat == 'ouverte')
                                    @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable)
                                    <div class="ibox-body">
                                        <form wire:submit="addNouvelAbonnement" autocomplete="off">
                                            {{-- ===========================
                                                    IDENTITÉ
                                                ============================ --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Identite') ?? 'Identité' }}
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        {{-- Matricule --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label class="font-weight-semibold">
                                                                {{ __('message.Matricule') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <input type="text"
                                                                class="form-control @error('matricule') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="matricule"
                                                                placeholder="{{ __('message.Matricule') }}">
                                                            @error('matricule')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                          </div>

                                                        {{-- Nom --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Nom') }}</label>
                                                            <input type="text" class="form-control" wire:model="nom"
                                                                readonly>
                                                        </div>

                                                        {{-- Prénom --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Prenom') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="prenom" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        {{-- Sexe --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Sexe') }}</label>
                                                            <input type="text" class="form-control" wire:model="sexe"
                                                                readonly>
                                                        </div>

                                                        {{-- Groupe --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Groupe') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="groupe" readonly>
                                                        </div>

                                                        {{-- Téléphone --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Telephone') }}</label>
                                                            <input type="text" class="form-control"
                                                                wire:model="telephone" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ===========================
                                                    ABONNEMENT
                                                ============================ --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Abonnement') }}</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        {{-- Type d’abonnement --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label class="font-weight-semibold">
                                                                {{ __('message.Type_abonnement') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                class="form-control @error('type_abonnement') is-invalid @enderror"
                                                                wire:model.live="type_abonnement">
                                                                <option value="">
                                                                    {{ __('message.Selectionner') ?? '—' }}</option>
                                                                @foreach ($type_abonnements as $type_abonnement)
                                                                    <option value="{{ $type_abonnement->id }}">
                                                                        {{ $type_abonnement->type_abonnement }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('type_abonnement')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        {{-- Période --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label class="font-weight-semibold">
                                                                {{ __('message.Periode') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                class="form-control @error('frais_periodique') is-invalid @enderror"
                                                                wire:model.live="frais_periodique">
                                                                <option value="">
                                                                    {{ __('message.Selectionner') ?? '—' }}</option>
                                                                @foreach ($frais_periodiques as $p)
                                                                    <option value="{{ $p->id }}">
                                                                        {{ $p->nombre }} {{ $p->periode }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('frais_periodique')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                         </div>

                                                        {{-- Frais (auto) --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Frais_abonnement') }}</label>
                                                            <div class="input-group">
                                                                <input type="text"
                                                                    class="form-control @error('frais_abonnement') is-invalid @enderror"
                                                                    wire:model="frais_abonnement" readonly>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">FCFA</span>
                                                                </div>
                                                                @error('frais_abonnement')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ===========================
                                                    PÉRIODE & REMISE
                                                ============================ --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Periode') }} &
                                                        {{ __('message.Remise') }}</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        {{-- Date début --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label class="font-weight-semibold">
                                                                {{ __('message.Date_debut') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <input type="date"
                                                                class="form-control @error('date_debut') is-invalid @enderror"
                                                                wire:model.live.debounce.700ms="date_debut">
                                                            @error('date_debut')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        {{-- Date fin (auto / manuel) --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Date_fin') }}</label>
                                                            <input type="date"
                                                                class="form-control @error('date_fin') is-invalid @enderror"
                                                                wire:model="date_fin">
                                                            @error('date_fin')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                         </div>

                                                        {{-- Remise --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label>{{ __('message.Remise') }}</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" min="0"
                                                                    class="form-control @error('remise') is-invalid @enderror"
                                                                    wire:model="remise" placeholder="0.00">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">FCFA</span>
                                                                </div>
                                                                @error('remise')
                                                                    <div class="invalid-feedback d-block">
                                                                        {{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ===========================
                                                    RÈGLEMENT
                                                ============================ --}}
                                            <div class="ibox mb-3">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Reglement') }}</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <div class="form-row">
                                                        {{-- Mode de paiement --}}
                                                        <div class="form-group col-lg-4 col-md-6">
                                                            <label class="font-weight-semibold">
                                                                {{ __('message.Type_reglement') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                class="form-control @error('mode_paiement') is-invalid @enderror"
                                                                wire:model="mode_paiement">
                                                                <option value="">
                                                                    {{ __('message.Selectionner') ?? '—' }}</option>
                                                                @foreach ($mode_paiements as $m)
                                                                    <option value="{{ $m->mode_paiement }}">
                                                                        {{ $m->mode_paiement }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('mode_paiement')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        {{-- Champs bancaires conditionnels --}}
                                                        @if (in_array($mode_paie, ['DEPOT', 'TRANSFERT', 'CHEQUE']))
                                                            <div class="form-group col-lg-4 col-md-6">
                                                                <label class="font-weight-semibold">
                                                                    {{ __('message.Banque') }} {{ $mode_paie }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select
                                                                    class="form-control @error('banque') is-invalid @enderror"
                                                                    wire:model="banque">
                                                                    <option value="">
                                                                        {{ __('message.Selectionner') ?? '—' }}
                                                                    </option>
                                                                    @foreach ($banques as $_banque)
                                                                        <option value="{{ $_banque->id }}">
                                                                            {{ $_banque->banque }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('banque')
                                                                    <div class="invalid-feedback">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>

                                                            <div class="form-group col-lg-4 col-md-6">
                                                                <label class="font-weight-semibold">
                                                                    {{ __('message.Reference') }} {{ $mode_paie }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control @error('coordonnee_paiement') is-invalid @enderror"
                                                                    wire:model="coordonnee_paiement"
                                                                    placeholder="{{ __('message.Reference') }}">
                                                                @error('coordonnee_paiement')
                                                                    <div class="invalid-feedback">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ===========================
                                                    ACTIONS
                                                ============================ --}}
                                            <div class="d-flex justify-content-end">
                                                <button type="submit"
                                                    class="btn btn-primary px-4 d-flex align-items-center"
                                                    wire:loading.attr="disabled" wire:target="addNouvelAbonnement"
                                                    style="background-color:#0033A0;border-color:#0033A0;">
                                                    <span wire:loading.remove wire:target="addNouvelAbonnement">
                                                        <i class="fa fa-save mr-2"></i>{{ __('message.Enregistrer') }}
                                                    </span>
                                                    <span wire:loading wire:target="addNouvelAbonnement">
                                                        <span class="spinner-border spinner-border-sm mr-2"></span>
                                                        {{ __('message.Enregistrer') }}…
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @else
                                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong>
                                            Regularisé la date comptable
                                            .</div>
                                    @endif
                                @else
                                    <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong>
                                        Caisse
                                        Fermer.</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez pas
                            de
                            caisse.</div>
                    @endif
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
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
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            
            <!-- END PAGA BACKDROPS-->
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
