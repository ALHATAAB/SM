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
                    @include('livewire.alert.message')
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="ibox">
                                <div class="ibox-head ibox ibox-danger">
                                    <div class="ibox-title">{{ __('message.Gestion_depense') }}</div>
                                    <div class="ibox-tools">
                                        <div class="btn-group">
                                     @if($caisse)
                                            <button class="btn btn-danger "data-toggle="modal"
                                        data-target="#depenseCaisseModal">Règlement à la caisse </button>
                                    @endif
                                    @if (!empty(Permission('Tresorier')))
                                        <button class="btn btn-warning "data-toggle="modal"
                                            data-target="#depenseBanqueModal">Règlement à la banque </button>
                                    @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="ibox-body">
                                    @if ($countdepense > 0)
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">{{ __('message.Date') }}</th>
                                                        <th width="450px">{{ __('message.Description') }}</th>
                                                        <th width="450px">{{ __('message.Secteur_depense') }}</th>
                                                        <th width="450px">{{ __('message.Mode_paiement') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Montant') }}</th>
                                                        @if (!empty(Permission('Supprimer')))
                                                        <th  >
                                                            {{ __('message.Actions') }}
                                                        </th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($depenses as $depense)
                                                        <tr>
                                                            <td>{{ $depense->date_comptable }}</td>
                                                            <td>{{ $depense->reference }}</td>
                                                            <td>
                                                            @if ($depense->type_abonnement_id == 0)
                                                                Autres
                                                            @else
                                                                {{ $depense->type_abonnement->type_abonnement }}
                                                                
                                                            @endif
                                                            </td>
                                                            <td>{{ $depense->mode_paiement }}</td>
                                                            
                                                            <td style="text-align:right">
                                                                {{ number_format($depense->montant, 0, ',', ' ') }}
                                                            </td>
                                                           
                                                            @if (!empty(Permission('Supprimer')))
                                                             <td>
                                                            {{-- <button class="btn btn-default btn-xs m-r-5"
                                                                data-toggle="tooltip" data-original-title="Edit">
                                                                 <i class="fa fa-pencil font-14"></i>
                                                                </button> --}}
                                                            <button class="btn btn-default btn-xs"
                                                                data-toggle="tooltip" data-original-title="Delete"
                                                                wire:click.prevent="annuler({{ $depense->id }})">
                                                                <i class="fa fa-trash font-14"></i></button>
                                                            </td>
                                                            @endif
                                                            
                                                               
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>

                                        </div>
                                    @else
                                        <div class="text-center">
                                            <h4 class="m-t-20">404</h4>
                                            <p class="error-title">PAGE NOT FOUND</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">

                            {{-- <div class="ibox">
                                <div class="ibox-body">
                                    <button class="btn btn-danger btn-block"data-toggle="modal"
                                        data-target="#depenseCaisseModal">Règlement à la caisse </button>
                                    @if (!empty(Permission('Tresorier')))
                                        <button class="btn btn-warning btn-block"data-toggle="modal"
                                            data-target="#depenseBanqueModal">Règlement à la banque </button>
                                    @endif
                                </div>
                            </div> --}}

                            <!-- Modal reglement à la caisse-->
                            <div wire:ignore.self class="modal fade" id="depenseCaisseModal" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addDepenseCaisse'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Depense') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Secteur_depense') }}</label>
                                                    <select class="form-control" wire:model='secteur'>
                                                        <option value=""> </option>
                                                        @foreach ($secteurs as $secteur)
                                                            <option value="{{ $secteur->id }}">
                                                                {{ $secteur->type_abonnement }}
                                                            </option>
                                                        @endforeach
                                                        <option value="0">Autres</option>
                                                    </select>
                                                    <div>
                                                        @error('secteur')
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

                            <!-- Modal reglement banque-->
                            <div wire:ignore.self class="modal fade" id="depenseBanqueModal" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addPaiementBanque'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Depense') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Secteur_depense') }}</label>
                                                    <select class="form-control" wire:model='secteur'>
                                                        <option value=""> </option>
                                                        @foreach ($secteurs as $secteur)
                                                            <option value="{{ $secteur->id }}">
                                                                {{ $secteur->type_abonnement }}
                                                            </option>
                                                        @endforeach
                                                        <option value="0">Autres</option>
                                                    </select>
                                                    <div>
                                                        @error('secteur')
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
                                                    <input class="form-control" type="text"
                                                        wire:model='reference'>
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
