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
                    <div class="ibox ibox-primary">
                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_caisse') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                @if(!empty(Permission('Ajouter')))
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
                                <form wire:submit='addCaisse'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Ajouter_caisse') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('message.Caisse') }}</label>
                                                <input class="form-control" type="text" wire:model='caisse'>
                                                <div>
                                                    @error('caisse')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- <div class="form-group">
                                                <label>{{ __('message.Montant_max') }}</label>
                                                <input class="form-control" type="text" value="0" wire:model='montant_max'>
                                                <div>
                                                    @error('montant_max')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            {{-- <div class="form-group">
                                                <label>{{ __('message.Montant_min') }}</label>
                                                <input class="form-control" type="text" value="0" wire:model='montant_min'>
                                                <div>
                                                    @error('montant_min')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            <div class="form-group">
                                                <label>{{ __('message.Solde') }}</label>
                                                <input class="form-control" type="text" value="0" wire:model='solde'>
                                                <div>
                                                    @error('solde')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Utilisateur') }}</label>
                                                <select class="form-control" wire:model='user_id'>
                                                    <option value=""> </option>
                                                    @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->prenom .' '.$user->nom }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('user_id')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{
                                                __('message.Fermer') }}</button>
                                            <button type="Submit" class="btn btn-primary">{{ __('message.Enregistrer')
                                                }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="ibox-body">
                            <form class="form-inline">
                                <label class="sr-only" for="ex-email">{{ __('message.Caisse') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                wire:model.live='recherche'    placeholder="{{ __('message.Recherche') }} {{ __('message.Caisse') }} ">
                                
                            </form>
                            <div class="table-responsive">   
                                @include('livewire.alert.message')
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.Caisse') }}</th>
                                            {{-- <th>{{ __('message.Montant_min') }}</th>
                                            <th>{{ __('message.Montant_max') }}</th> --}}
                                            <th style="text-align:right">{{ __('message.Solde') }}</th>
                                            <th>{{ __('message.Utilisateur') }}</th>
                                            <th>{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($caisses as $caisse)
                                        <tr>
                                            <td>{{ $caisse->caisse }}</td>
                                            {{-- <td>{{ $caisse->montant_min }}</td>
                                            <td>{{ $caisse->montant_max }}</td> --}}
                                            <td style="text-align:right"><strong>{{ number_format($caisse->solde  , 0, ',', ' ') }}</strong></td>                                                   
                                                        
                                            <td>{{ $caisse->user->nom.' '.$caisse->user->prenom }}</td>
                                            <td>
                                                @if(!empty(Permission('Modifier')))
                                                <button type="button" wire:click.prevent="editCaisse({{ $caisse->id }})"
                                                    class="btn btn-sm  btn-info" data-toggle="modal"
                                                    data-target="#editCaisse" data-whatever="@getbootstrap"><i
                                                        class="feather fa fa-edit"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                 <!-- Liens de pagination -->
                                 <div>
                                    {{ $caisses->links() }}
                                </div>

                                {{-- Modal editUser --}}
                                <div wire:ignore.self class="modal fade" id="editCaisse" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <form wire:submit='updateCaisse'>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                        {{ __('message.Modifier_caisse') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>{{ __('message.Caisse') }}</label>
                                                        <input class="form-control" type="text" wire:model='caisse'>
                                                        <div>
                                                            @error('caisse')
                                                            <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- <div class="form-group">
                                                        <label>{{ __('message.Montant_max') }}</label>
                                                        <input class="form-control" type="text" value="0" wire:model='montant_max'>
                                                        <div>
                                                            @error('montant_max')
                                                            <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div> --}}
                                                    {{-- <div class="form-group">
                                                        <label>{{ __('message.Montant_min') }}</label>
                                                        <input class="form-control" type="text" value="0" wire:model='montant_min'>
                                                        <div>
                                                            @error('montant_min')
                                                            <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div> --}}
                                                    <div class="form-group">
                                                        <label>{{ __('message.Utilisateur') }}</label>
                                                        <select class="form-control" wire:model='user_id'>
                                                            <option value=""> </option>
                                                            @foreach ($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->prenom .' '.$user->nom }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('user_id')
                                                            <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{
                                                        __('message.Fermer') }}</button>
                                                    <button type="Submit" class="btn btn-primary">{{
                                                        __('message.Modifier')
                                                        }}</button>
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
