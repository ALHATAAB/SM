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
                    <div class="page-content fade-in-up">
                        <div class="ibox ibox-ligth">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Frais_abonnement') }}</div>
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
                                    <form wire:submit='AddFraisAbonnement'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter') }} {{ __('message.Frais_abonnement') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                            <div class="form-group">
                                                                <label>{{ __('message.Type_abonnement') }}</label>
                                                                <select class="form-control"
                                                                    wire:model='type_abonnement'>
                                                                    <option value=""> </option>
                                                                    @foreach ($type_abonnements as $type_abonnement)
                                                                        <option value="{{ $type_abonnement->id }}">
                                                                            {{ $type_abonnement->type_abonnement }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div>
                                                                    @error('type_abonnement')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Periode') }}</label>
                                                                    <select class="form-control" wire:model='periode'>
                                                                        <option value=""> </option>
                                                                        @foreach ($periodes as $periode)
                                                                            <option value="{{ $periode }}">
                                                                                {{ $periode }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('periode')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Nombre') }}</label>
                                                                    <select class="form-control" wire:model='nombre'>
                                                                        <option value=""> </option>
                                                                        @foreach ($nombres as $nombre)
                                                                            <option value="{{ $nombre }}">
                                                                                {{ $nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('nombres')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">

                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Frais_abonnement') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model='frais'>
                                                                    <div>
                                                                        @error('frais')
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
                                                    class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @include('livewire.alert.message')
                            <div class="row">

                                @foreach ($type_abonnements as $type_abonnement)
                                    <div class="col-md-4">
                                        <ul class="list-group list-group-bordered">
                                            <li class="list-group-item active"> {{ $type_abonnement->type_abonnement }}
                                            </li>
                                        </ul>
                                        @foreach ($fraisAbonnements as $frais)
                                            @if ($type_abonnement->id == $frais->type_abonnement_id)
                                                <ul class="list-group list-group-bordered">
                                                    @if (!empty(Permission('Modifier')))
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            wire:click.prevent="editFraisAbonnement({{ $frais->id }})"
                                                            data-toggle="modal" data-target="#editFraisAbonnement"
                                                            data-whatever="@getbootstrap">
                                                            <li class="list-group-item">
                                                                {{ $frais->nombre }} {{ $frais->periode }}
                                                                <span
                                                                    class="badge badge-success float-right">{{ $frais->frais_abonnement }}</span>
                                                            </li>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <li class="list-group-item">
                                                                {{ $frais->nombre }} {{ $frais->periode }}
                                                                <span
                                                                    class="badge badge-success float-right">{{ $frais->frais_abonnement }}</span>
                                                            </li>
                                                        </button>
                                                    @endif
                                                </ul>

                                                {{-- <ul
                                                                        class="list-group list-group-full list-group-divider">
                                                                        <li class="list-group-item">{{ $filiere->filiereEtude->filiere }}
                                                                            <span
                                                                                class="badge badge-warning badge-square float-right">{{ $filiere->frais_scolaire }}</span>
                                                                        </li>
                                                                    </ul> --}}
                                            @endif
                                        @endforeach
                                        {{-- Modal editfiliere_etude --}}
                                        <div wire:ignore.self class="modal fade" id="editFraisAbonnement" tabindex="-1"
                                            niveau_etude="dialog" aria-labelledby="exampleModalCenterTitle"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" niveau_etude="document">
                                                <form wire:submit.prevent='updateFraisAbonnement'>
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                                {{ __('message.Modifier') }}
                                                                {{ __('message.Frais_abonnement') }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group">
                                                                <label>{{ __('message.Type_abonnement') }}</label>
                                                                <select class="form-control"
                                                                    wire:model='type_abonnement'>
                                                                    <option value=""> </option>
                                                                    @foreach ($type_abonnements as $type_abonnement)
                                                                        <option value="{{ $type_abonnement->id }}">
                                                                            {{ $type_abonnement->type_abonnement }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div>
                                                                    @error('type_abonnement')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Periode') }}</label>
                                                                    <select class="form-control" wire:model='periode'>
                                                                        <option value=""> </option>
                                                                        @foreach ($periodes as $periode)
                                                                            <option value="{{ $periode }}">
                                                                                {{ $periode }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('periode')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Nombre') }}</label>
                                                                    <select class="form-control" wire:model='nombre'>
                                                                        <option value=""> </option>
                                                                        @foreach ($nombres as $nombre)
                                                                            <option value="{{ $nombre }}">
                                                                                {{ $nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('nombres')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">

                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Frais_abonnement') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model='frais'>
                                                                    <div>
                                                                        @error('frais')
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
                                        <br>
                                    </div>
                                @endforeach
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
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
