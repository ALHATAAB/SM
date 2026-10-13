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
                            <div class="ibox-title">{{ __('message.Liste_preinscription') }}</div>
                            <div class="ibox-tools">
                               <form class="form-inline">
                                <label class="sr-only" for="ex-email">{{ __('message.Nom') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                    wire:model.live='recherche'
                                    placeholder="{{ __('message.Recherche') }}">
                            </form>
                            </div>
                        </div>
                        <div class="ibox-body">
                            
                            @include('livewire.alert.message')
                        @if(isset($preinscriptionCount)>0)    
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="50px">Matricule</th>
                                            <th>{{ __('message.Nom') }}</th>
                                            <th>{{ __('message.Prenom') }}</th>
                                            <th>{{ __('message.Telephone') }}</th>
                                            <th>{{ __('message.Adresse') }}</th>
                                            @if (!empty(Permission('Comptabilite')))
                                                <th></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($preinscriptions as $preinscription)
                                            <tr>
                                                <td>

                                                    {{ $preinscription->matricule }}

                                                </td>
                                                <td>{{ $preinscription->nom }}</td>
                                                <td>{{ $preinscription->prenom }}</td>
                                                <td>{{ $preinscription->telephone }}</td>
                                                <td>{{ $preinscription->adresse }}</td>
                                                @if (!empty(Permission('Comptabilite')))
                                                    <td>
                                                        <a href="{{ url('/inscription?matricule=' . urlencode($preinscription->matricule)) }}"
                                                            class="btn btn-primary btn-sm text-nowrap">
                                                            <i class="fa fa-arrow-right"></i> Inscrire
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <!-- Liens de pagination -->
                                <div>
                                    {{ $preinscriptions->links() }}
                                </div>
                            </div>
                        @endif
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
