<div>

    <body class="fixed-navbar">
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
                            <div class="ibox-title">{{ __('message.Liste_abonnee') }}</div>
                            <div class="ibox-tools">
                                {{-- @if (!empty(Permission('Ajouter')))
                                    <button type="button" class="btn btn-light" data-toggle="modal"
                                        data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                    </button>
                                @endif --}}
                            </div>
                        </div>

                        <div class="ibox-body">
                            <form class="form-inline mb-3">
                                <input class="form-control mr-2" id="ex-email" type="text"
                                    wire:model.live='recherche' placeholder="{{ __('message.Recherche') }} ">
                            </form>

                            @include('livewire.alert.message')

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th width="50px">{{ __('message.Matricule') }}</th>
                                            <th>{{ __('message.Nom') }} {{ __('message.Prenom') }} {{ __('message.Telephone') }}</th>
                                            <th>{{ __('message.Adresse') }}</th>
                                            <th>{{ __('message.Type_abonnement') }}</th>
                                            <th>{{ __('message.Periode') }}</th>
                                            <th>{{ __('message.Periode') }} {{ __('message.Date_fin') }}</th>
                                            <th>{{ __('message.Frais_abonnement') }}</th>
                                            <th>{{ __('message.Remise') }}</th>
                                            <th>{{ __('message.Frais_payer') }}</th>
                                            <th>{{ __('message.Statut') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($abonnees as $abonnee)
                                            <tr>
                                                <td>{{ $abonnee->matricule }}</td>
                                                <td>{{ $abonnee->preInscription->nom }}
                                                    {{ $abonnee->preInscription->prenom }} - {{ $abonnee->preInscription->telephone }}</td>
                                                <td>{{ $abonnee->preInscription->adresse }}</td>
                                                <td>{{ $abonnee->type_abonnement->type_abonnement }}</td>
                                                <td>{{ $abonnee->nombre }} {{ $abonnee->periode }}</td>
                                                <td>{{ $abonnee->date_debut }} - {{ $abonnee->date_fin }}</td>
                                                <td class="text-right">
                                                    {{ number_format($abonnee->frais_abonnement + $abonnee->remise, 0, ',', ' ') }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($abonnee->remise, 0, ',', ' ') }}</td>
                                                <td class="text-right font-weight-bold">
                                                    {{ number_format($abonnee->frais_abonnement, 0, ',', ' ') }}</td>

                                                @php
                                                    $dateFin = Carbon\Carbon::parse($abonnee->date_fin);
                                                    $aujourdhui = Carbon\Carbon::now();
                                                @endphp

                                                <td class="text-center">
                                                    @if ($aujourdhui->gt($dateFin))
                                                        <span class="badge badge-danger">Expiré</span>
                                                    @else
                                                        <span class="badge badge-success">En cours</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Liens de pagination -->
                                <div class="d-flex justify-content-end">
                                    {{ $abonnees->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        @include('components.layouts.loading')
    </body>
</div>
