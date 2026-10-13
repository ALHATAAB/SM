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

                <div class="page-content fade-in-up">
                    @if (!empty(Permission('Afficher')))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ibox ibox-info">
                                    <div class="ibox-head ">
                                        <div class="ibox-title">{{ __('message.Parametre_societe') }} </div>
                                        <div class="ibox-tools">
                                            <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                            <a class="dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item">option 1</a>
                                                <a class="dropdown-item">option 2</a>
                                            </div>
                                        </div>
                                    </div>
                                    @include('livewire.alert.message')
                                    <form wire:submit="save">
                                        <div class="ibox-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card m-t-20 m-b-20">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Raison_sociale') }}</label>
                                                                <input class="form-control" type="text"
                                                                    placeholder="Raison sociale"
                                                                    wire:model='raison_sociale'>
                                                                <div>
                                                                    @error('raison_sociale')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="card m-t-20 m-b-20 text-center">
                                                                @if ($logo)
                                                                    <img src="{{ $logo->temporaryUrl() }}"
                                                                        width="100px" class="rounded">
                                                                @elseif ($logo_actuel)
                                                                    <img src="{{ \App\Services\SocieteService::info()['logo_url'] }}"
                                                                        width="100px" class="rounded">
                                                                @else
                                                                    <img src="{{ url('') }}/assets/img/admin-avatar.png"
                                                                        width="100px" />
                                                                @endif
                                                                <input type="file" wire:model="logo"
                                                                    class="form-control form-control-sm mt-2"
                                                                    accept="image/*">
                                                                <div wire:loading wire:target="logo"
                                                                    class="small text-muted">Chargement...</div>
                                                                @error('logo')
                                                                    <span class="text-error d-block">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card m-t-20 m-b-20">
                                                        <div class="card-header ibox ibox-grey">
                                                            <h4 class="text-center card-title">
                                                                {{ __('message.Information_generale') }}</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Activite') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="Activité" wire:model='activite'>
                                                                    <div>
                                                                        @error('activite')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Telephone') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="Telephone" wire:model='telephone'>
                                                                    <div>
                                                                        @error('telephone')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-8 form-group">
                                                                    <label>{{ __('message.Adresse') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="Adresse" wire:model='adresse'>
                                                                    @error('adresse')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-sm-4 form-group">
                                                                    <label>{{ __('message.Code_postal') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="Code postal" wire:model='code_postal'>
                                                                    @error('code_postal')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('message.Complement_adresse') }}</label>
                                                                <input class="form-control" type="text"
                                                                    placeholder="Complément d'adresse" wire:model='complement'>
                                                                @error('complement')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('message.Forme_juridique') }}</label>
                                                                <input class="form-control" type="text"
                                                                    placeholder="Forme juridique" wire:model='forme_juridique'>
                                                                @error('forme_juridique')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card m-t-20 m-b-20">
                                                        <div class="ibox ibox-danger card-header ">
                                                            <h4 class="text-center card-title">
                                                                {{ __('message.Information_complementaire') }}</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-12 form-group">
                                                                    <label>{{ __('message.Email') }}</label>
                                                                    <input class="form-control" type="email"
                                                                        placeholder="Email " wire:model='email'>
                                                                    <div>
                                                                        @error('email')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.NIF') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="NIF" wire:model='nif'>
                                                                    <div>
                                                                        @error('nif')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.RCCM') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="RCCM" wire:model='rccm'>
                                                                    <div>
                                                                        @error('rccm')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12 form-group">
                                                                    <label>{{ __('message.Site_web') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        placeholder="https://..." wire:model='site_web'>
                                                                    @error('site_web')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary" type="submit"
                                                        wire:loading.attr="disabled" wire:target="save,logo">
                                                        <span wire:loading.remove
                                                            wire:target="save">{{ __('message.Enregistrer') }}</span>
                                                        <span wire:loading
                                                            wire:target="save">{{ __('message.Enregistrement_en_cours') }}...</span>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>

                                </div>
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
