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
                    <div class="col-lg-12 col-md-12">
                        @include('livewire.alert.message')
                        <div class="ibox">
                            <div class="ibox-head ibox ibox-danger">
                                <div class="ibox-title">{{ __('message.Grand_livre') }}</div>
                                <div class="ibox-tools">
                                    <div class="btn-group">
                                        @if ($countCaisse > 0)
                                            @if (!empty(Permission('Tresorier')))
                                                <button class="btn btn-success  " data-toggle="modal"
                                                    data-target="#versementModal">VERSEMENT CAISSE VERS BANQUE</button>
                                                <button class="btn btn-danger "data-toggle="modal"
                                                    data-target="#retraitModal">RETRAIT BANQUE VERS CAISSE</button>
                                                <button class="btn btn-primary "data-toggle="modal"
                                                    data-target="#depotModal">DEPOT BANQUE</button>
                                            @endif
                                        @endif
                                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"><i
                                                class="fa fa-cogs"></i> Compte Banques <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($banques as $banque)
                                                <li>
                                                    <a class="dropdown-item" href="javascript:;"
                                                        wire:click.prevent="grandLivreBanque({{ $banque->id }})">{{ $banque->banque . ' (' . $banque->numero_compte . ')' }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-body">
                                <div class="ibox-body">
                                    @if ($coundHistorique > 0)
                                        <div class="ibox-head ibox">
                                            <div class="container">
                                                <div class="row align-items-start">
                                                    <div class="col-sm">
                                                        <strong> Banque</strong> : {{ $grandLivre->banque }}
                                                    </div>
                                                    <div class="col-sm">
                                                        <strong>Numero de compte</strong> :
                                                        ({{ $grandLivre->numero_compte }})
                                                    </div>
                                                    <div class="col-sm">
                                                        <strong>Solde banque </strong> :
                                                        {{ number_format($grandLivre->solde, 0, ',', ' ') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">{{ __('message.Date') }}</th>
                                                        <th width="450px">{{ __('message.Description') }}
                                                        </th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Entree') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Sortie') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Solde') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($historiques as $historique)
                                                        <tr>
                                                            <td>{{ $historique->date_comptable }}</td>
                                                            <td style="max-width: 220px;">
                                                                {{ $historique->description }}</td>
                                                            <td style="text-align:right" class="text-success">
                                                                {{ number_format($historique->entree, 0, ',', ' ') }}
                                                            </td>
                                                            <td style="text-align:right" class="text-danger">
                                                                {{ number_format($historique->sortie, 0, ',', ' ') }}
                                                            </td>
                                                            <td style="text-align:right">
                                                                {{ number_format($historique->solde, 0, ',', ' ') }}
                                                            </td>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>

                                        </div>
                                    @else
                                        <div class="text-center">
                                            Aucune information
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Modal versement-->
                        <div wire:ignore.self class="modal fade" id="versementModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addVersement'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Versement') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('message.Caisse') }}</label>
                                                <select class="form-control" wire:model='caisse_id'>
                                                    <option value=""> </option>
                                                    @foreach ($caisses as $caisse)
                                                        <option value="{{ $caisse->id }}">
                                                            {{ $caisse->caisse }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('compte_banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Compte_banque') }}</label>
                                                <select class="form-control" wire:model='compte_banque'>
                                                    <option value=""> </option>
                                                    @foreach ($banques as $banque)
                                                        <option value="{{ $banque->id }}">
                                                            {{ $banque->banque . ' (' . $banque->numero_compte . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('compte_banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Montant') }}</label>
                                                <input class="form-control" type="text" wire:model='montant'>
                                                <div>
                                                    @error('montant')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Reference') }}</label>
                                                <input class="form-control" type="text" wire:model='reference'>
                                                <div>
                                                    @error('reference')
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
                        <!-- Modal retrait-->
                        <div wire:ignore.self class="modal fade" id="retraitModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addRetrait'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Retrait') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('message.Compte_banque') }}</label>
                                                <select class="form-control" wire:model='compte_banque'>
                                                    <option value=""> </option>
                                                    @foreach ($banques as $banque)
                                                        <option value="{{ $banque->id }}">
                                                            {{ $banque->banque . ' (' . $banque->numero_compte . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('compte_banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Caisse') }}</label>
                                                <select class="form-control" wire:model='caisse_id'>
                                                    <option value=""> </option>
                                                    @foreach ($caisses as $caisse)
                                                        <option value="{{ $caisse->id }}">
                                                            {{ $caisse->caisse }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('compte_banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Montant') }}</label>
                                                <input class="form-control" type="text" wire:model='montant'>
                                                <div>
                                                    @error('montant')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Reference') }}</label>
                                                <input class="form-control" type="text" wire:model='reference'>
                                                <div>
                                                    @error('reference')
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
                        <!-- Modal depot-->
                        <div wire:ignore.self class="modal fade" id="depotModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addDepot'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Depot') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('message.Compte_banque') }}</label>
                                                <select class="form-control" wire:model='compte_banque'>
                                                    <option value=""> </option>
                                                    @foreach ($banques as $banque)
                                                        <option value="{{ $banque->id }}">
                                                            {{ $banque->banque . ' (' . $banque->numero_compte . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('compte_banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Montant') }}</label>
                                                <input class="form-control" type="text" wire:model='montant'>
                                                <div>
                                                    @error('montant')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Reference') }}</label>
                                                <input class="form-control" type="text" wire:model='reference'>
                                                <div>
                                                    @error('reference')
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
                    </div>

                    @if ($depotBanqueCount > 0)
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">Dépôt banque</div>
                            </div>
                            <div class="ibox-body">
                                <table class="table table-bordered">
                                    <thead class="thead-default">
                                        <tr>
                                            <th>Banque</th>
                                            <th>Reference</th>
                                            <th>Montant </th>
                                            <th>Caissier</th>
                                            @if (!empty(Permission('Valider')))
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($depotBanques as $encaissement)
                                            <tr>
                                                <td>{{ $encaissement->banque->banque }}</td>
                                                <td>{{ $encaissement->reference }}</td>
                                                <td>{{ $encaissement->montant }}</td>
                                                <td>{{ $encaissement->user->nom . ' ' . $encaissement->user->prenom }}
                                                </td>
                                                <td>
                                                    @if ($encaissement->statuts == 'AUTORISER')
                                                        @if (!empty(Permission('Supprimer')))
                                                            {{-- <button class="btn btn-default btn-xs m-r-5"
                                                                data-toggle="tooltip" data-original-title="Edit">
                                                                 <i class="fa fa-pencil font-14"></i>
                                                                </button> --}}
                                                            <button class="btn btn-default btn-xs"
                                                                data-toggle="tooltip" data-original-title="Delete"
                                                                wire:click.prevent="annuler({{ $encaissement->id }})">
                                                                <i class="fa fa-trash font-14"></i></button>
                                                        @endif
                                                    @else
                                                        @if (!empty(Permission('Valider')))
                                                            <button class="btn btn-success"
                                                                wire:click.prevent="autoriser({{ $encaissement->id }})">Autoriser</button>
                                                            <button class="btn btn-danger"
                                                                wire:click.prevent="rejeter({{ $encaissement->id }})">Rejeter</button>
                                                        @endif
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
