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
                            <div class="ibox-title">{{ __('message.Liste_banque') }}</div>
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
                                <form wire:submit='addbanque'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Ajouter_banque') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('message.Banque') }}</label>
                                                <input class="form-control" type="text" wire:model='banque'>
                                                <div>
                                                    @error('banque')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Numero_compte') }}</label>
                                                <input class="form-control" type="text" value="0"
                                                    wire:model='numero_compte'>
                                                <div>
                                                    @error('numero_compte')
                                                        <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Solde') }}</label>
                                                <input class="form-control" type="text" value="0"
                                                    wire:model='solde'>
                                                <div>
                                                    @error('solde')
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
                        <div class="ibox-body">
                            <form class="form-inline">
                                <label class="sr-only" for="ex-email">{{ __('message.banque') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                    wire:model.live='recherche'
                                    placeholder="{{ __('message.Recherche') }} {{ __('message.Banque') }} ">

                            </form>
                            <div class="table-responsive">
                                @include('livewire.alert.message')
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.Banque') }}</th>
                                            <th>{{ __('message.Numero_compte') }}</th>
                                            <th style="text-align:right">{{ __('message.Solde') }}</th>
                                            <th>{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($banques as $banque)
                                            <tr>
                                                <td>{{ $banque->banque }}</td>
                                                <td>{{ $banque->numero_compte}}</td>
                                                <td style="text-align:right"><strong>{{ number_format($banque->solde  , 0, ',', ' ') }}</strong></td>                                                   
                                              
                                                <td>
                                                    @if (!empty(Permission('Modifier')))
                                                        <button type="button"
                                                            wire:click.prevent="editbanque({{ $banque->id }})"
                                                            class="btn btn-sm  btn-info" data-toggle="modal"
                                                            data-target="#editbanque" data-whatever="@getbootstrap"><i
                                                                class="feather fa fa-edit"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Liens de pagination -->
                                <div>
                                    {{ $banques->links() }}
                                </div>
                                {{-- Modal editUser --}}
                                <div wire:ignore.self class="modal fade" id="editbanque" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <form wire:submit='updatebanque'>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                        {{ __('message.Modifier_banque') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>{{ __('message.Banque') }}</label>
                                                        <input class="form-control" type="text" wire:model='banque'>
                                                        <div>
                                                            @error('banque')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('message.Numero_compte') }}</label>
                                                        <input class="form-control" type="text" value="0"
                                                            wire:model='numero_compte'>
                                                        <div>
                                                            @error('numero_compte')
                                                                <span class="text-error">{{ $message }}</span>
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
