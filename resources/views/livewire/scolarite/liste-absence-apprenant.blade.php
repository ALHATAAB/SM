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

                    <div class="ibox ibox-danger">
                        <div class="ibox-head ibox ">
                            <div class="ibox-title">{{ __('message.Liste_absence_apprenant') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                @if (!empty(Permission('Ajouter')))
                                    <a href="{{ url('/absence/apprenant/') }}">
                                        <button type="button" class="btn btn-ligth"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>  
                                @endif
                            </div>
                        </div>
                        <div class="ibox-body">
                                <div class="ibox-body">
                                    <div>
                                        <span><strong> <i class="ti ti-filter"></i> Filtre</strong></span>
                                        <form class="form-inline">
                                            <label class="sr-only"
                                                for="ex-pass">{{ __('message.Date_debut') }}</label>
                                            <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass"
                                                type="date" wire:model.live='dateDebut'
                                                placeholder="{{ __('message.Date_debut') }}">
                                            <label class="sr-only" for="ex-pass">{{ __('message.Date_fin') }}</label>
                                            <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass"
                                                type="date" wire:model.live='dateFin'
                                                placeholder="{{ __('message.Date_fin') }}">
                                        </form>
                                    </div>
                                    <br>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('message.Nom') }}
                                                        {{ __('message.Prenom') }}</th>
                                                    <th>{{ __('message.Classe') }}</th>
                                                    <th>{{ __('message.Matiere') }}</th>
                                                    <th>{{ __('message.Date') }}</th>
                                                    <th>{{ __('message.heure_debut') }}</th>
                                                    <th>{{ __('message.heure_fin') }}</th>
                                                    <th>{{ __('message.Enseignant') }}</th>
                                                    <th>{{ __('message.Salle_etude') }}</th>
                                                    <th>{{ __('message.Statuts') }}</th>
                                                    <th>{{ __('message.Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($absences as $absence)
                                                    <tr>
                                                        <td>{{ $absence->inscription?->preInscription?->nom }}
                                                            {{ $absence->inscription?->preInscription?->prenom }}</td>
                                                        <td>{{ $absence->classe?->classe }}</td>
                                                        <td>{{ $absence->matiere?->matiere_etude }}</td>
                                                        <td>{{ $absence->date }}</td>
                                                        <td>{{ $absence->heure_debut }}</td>
                                                        <td>{{ $absence->heure_fin }}</td>
                                                        <td>{{ $absence->enseignant?->prenom }} {{ $absence->enseignant?->nom }}</td>
                                                        <td>{{ $absence->salle?->salle }}</td>

                                                        <td>
                                                            @if ($absence->justifiee == 1)
                                                                <span
                                                                    class="badge badge-success m-r-5 m-b-5">Justifiee</span>
                                                            @endif
                                                            @if ($absence->justifiee == 0)
                                                                <span class="badge badge-danger m-b-5">Non justifiee</span>
                                                            @endif

                                                        </td>
                                                        <td>
                                                            <button class="btn btn-default btn-xs m-r-5"
                                                                data-toggle="tooltip" data-original-title="Edit"><i
                                                                    class="fa fa-pencil font-14"></i></button>
                                                            <button class="btn btn-default btn-xs" data-toggle="tooltip"
                                                                data-original-title="Delete"><i
                                                                    class="fa fa-trash font-14"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($absences->isEmpty())
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted">Aucune absence enregistrée pour cette période.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <div>
                                            {{ $absences->links() }}
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->

    </body>
</div>
