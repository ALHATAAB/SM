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
                    @if (!empty(Permission('Afficher')))
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Coefficient_matiere') }}</div>
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
                                    <form wire:submit='AddCoefficientMatiere'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter_utilisateur') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Classe_etude') }}</label>
                                                    <select class="form-control" wire:model='classe'>
                                                        <option value=""> </option>
                                                        @foreach ($classes as $classe)
                                                            <option value="{{ $classe->id }}">
                                                                {{ $classe->classe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('classe')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group">
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
                                                <div class="form-group">
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
                                                        @error('niveau')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Matiere_etude') }}</label>
                                                        <select class="form-control" wire:model='matiere'>
                                                            <option value=""> </option>
                                                            @foreach ($matieres as $matiere)
                                                                <option value="{{ $matiere->id }}">
                                                                    {{ $matiere->matiere_etude }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('matiere')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Coefficient_matiere') }}</label>
                                                        <input class="form-control" type="text"
                                                            wire:model='coefficient'>
                                                        <div>
                                                            @error('coefficient')
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
                            @include('livewire.alert.message')
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="ibox">
                                        <div class="ibox-body" style="background-color:darkgray; color:azure ">
                                            <p><strong> <i class="ti ti-filter"></i> Filtre</strong> </p>
                                            <div class="row">
                                                <div class="col-sm-12 form-group">
                                                    <label>{{ __('message.Classe_etude') }}</label>
                                                    <select class="form-control" wire:model.live='classe'>
                                                        <option value=""> </option>
                                                        @foreach ($classes as $classe)
                                                            <option value="{{ $classe->id }}">
                                                                {{ $classe->classe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('classe')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="ibox">
                                        <div class="ibox-body">
                                            <div class="table-responsive">
                                                @if ($coefficientCount > 0)
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                {{-- <th width="50px"></th> --}}
                                                                <th>{{ __('message.Matiere_etude') }}</th>
                                                                <th>{{ __('message.Coefficient_matiere') }}</th>
                                                                @if (!empty(Permission('Modifier')))
                                                                    <th>{{ __('message.Actions') }}</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($coefficientMatiere as $valeur)
                                                                <tr>
                                                                    <td>{{ $valeur->matiereEtude->matiere_etude }}</td>
                                                                    <td>{{ $valeur->coefficient }}</td>
                                                                    <td>
                                                                        @if (!empty(Permission('Modifier')))
                                                                            <button type="button"
                                                                                wire:click.prevent="editCoefficientMatiere({{ $valeur->id }})"
                                                                                class="btn btn-sm  btn-info"
                                                                                data-toggle="modal"
                                                                                data-target="#editCoefficientMatiere"
                                                                                data-whatever="@getbootstrap"><i
                                                                                    class="feather fa fa-edit"></i></button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                    {{-- Modal editfiliere_etude --}}
                                                    <div wire:ignore.self class="modal fade" id="editCoefficientMatiere"
                                                        tabindex="-1" niveau_etude="dialog"
                                                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered"
                                                            niveau_etude="document">
                                                            <form wire:submit.prevent='updateCoefficientMatiere'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Filiere_etude') }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                       <div class="form-group">
                                                    <label>{{ __('message.Classe_etude') }}</label>
                                                    <select class="form-control" wire:model='classe'>
                                                        <option value=""> </option>
                                                        @foreach ($classes as $classe)
                                                            <option value="{{ $classe->id }}">
                                                                {{ $classe->classe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('classe')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group">
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
                                                <div class="form-group">
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
                                                        @error('niveau')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Matiere_etude') }}</label>
                                                        <select class="form-control" wire:model='matiere'>
                                                            <option value=""> </option>
                                                            @foreach ($matieres as $matiere)
                                                                <option value="{{ $matiere->id }}">
                                                                    {{ $matiere->matiere_etude }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('matiere')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Coefficient_matiere') }}</label>
                                                        <input class="form-control" type="text"
                                                            wire:model='coefficient'>
                                                        <div>
                                                            @error('coefficient')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-primary">{{ __('message.Modifier') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning alert-bordered-right"><strong>Alert
                                                            ! </strong> Pas de donnée.</div>
                                                @endif
                                                <!-- Liens de pagination -->
                                                <div>
                                                    {{-- {{ $filiere_etudes->links() }} --}}


                                                </div>
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
