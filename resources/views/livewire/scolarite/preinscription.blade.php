<div class="page-wrapper">
    {{-- HEADER & SIDEBAR --}}
    @include('components.layouts.header')
    @include('components.layouts.sidebar')

    <div class="content-wrapper">
        <div class="page-content fade-in-up">

            @include('livewire.alert.message')

            <div class="ibox ibox-primary">
                <div class="ibox-head">
                    <div class="ibox-title">{{ __('message.Pre_inscription') }}</div>
                </div>

                <form wire:submit="addPreInscription" autocomplete="off">
                    {{-- Matricule --}}
                    <div class="ibox-body">
                        <div class="form-row">
                            <div class="form-group col-sm-3">
                                <label class="mb-1">{{ __('message.Matricule') }}</label>
                                <input type="text" class="form-control @error('matricule') is-invalid @enderror"
                                    wire:model.defer="matricule" readonly>
                                @error('matricule')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Identité principale --}}
                    <div class="ibox-body pt-0">
                        <div class="form-row">
                            <div class="form-group col-sm-3">
                                <label class="mb-1">{{ __('message.Nom') }}</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                    wire:model.debounce.300ms="nom">
                                @error('nom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-sm-3">
                                <label class="mb-1">{{ __('message.Prenom') }}</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                    wire:model.debounce.300ms="prenom">
                                @error('prenom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-sm-3">
                                <label class="mb-1">{{ __('message.Sexe') }}</label>
                                <select class="form-control @error('sexe') is-invalid @enderror"
                                    wire:model.defer="sexe">
                                    <option value="">{{ __('message.Selectionner') ?? '—' }}</option>
                                    <option value="Masculin">Masculin</option>
                                    <option value="Feminin">Feminin</option>
                                </select>
                                @error('sexe')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-sm-3">
                                <label class="mb-1">{{ __('message.Groupe') }}</label>
                                <select class="form-control @error('groupe') is-invalid @enderror"
                                    wire:model.defer="groupe">
                                    <option value="">{{ __('message.Selectionner') ?? '—' }}</option>
                                    <option value="Homme">Homme</option>
                                    <option value="Femme">Femme</option>
                                    <option value="Enfant">Enfant</option>
                                </select>
                                @error('groupe')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Informations personnelles --}}
                        <div class="col-md-6">
                            <div class="ibox h-100">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Information_personnel') }}</div>
                                </div>
                                <div class="ibox-body">
                                    <div class="form-row">
                                        <div class="form-group col-sm-6">
                                            <label class="mb-1">{{ __('message.Date_naissance') }}</label>
                                            <input type="date"
                                                class="form-control @error('date_naissance') is-invalid @enderror"
                                                wire:model.defer="date_naissance">
                                            @error('date_naissance')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <label class="mb-1">{{ __('message.Lieu_naissance') }}</label>
                                            <input type="text"
                                                class="form-control @error('lieu_naissance') is-invalid @enderror"
                                                wire:model.debounce.300ms="lieu_naissance">
                                            @error('lieu_naissance')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="mb-1">{{ __('message.Telephone') }}</label>
                                        <input type="text"
                                            class="form-control @error('telephone') is-invalid @enderror"
                                            wire:model.debounce.300ms="telephone" placeholder="+227 …"
                                            pattern="^\+?[0-9\s\-]{8,}$"
                                            title="Veuillez entrer un numéro valide (au moins 8 chiffres, avec ou sans +)">
                                        @error('telephone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="mb-1">{{ __('message.Adresse') }}</label>
                                        <input type="text"
                                            class="form-control @error('adresse') is-invalid @enderror"
                                            wire:model.debounce.300ms="adresse">
                                        @error('adresse')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @include('livewire._champ-photo')
                                </div>
                            </div>
                        </div>

                        {{-- Informations tuteur --}}
                        <div class="col-md-6">
                            <div class="ibox h-100">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Information_tuteur') }}</div>
                                    <div class="ibox-tools">
                                        <a class="fullscreen-link"><i class="fa fa-expand"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-body">
                                    <div class="form-group">
                                        <label class="mb-1">{{ __('message.Nom_prenom_tuteur') }}</label>
                                        <input type="text"
                                            class="form-control @error('prenom_parent') is-invalid @enderror"
                                            wire:model.debounce.300ms="prenom_parent">
                                        @error('prenom_parent')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="mb-1">{{ __('message.Telephone') }}</label>
                                        <input type="text"
                                            class="form-control @error('telephone_parent') is-invalid @enderror"
                                            wire:model.debounce.300ms="telephone_parent" placeholder="+227 …"
                                            pattern="^\+?[0-9\s\-]{8,}$"
                                            title="Veuillez entrer un numéro valide (au moins 8 chiffres, avec ou sans +)">
                                        @error('telephone_parent')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="mb-1">{{ __('message.Adresse') }}</label>
                                        <input type="text"
                                            class="form-control @error('adresse_parent') is-invalid @enderror"
                                            wire:model.debounce.300ms="adresse_parent">
                                        @error('adresse_parent')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ibox-body">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-info px-4" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('message.Enregistrer') }}</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm"></span>
                                    {{ __('message.Enregistrer') }}…
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        @include('components.layouts.footer')
    </div>

    @include('components.layouts.loading')
</div>
