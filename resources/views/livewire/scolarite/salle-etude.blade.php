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
                @if(!empty(Permission('Afficher')))
                <div class="ibox ibox-primary">

                <div class="ibox-head ibox ibox-danger">
                    <div class="ibox-title">{{ __('message.Liste_salle_etude') }} </div>
                    <div class="ibox-tools">
                        <!-- Button trigger modal -->
                        @if(!empty(Permission('Ajouter')))
                        <button type="button" class="btn btn-light" data-toggle="modal"
                            data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <!-- Modal -->
                <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form wire:submit='addSalleEtude'>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                        {{ __('message.Ajouter_salle_etude') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                    <label>{{ __('message.Salle_etude') }}</label>
                                        <input class="form-control" type="text" wire:model='salle'>
                                        <div>
                                            @error('salle')
                                            <span class="text-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <label>{{ __('message.Capacite') }}</label>
                                        <input class="form-control" type="text" wire:model='capacite'>
                                        <div>
                                            @error('capacite')
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
                       <label class="sr-only" for="ex-email">{{ __('message.Salle_etude') }}</label>
                        <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                        wire:model.live='recherche'    placeholder="{{ __('message.Recherche') }} {{ __('message.Salle_etude') }} ">
                    </form>
                    <div class="table-responsive">
                        @include('livewire.alert.message')
                        <table class="table">
                            <thead>
                                <tr>
                                    {{-- <th width="50px"></th> --}}
                                    <th>{{ __('message.Salle_etude') }}</th>
                                    <th>{{ __('message.Capacite') }}</th>
                                    @if( !empty(Permission('Modifier')) )
                                    <th>{{ __('message.Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salle_etudes as $salle_etude)
                                <tr>
                                    <td>{{ $salle_etude->salle }}</td>
                                    <td>{{ $salle_etude->capacite}}</td>
                                    <td>
                                        @if(!empty(Permission('Modifier')))
                                        <button type="button"
                                            wire:click.prevent="editSalleEtude({{ $salle_etude->id }})"
                                            class="btn btn-sm  btn-info" data-toggle="modal" data-target="#editSalleEtude"
                                            data-whatever="@getbootstrap"><i
                                                class="feather fa fa-edit"></i></button>
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Liens de pagination -->
                        <div>
                            {{ $salle_etudes->links() }}
                        </div>

                        {{-- Modal editsalle_etude --}}
                        <div wire:ignore.self class="modal fade" id="editSalleEtude" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit.prevent='updateSalleEtude'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Modifier') }} {{ __('message.Salle_etude') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                    <div class="form-group">
                                    <label>{{ __('message.Salle_etude') }}</label>
                                        <input class="form-control" type="text" wire:model='salle'>
                                        <div>
                                            @error('salle')
                                            <span class="text-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <label>{{ __('message.Capacite') }}</label>
                                        <input class="form-control" type="text" wire:model='capacite'>
                                        <div>
                                            @error('capacite')
                                            <span class="text-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{
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
            @else
            <div class="ibox ibox-primary">
                <div class="ibox-body text-center">
                    <h1 class="m-t-20">404</h1>
                    <p class="error-title">PAGE NOT FOUND</p>
                </div>
            </div>
            @endif

                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
