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
                            <div class="ibox-head">
                                <div class="ibox-title">Approvisionnement caisse</div>
                                <div class="ibox-tools">
                                    @if ($countCaisse > 0)
                                        <button class="btn btn-success btn-block " data-toggle="modal"
                                            data-target="#approModal"><i class="fa fa-plus"></i></button>
                                    @endif
                                </div>
                            </div>

                            <!-- Modal appro-->
                            <div wire:ignore.self class="modal fade" id="approModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addAppro'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.appro') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Caisse') }}</label>
                                                    <select class="form-control" wire:model='caisse_destination_id'>
                                                        <option value=""> </option>
                                                        @foreach ($caisses as $caisse)
                                                            <option value="{{ $caisse->id }}">
                                                                {{ $caisse->caisse }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('caisse_destination_id')
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
                             @if ($approCaisseCount > 0)
                            <div class="ibox-body"> 
                                @include('livewire.alert.message')
                                <table class="table table-bordered">
                                    <thead class="thead-default">
                                        <tr>
                                            <th>Date comptable</th>
                                            <th>Caisse source</th>
                                            <th>Caisse approvisionnée</th>
                                            <th>Reference</th>
                                            <th>Montant </th>
                                            <th>Caissier source</th>
                                            <th>Etat</th>
                                            @if (!empty(Permission('Valider')) || !empty(Permission('Tresorier')))
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($approCaisses as $encaissement)
                                            <tr>
                                                <td>{{ $encaissement->date_comptable }}</td>
                                                <td>{{ $encaissement->caisse->caisse }}</td>
                                                <td>{{ $encaissement->caisse_destination->caisse }}</td>
                                                <td>{{ $encaissement->reference }}</td>
                                                <td>{{ $encaissement->montant }}</td>
                                                <td>{{ $encaissement->user->nom . ' ' . $encaissement->user->prenom }}
                                                </td>
                                                <td>
                                                    @if ($encaissement->statuts == 'NON AUTORISER')
                                                        {{ $encaissement->statuts }}
                                                    @elseif ($encaissement->statuts == 'AUTORISER')
                                                        <button class="btn btn-success btn-circle"><i
                                                                class="fa fa-check"></i></button>
                                                    @elseif ($encaissement->statuts == 'REJETER')
                                                        <button class="btn btn-danger btn-circle"><i
                                                                class="fa fa-times"></i></button>
                                                    @endif
                                                </td>
                                                @if (!empty(Permission('Valider')) || !empty(Permission('Tresorier')))
                                                    <td>
                                                        @if ($encaissement->statuts == 'NON AUTORISER')
                                                            <button class="btn btn-success"
                                                                wire:click.prevent="autoriser({{ $encaissement->id }})">Autoriser</button>
                                                            <button class="btn btn-danger"
                                                                wire:click.prevent="rejeter({{ $encaissement->id }})">Rejeter</button>
                                                        @elseif ($encaissement->statuts == 'AUTORISER')
                                                            {{ $encaissement->date_validation }}
                                                        @elseif ($encaissement->statuts == 'REJETER')
                                                            {{ $encaissement->date_validation }}
                                                        @endif


                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <!-- Liens de pagination -->
                                <div>
                                    {{ $approCaisses->links() }}
                                </div>
                            </div>
                             @endif
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
