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
                                <div class="ibox-title">{{ __('message.Frais_etude') }}</div>
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
                                    <form wire:submit='AddFraisEtude'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter') }} {{ __('message.Frais_etude') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <label>{{ __('message.Niveau_etude') }}</label>
                                                    <select class="form-control" wire:model='niveau'>
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
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Filiere_etude') }}</label>
                                                        <select class="form-control" wire:model='filiere'>
                                                            <option value=""> </option>
                                                            @foreach ($filieres as $filiere)
                                                                <option value="{{ $filiere->id }}">
                                                                    {{ $filiere->filiere }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('filiere')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Frais_inscription') }}</label>
                                                        <input class="form-control" type="text"
                                                            wire:model='inscription'>
                                                        <div>
                                                            @error('inscription')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Frais_etude') }}</label>
                                                    <input class="form-control" type="text" wire:model='frais'>
                                                    <div>
                                                        @error('frais')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
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

                                @foreach ($niveaux as $niveau)
                                    <div class="col-md-4">
                                        <ul class="list-group list-group-bordered">
                                            <li class="list-group-item active"> {{ $niveau->niveau }}
                                            </li>
                                        </ul>
                                        @foreach ($fraisScolaires as $frais)
                                            @if ($niveau->id == $frais->niveau_etude_id)
                                                <ul class="list-group list-group-bordered">
                                                    @if (!empty(Permission('Modifier')))
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            wire:click.prevent="editFraisEtude({{ $frais->id }})"
                                                            data-toggle="modal" data-target="#editFraisEtude"
                                                            data-whatever="@getbootstrap">
                                                            <li class="list-group-item">
                                                                {{ $frais->filiereEtude->filiere }}
                                                                <span
                                                                    class="badge badge-danger float-left">{{ $frais->frais_inscription }}</span>
                                                                <span
                                                                    class="badge badge-success float-right">{{ $frais->frais_scolaire }}</span>
                                                            </li>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <li class="list-group-item">
                                                                {{ $frais->filiereEtude->filiere }}
                                                                <span
                                                                    class="badge badge-danger float-left">{{ $frais->frais_inscription }}</span>
                                                                <span
                                                                    class="badge badge-success float-right">{{ $frais->frais_scolaire }}</span>
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
                                        <div wire:ignore.self class="modal fade" id="editFraisEtude" tabindex="-1"
                                            niveau_etude="dialog" aria-labelledby="exampleModalCenterTitle"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" niveau_etude="document">
                                                <form wire:submit.prevent='updateFraisEtude'>
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                                {{ __('message.Modifier') }}
                                                                {{ __('message.Frais_etude') }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="form-group">
                                                                <label>{{ __('message.Niveau_etude') }}</label>
                                                                <select class="form-control" wire:model='niveau'>
                                                                    <option value=""> </option>
                                                                    @foreach ($niveaux as $niveau)
                                                                        <option value="{{ $niveau->id }}">
                                                                            {{ $niveau->niveau }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div>
                                                                    @error('niveau')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Filiere_etude') }}</label>
                                                                    <select class="form-control" wire:model='filiere'>
                                                                        <option value=""> </option>
                                                                        @foreach ($filieres as $filiere)
                                                                            <option value="{{ $filiere->id }}">
                                                                                {{ $filiere->filiere }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div>
                                                                        @error('filiere')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>{{ __('message.Frais_inscription') }}</label>
                                                                    <input class="form-control" type="text"
                                                                        wire:model='inscription'>
                                                                    <div>
                                                                        @error('inscription')
                                                                            <span
                                                                                class="text-error">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('message.Frais_etude') }}</label>
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
