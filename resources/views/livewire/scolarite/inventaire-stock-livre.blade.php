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

                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_livre') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                {{-- @if (!empty(Permission('Ajouter')))
                                    <button type="button" class="btn btn-ligth" data-toggle="modal"
                                        data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                    </button>
                                @endif --}}
                            </div>
                        </div>
                        <div class="ibox-body">
                            <form class="form-inline">
                                <label class="sr-only" for="ex-email">{{ __('message.Recherche') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                    wire:model.live='recherche' placeholder="{{ __('message.Recherche') }} ">

                            </form>
                            @include('livewire.alert.message')
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.Titre') }}</th>
                                            <th>{{ __('message.Auteur') }}</th>
                                            <th>{{ __('message.Stock') }}</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($livres as $livre)
                                            <tr>
                                                <td>{{ $livre->titre }}</td>
                                                <td>{{ $livre->auteur }}</td>
                                                <td>{{ $livre->stock_actuel }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div>
                                    {{ $livres->links() }}
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
