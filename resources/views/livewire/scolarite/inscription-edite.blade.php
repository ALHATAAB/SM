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
                    {{-- @if ($countCaisse > 0) --}}
                        <div class="page-content fade-in-up">
                            <div class="ibox ibox-primary">
                                <div class="ibox-head">
                                    <div class="ibox-title">{{ __('message.Inscription') }}</div>
                                </div>
                                @include('livewire.alert.message')
                                {{-- @if ($caisse->etat == 'ouverte') --}}
                                {{-- @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable) --}}
                                    <form wire:submit='EditInscription'>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="ibox">
                                                    <div class="ibox-body">
                                                        <div class="row">
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Matricule') }} <span
                                                                        style="color:red">*</span></label>
                                                                <input class="form-control" type="text"
                                                                    wire:model.live.debounce.900ms="matricule" readonly>
                                                                    <div>
                                                                        @error('matricule')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Nom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="nom" readonly>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Prenom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="prenom" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Sexe') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="sexe" readonly>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Groupe') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="groupe" readonly>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Telephone') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="telephone" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Niveau_etude') }} <span
                                                                        style="color:red">*</span></label>
                                                                <select class="form-control" wire:model.live='niveau'>
                                                                    <option value=""> </option>
                                                                    @foreach ($niveaux as $niveau)
                                                                        <option value="{{ $niveau->id }}">
                                                                            {{ $niveau->niveau }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div>
                                                                    @error('niveau')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Filiere_etude') }} <span
                                                                        style="color:red">*</span></label>
                                                                <select class="form-control"
                                                                    wire:model.live.change='filiere'>
                                                                    <option value=""> </option>
                                                                    @foreach ($filieres as $filiere)
                                                                        <option
                                                                            value="{{ $filiere->filiere_etude_id }}">
                                                                            {{ $filiere->filiereEtude->filiere }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div>
                                                                    @error('filiere')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Frais_inscription') }} <span
                                                                        style="color:red">*</span></label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="frais_inscription">
                                                            </div>
                                                            <div>
                                                                @error('frais_inscription')
                                                                <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.frais_scolarite') }} </label>
                                                                <input class="form-control" type="text"
                                                                    wire:model="frais_scolarite" >
                                                            </div>
                                                            <div>
                                                                @error('frais_scolarite')
                                                                <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <div class="col-md-6">
                                                        <button class="btn btn-info"
                                                            type="submit">{{ __('message.Enregistrer') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </form>
                                    {{-- @else --}}
                                    {{-- <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Regularisé la date comptable
                                        .</div> --}}
                                {{-- @endif
                                @else
                                    <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Caisse
                                        Fermer.</div>
                                @endif --}}
                            </div>
                        </div>
                    {{-- @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez pas de
                            caisse.</div>
                    @endif --}}
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
