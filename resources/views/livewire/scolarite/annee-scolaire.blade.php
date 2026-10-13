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
                    
                    @if (!empty(Permission('Afficher')))
                        <div class="ibox ibox-primary">
                            <div class="ibox-head ibox ibox-danger">
                                <div class="ibox-title">{{ __('message.Liste_annee_scolaire') }} </div>
                                <div class="ibox-tools">
                                    <!-- Button trigger modal -->
                                    @if (!empty(Permission('Ajouter')))
                                        <button type="button" class="btn btn-ligth" data-toggle="modal"
                                            data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <!-- Modal -->
                            <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addAnnee'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter_annee_scolaire') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Annee') }}</label>
                                                    <input class="form-control" type="number" wire:model='annee'>
                                                    <div>
                                                        @error('annee')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Date_debut') }}</label>
                                                        <input class="form-control" type="date"
                                                            wire:model='date_debut'>
                                                        <div>
                                                            @error('date_debut')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Date_fin') }}</label>
                                                        <input class="form-control" type="date"
                                                            wire:model='date_fin'>
                                                        <div>
                                                            @error('date_fin')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
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
                            <div class="ibox-body">
                                <form class="form-inline">
                                    {{-- <label class="sr-only" for="ex-email">{{ __('message.Matricule') }}</label>
                            <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                placeholder=" {{ __('message.Matricule') }}"> --}}
                                    <label class="sr-only" for="ex-email">{{ __('message.Annee_scolaire') }}</label>
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                        wire:model.live='recherche'
                                        placeholder="{{ __('message.Recherche') }} {{ __('message.Annee_scolaire') }} ">

                                </form>
                                <div class="table-responsive">
                                    @include('livewire.alert.message')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                {{-- <th width="50px"></th> --}}
                                                <th>{{ __('message.Annee_scolaire') }}</th>
                                                <th>{{ __('message.Date_debut') }}</th>
                                                <th>{{ __('message.Date_fin') }}</th>
                                                <th>{{ __('message.Etat') }}</th>
                                                @if (!empty(Permission('Valider')) || !empty(Permission('Modifier')))
                                                    <th>{{ __('message.Actions') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($annee_scolaires as $annee_scolaire)
                                                <tr>
                                                    <td>{{ $annee_scolaire->annee }}</td>
                                                    <td>{{ $annee_scolaire->date_debut }}</td>
                                                    <td>{{ $annee_scolaire->date_fin }}</td>
                                                    @if ($annee_scolaire->etat == 'inactif')
                                                        <td><span
                                                                class="badge badge-danger">{{ $annee_scolaire->etat }}</span>
                                                        </td>
                                                    @else
                                                        <td><span
                                                                class="badge badge-success">{{ $annee_scolaire->etat }}</span>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        @if (!empty(Permission('Valider')))
                                                            @if ($annee_scolaire->etat == 'inactif')
                                                                <button type="button" data-toggle="modal"
                                                                    data-target=".activerCpt"
                                                                    wire:click.prevent="activer({{ $annee_scolaire->id }})"
                                                                    class="btn btn-sm  btn-success"><i
                                                                        class="feather fa fa-check-circle"></i></button>
                                                            @else
                                                                <button type="button" data-toggle="modal"
                                                                    data-target=".desactiverCpt"
                                                                    wire:click.prevent="desactiver({{ $annee_scolaire->id }})"
                                                                    class="btn btn-sm  btn-danger"><i
                                                                        class="feather fa fa-times-circle-o"></i></button>
                                                            @endif
                                                        @endif
                                                        @if (!empty(Permission('Modifier')))
                                                            <button type="button"
                                                                wire:click.prevent="editAnnee({{ $annee_scolaire->id }})"
                                                                class="btn btn-sm  btn-info" data-toggle="modal"
                                                                data-target="#editAnnee"
                                                                data-whatever="@getbootstrap"><i
                                                                    class="feather fa fa-edit"></i></button>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <!-- Liens de pagination -->
                                    <div>
                                        {{ $annee_scolaires->links() }}
                                    </div>
                                    {{-- Modal activer compte --}}
                                    <div wire:ignore.self class="modal fade activerCpt" id="#" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-success">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmActive'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-success alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous activer ce compte ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-success">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>


                                    {{-- Modal desactiver compte --}}
                                    <div wire:ignore.self class="modal fade desactiverCpt" id="#"
                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-danger">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmDesactive'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous desactiver ce compte ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-danger">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    {{-- Modal editAnnee --}}
                                    <div wire:ignore.self class="modal fade" id="editAnnee" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateAnnee'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('message.Modifier') }}
                                                            {{ __('message.Annee_scolaire') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>{{ __('message.Annee') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='annee'>
                                                            <div>
                                                                @error('annee')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_debut') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='date_debut'>
                                                                <div>
                                                                    @error('datebut_de')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_fin') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='date_fin'>
                                                                <div>
                                                                    @error('date_fin')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-primary">{{ __('message.Modifier') }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Modal initialisation mdp compte --}}
                                    <div wire:ignore.self class="modal fade initialiserMdp" id="#"
                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-warning">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmMdp'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous initialiser le mots de passe ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-warning">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>

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
                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
        @include('components.layouts.loading')
        <!-- END PAGA BACKDROPS-->
    </body>
</div>
