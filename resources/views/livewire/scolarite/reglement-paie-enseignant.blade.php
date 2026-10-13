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
                @if ($countCaisse > 0)
                    <div class="page-content fade-in-up">
                        <!-- START PAGE CONTENT-->
                        <div class="page-content fade-in-up">
                            <div class="ibox ibox-primary">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Reglement_Salaire_enseignant') }}</div>
                                </div>
                                @include('livewire.alert.message')
                                @if ($caisse->etat == 'ouverte')
                                    @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable)
                                        <form wire:submit='addPaiementSalaire'>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="ibox">
                                                        <div class="ibox-body">
                                                            <div class="row">
                                                                <div class="col-sm-3 form-group">

                                                                    <label>{{ __('message.Matricule') }} <span
                                                                            style="color:red">*</span></label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model.live.debounce.900ms="matricule">
                                                                </div>
                                                                <div class="col-sm-3 form-group">
                                                                    <label>{{ __('message.Nom') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model="nom" readonly>
                                                                </div>
                                                                <div class="col-sm-3 form-group">
                                                                    <label>{{ __('message.Prenom') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model="prenom" readonly>
                                                                </div>
                                                                <div class="col-sm-3 form-group">
                                                                    <label>{{ __('message.Poste') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model="poste" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-sm-3 form-group">
                                                                    <label>{{ __('message.Mois_reglement') }} <span
                                                                            style="color:red">*</span></label>
                                                                    <select class="form-control"
                                                                        wire:model.live.debounce.900ms="mois_paiement">
                                                                        <option value=""> </option>
                                                                        @foreach ($mois_paiements as $mois_paiement)
                                                                            <option value="{{ $mois_paiement->id }}">
                                                                                {{ $mois_paiement->mois }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('mois_paiement')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3 form-group">
                                                                    <label>{{ __('message.Type_reglement') }} <span
                                                                            style="color:red">*</span></label>
                                                                    <select class="form-control"
                                                                        wire:model.live.debounce.900ms="mode_paiement">
                                                                        <option value=""> </option>
                                                                        @foreach ($mode_paiements as $mode_paiement)
                                                                            <option
                                                                                value="{{ $mode_paiement->mode_paiement }}">
                                                                                {{ $mode_paiement->mode_paiement }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('mode_paiement')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                @if ($mode_paie == 'DEPOT' || $mode_paie == 'TRANSFERT' || $mode_paie == 'CHEQUE')
                                                                    <div class="col-sm-3 form-group">
                                                                        <label>{{ __('message.Banque') }}
                                                                            {{ $mode_paie }}<span
                                                                                style="color:red">*</span></label>
                                                                        <select class="form-control"
                                                                            wire:model="banque">
                                                                            <option value=""> </option>
                                                                            @foreach ($banques as $banque)
                                                                                <option value="{{ $banque->id }}">
                                                                                    {{ $banque->banque }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div>
                                                                            @error('banque')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3 form-group">
                                                                        <label>{{ __('message.Numero') }}
                                                                            {{ $mode_paie }}<span
                                                                                style="color:red">*</span></label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model="coordonnee_paiement">
                                                                        <div>
                                                                            @error('coordonnee_paiement')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-4">

                                                    <div class="ibox">
                                                        <div class="ibox-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 form-group">
                                                                    <label>{{ __('message.Statuts') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model="statuts" readonly>
                                                                </div>
                                                                <div class="col-sm-12 form-group">
                                                                    <label>Total brut</label>
                                                                    <input class="form-control" type="text" value="{{ $montant_brut ? number_format($montant_brut, 0, ',', ' ') : '' }}" readonly>
                                                                </div>
                                                                <div class="col-sm-12 form-group">
                                                                    <label>
                                                                        <input type="checkbox" wire:model.live="appliquer_its">
                                                                        Appliquer la retenue ITS ({{ $taux_its }} %) <small>(optionnel)</small>
                                                                    </label>
                                                                    @if ($appliquer_its && $montant_brut)
                                                                        <small class="text-muted d-block">Retenue : {{ number_format(round($montant_brut * $taux_its / 100), 0, ',', ' ') }} F</small>
                                                                    @endif
                                                                </div>
                                                                <div class="col-sm-12 form-group">
                                                                    <label>Autres retenues / Avance <small>(optionnel)</small></label>
                                                                    <input class="form-control" type="number" min="0" step="1" wire:model.live.debounce.400ms="autres_retenues" placeholder="0">
                                                                    <div>
                                                                        @error('autres_retenues')
                                                                            <span class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12 form-group">
                                                                    <label>Net à payer <span
                                                                            style="color:red">*</span></label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model="montant" readonly>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="ibox-body">

                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-info"
                                                                type="submit">{{ __('message.Enregistrer') }}</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    @else
                                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong>
                                            Regularisé la date comptable
                                            .</div>
                                    @endif
                                @else
                                    <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Caisse
                                        Fermer.</div>
                                @endif
                            </div>
                        </div>
                        <!-- END PAGE CONTENT-->
                        @include('components.layouts.footer')
                    </div>
                @else
                    <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez pas de
                        caisse.</div>
                @endif
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
