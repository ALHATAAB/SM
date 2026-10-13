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

                <!-- START PAGE CONTENT-->
                <div class="page-content fade-in-up">

                    <div class="ibox ibox-primary">

                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_livre') }}</div>
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
                        <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addLivre'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Ajouter_livre') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <div class="form-group">
                                                <label>{{ __('message.Code') }} {{ __('message.livre') }}</label>
                                                <input class="form-control" type="text" wire:model='matricule'
                                                    readonly>
                                                <div>
                                                    @error('matricule')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group">
                                                <label>{{ __('message.Titre') }}</label>
                                                <input class="form-control" type="text" wire:model='titre'>
                                                <div>
                                                    @error('titre')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Auteur') }}</label>
                                                <input class="form-control" type="text" wire:model='auteur'>
                                                <div>
                                                    @error('auteur')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_achat') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_achat'>
                                                    <div>
                                                        @error('prix_achat')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_vente') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_vente'>
                                                    <div>
                                                        @error('prix_vente')
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
                                <label class="sr-only" for="ex-email">{{ __('message.Recherche') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                    wire:model.live='recherche' placeholder="{{ __('message.Recherche') }} ">

                            </form>
                            @include('livewire.alert.message')
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.Titre') }}</th>
                                            <th>{{ __('message.Auteur') }}</th>
                                            <th>{{ __('message.Prix_achat') }}</th>
                                            <th>{{ __('message.Prix_vente') }}</th>
                                            @if (!empty(Permission('Consulter')))
                                                <th>{{ __('message.Actions') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($livres as $livre)
                                            <tr>
                                                <td>{{ $livre->titre }}</td>
                                                <td>{{ $livre->auteur }}</td>
                                                <td>{{ $livre->prix_achat }}</td>
                                                <td>{{ $livre->prix_vente }}</td>

                                                <td>
                                                    @if (!empty(Permission('Consulter')))
                                                        <button type="button"
                                                            wire:click.prevent="detailLivre({{ $livre->id }})"
                                                            class="btn btn-sm btn-ligth " data-toggle="modal"
                                                            data-target="#detailLivre"
                                                            data-whatever="@getbootstrap"><i
                                                                class="feather fa fa-eye"></i></button>
                                                    @endif
                                                    @if (!empty(Permission('Modifier')))
                                                        <button type="button"
                                                            wire:click.prevent="editLivre({{ $livre->id }})"
                                                            class="btn btn-sm  btn-info" data-toggle="modal"
                                                            data-target="#editLivre"
                                                            data-whatever="@getbootstrap"><i
                                                                class="feather fa fa-edit"></i></button>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div>
                                    {{ $livres->links() }}
                                </div>
                                {{-- Modal detail livre --}}
                                <div wire:ignore.self class="modal fade" id="detailLivre" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <form wire:submit.prevent='updateSalleEtude'>
                                           <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Detail_livre') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <div class="form-group">
                                                <label>{{ __('message.Code') }} {{ __('message.livre') }}</label>
                                                <input class="form-control" type="text" wire:model='matricule'
                                                    readonly>
                                                <div>
                                                    @error('matricule')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group">
                                                <label>{{ __('message.Titre') }}</label>
                                                <input class="form-control" type="text" wire:model='titre'>
                                                <div>
                                                    @error('titre')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Auteur') }}</label>
                                                <input class="form-control" type="text" wire:model='auteur'>
                                                <div>
                                                    @error('auteur')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_achat') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_achat'>
                                                    <div>
                                                        @error('prix_achat')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_vente') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_vente'>
                                                    <div>
                                                        @error('prix_vente')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Quantite_initial') }}</label>
                                                    <input class="form-control" type="text" wire:model='quantite_initial'>
                                                    <div>
                                                        @error('quantite_initial')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Quantite_actuel') }}</label>
                                                    <input class="form-control" type="text" wire:model='quantite_actuel'>
                                                    <div>
                                                        @error('quantite_actuel')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                        </div>
                                    </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- Modal edite livre --}}
                                <div wire:ignore.self class="modal fade" id="editLivre" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <form wire:submit.prevent='updateLivre'>
                                            <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Modifier_livre') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <div class="form-group">
                                                <label>{{ __('message.Code') }} {{ __('message.livre') }}</label>
                                                <input class="form-control" type="text" wire:model='matricule'
                                                    readonly>
                                                <div>
                                                    @error('matricule')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group">
                                                <label>{{ __('message.Titre') }}</label>
                                                <input class="form-control" type="text" wire:model='titre'>
                                                <div>
                                                    @error('titre')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Auteur') }}</label>
                                                <input class="form-control" type="text" wire:model='auteur'>
                                                <div>
                                                    @error('auteur')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_achat') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_achat'>
                                                    <div>
                                                        @error('prix_achat')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Prix_vente') }}</label>
                                                    <input class="form-control" type="text" wire:model='prix_vente'>
                                                    <div>
                                                        @error('prix_vente')
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
                                                class="btn btn-primary">{{ __('message.Modifier') }}</button>
                                        </div>
                                    </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


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
