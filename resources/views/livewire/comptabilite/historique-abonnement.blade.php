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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="ibox ibox-primary">
                                <div class="ibox-head" style="background-color: #bdc3c7;color:#000000">

                                        <div class="ibox-title">{{ __('message.Operation_abonnement_apprenant') }}</div>
                                        <div class="ibox-tools">
                                        <form class="form-inline">
                                            <label class="sr-only"
                                                for="ex-pass">{{ __('message.Recherche') }}</label>
                                            <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass"
                                                type="text" wire:model.live='recherche'
                                                placeholder="{{ __('message.Recherche') }}">
                                            
                                        </form>
                                    </div>
                                    </div>
                                <div class="ibox-body">
                                    <br>
                            
                                    @include('livewire.alert.message')
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th width="50px"></th>
                                                    <th>{{ __('message.Description') }}</th>
                                                    <th>{{ __('message.Nom') }}</th>
                                                    <th>{{ __('message.Prenom') }}</th>
                                                    <th>{{ __('message.Abonnement') }}</th>
                                                    <th>{{ __('message.Montant') }}</th>
                                                    <th>{{ __('message.Caisse') }}</th>
                                                    <th>{{ __('message.Caissier') }}</th>
                                                    <th>{{ __('message.Date') }}</th>
                                                     @if (!empty(Permission('Supprimer')))
                                                    <th>{{ __('message.Actions') }}</th>
                                                      @endif
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($operation_abonnements as $operation_abonnement)
                                                    <tr>
                                                        <td>
                                                                <span class="input-span">{{ $operation_abonnement->id }}</span>
                                                        
                                                        </td>
                                                        <td>{{ $operation_abonnement->description }}</td>
                                                        <td>{{ $operation_abonnement->preInscription->nom }}</td>
                                                        <td>{{ $operation_abonnement->preInscription->prenom }}</td>
                                                        <td>{{ $operation_abonnement->type_abonnement->type_abonnement }} : {{ $operation_abonnement->nombre }}  {{ $operation_abonnement->periode }}</td>
                                                        <td>{{ $operation_abonnement->frais_abonnement }}</td>
                                                        @if($operation_abonnement->caisse_id!=Null)
                                                        <td>{{ $operation_abonnement->caisse->caisse }}</td>
                                                        @else
                                                         <td>{{ $operation_abonnement->banque->banque }}</td>
                                                        @endif
                                                        <td>{{ $operation_abonnement->user->nom }}</td>
                                                        <td>{{ Carbon\Carbon::parse($operation_abonnement->date_comptable)->translatedFormat('l d F Y') }}
                                                            {{-- {{ $operation_abonnement->date }}</td> --}}
                                                        </td>
                                                        @if (!empty(Permission('Supprimer')))
                                                        <td>
                                                            {{-- <button class="btn btn-default btn-xs m-r-5"
                                                                data-toggle="tooltip" data-original-title="Edit">
                                                                 <i class="fa fa-pencil font-14"></i>
                                                                </button> --}}
                                                            <button class="btn btn-default btn-xs" data-toggle="tooltip"
                                                                data-original-title="Delete"
                                                                wire:click.prevent="DeleteOperation({{ $operation_abonnement->id }})">
                                                                <i class="fa fa-trash font-14"></i></button>
                                                        </td>
                                                        @endif
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                        <div>
                                            {{ $operation_abonnements->links() }}
                                        </div>
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
