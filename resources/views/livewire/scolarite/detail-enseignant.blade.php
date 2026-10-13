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
                    <div class="row">
                        <div class="col-lg-2 col-md-2">
                            <div class="ibox color-danger">
                                <div class="ibox-body text-center">
                                    @livewire('photo-personne', ['type' => 'enseignant', 'personneId' => $enseignant->id], key('photo-enseignant-' . $enseignant->id))
                                    <h2 class="font-strong m-b-10 m-t-10">{{ $enseignant->nom }}
                                        {{ $enseignant->prenom }}</h2>
                                     <div class="m-b-20 text-muted">{{ __('message.Code') }} : {{ $enseignant->matricule }}</div>
                                    <a href="{{ url('/fiche/enseignant/' . encrypt($enseignant->id)) }}" target="_blank" class="btn btn-warning btn-sm" title="Fiche complète (PDF)"> Fiche complète
                                        <i class="ti ti-printer"></i>
                                    </a>

                                   {{-- <div>
                                        <button class="btn btn-info btn-rounded m-b-5"><i class="fa fa-plus"></i>
                                            Follow</button>
                                        <button class="btn btn-default btn-rounded m-b-5">Message</button>
                                    </div> --}}
                                </div>
                            </div>
                            {{-- <div class="ibox">
                                <ul class="list-group list-group-bordered">
                                    <strong>
                                        <h4>
                                            <li class="list-group-item">Code enseignant
                                                <span class=" float-right">{{ $enseignant->matricule }}</span>
                                            </li>
                                        </h4>
                                    </strong>
                                    <li class="list-group-item">Date de naissance
                                        <span class=" float-right">{{ $enseignant->date_naissance }}</span>
                                    </li>
                                    <li class="list-group-item">Lieu de naissance
                                        <span class=" float-right">{{ $enseignant->lieu_naissance }}</span>
                                    </li>
                                    <li class="list-group-item">Sexe
                                        <span class=" float-right">{{ $enseignant->sexe }}</span>
                                    </li>
                                    <li class="list-group-item">Grade
                                        <span class=" float-right">{{ $enseignant->grade }}</span>
                                    </li>
                                    <li class="list-group-item">Specialité
                                        <span class=" float-right">{{ $enseignant->specialite }}</span>
                                    </li>
                                </ul>

                            </div> --}}

                        </div>
                        <div class="col-lg-10 col-md-10">
                             @include('livewire.alert.message')
                            <div class="ibox">
                                <div class="ibox-body">
                                    <ul class="nav nav-tabs tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#tab-1" data-toggle="tab"><i
                                                    class="ti-bar-chart"></i> Emploi du temps</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-2" data-toggle="tab"><i
                                                    class="ti-settings"></i> Modifier</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-3" data-toggle="tab"><i
                                                    class="ti-announcement"></i> Émargement</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="tab-1">
                                            <h4 class="text-info m-b-20 m-t-20"><i class="ti ti-calendar"></i> Emploi
                                                du temps</h4>
                                                <div class="row">
                                                    @foreach ($jours as $jour)
                                                        <div class="col-md-{{ (12/count($jours)) }} mb-2">
                                                            <div class="bg-light p-2 rounded shadow-sm">
                                                                <h5 class="text-center fw-bold mb-2">
                                                                    {{ $jour }}</h5>
                                                                @forelse($seances[$jour] ?? [] as $seance)
                                                                    <div
                                                                        class="bg-white border p-2 rounded mb-2 text-sm text-center">
                                                                        <strong>{{ $seance->matiere->matiere_etude }}</strong><br>
                                                                        {{ $seance->heure_debut }} <br>
                                                                        {{ $seance->heure_fin }}<br>
                                                                        {{ $seance->salle->salle }}<br>
                                                                        {{ $seance->classe->classe }}
                                                                    </div>
                                                                @empty
                                                                    <p class="text-muted text-center">Aucune séance
                                                                    </p>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            
                                        </div>
                                        <div class="tab-pane fade" id="tab-2">
                                            <form wire:submit='editerEnseignant'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('message.Modifier_enseignant') }}</h5>
                                                        {{-- <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button> --}}
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Prenom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='prenom'>
                                                                <div>
                                                                    @error('prenom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Nom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='nom'>
                                                                <div>
                                                                    @error('nom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Date_naissance') }}</label>
                                                                <input class="form-control" type="date"
                                                                    wire:model='date_naissance'>
                                                                <div>
                                                                    @error('date_naissance')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Lieu_naissance') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='lieu_naissance'>
                                                                <div>
                                                                    @error('lieu_naissance')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Sexe') }} </label>
                                                            <select class="form-control" wire:model="sexe">
                                                                <option value=""></option>
                                                                <option value="Homme">Homme</option>
                                                                <option value="Femme">Femme</option>
                                                            </select>
                                                            <div>
                                                                @error('sexe')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Adresse') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='adresse'>
                                                            <div>
                                                                @error('adresse')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Date_embauche') }}</label>
                                                            <input class="form-control" type="date" wire:model='date_embauche'>
                                                            <div>
                                                                @error('date_embauche')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Date_depart') }}</label>
                                                            <input class="form-control" type="date" wire:model='date_depart'>
                                                            <small class="text-muted">Laisser vide tant que l'enseignant est en poste.</small>
                                                            <div>
                                                                @error('date_depart')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Telephone') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='telephone'>
                                                            <div>
                                                                @error('telephone')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Type_salaire') }} </label>
                                                                <select class="form-control"
                                                                    wire:model="type_salaire">
                                                                    <option value="">--Mode payement--</option>
                                                                    <option value="fixe">Par mois</option>
                                                                    <option value="horaire">Par heure</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Montant') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='montant'>
                                                                <div>
                                                                    @error('montant')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                         @if (!empty(Permission('Modifier')))
                                                        <button type="Submit"
                                                            class="btn btn-info">{{ __('message.Modifier') }}
                                                        </button>
                                                    @endif
                                                       
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="tab-3">
                                            <h5 class="text-info m-b-20 m-t-20"><i class="fa fa-bullhorn"></i> Latest
                                                Émargement de l'année</h5>
                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __('message.Matiere') }}</th>
                                                                            <th>{{ __('message.Date') }}</th>
                                                                            <th>{{ __('message.heure_debut') }}</th>
                                                                            <th>{{ __('message.heure_fin') }}</th>
                                                                            <th>{{ __('message.salle') }}</th>
                                                                            <th>{{ __('message.Statuts') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        @foreach ($emargements as $emargement)
                                                                            <tr>
                                                                               <td>{{ $emargement->matiere->matiere_etude }}</td>
                                                                                <td>{{ $emargement->date }}</td>
                                                                                <td>{{ $emargement->heure_arrivee }}</td>
                                                                                <td>{{ $emargement->heure_depart }}</td>
                                                                                <td>{{ $emargement->salle->salle }}</td>

                                                                                <td>
                                                                                    @if ($emargement->status === 'present')
                                                                                        <span
                                                                                            class="badge badge-success m-r-5 m-b-5">PRESENT</span>
                                                                                    @endif
                                                                                    @if ($emargement->status === 'absent')
                                                                                        <span
                                                                                            class="badge badge-danger m-b-5">ABSENT</span>
                                                                                    @endif
                                                                                    @if ($emargement->status === 'retard')
                                                                                        <span
                                                                                            class="badge badge-warning m-r-5 m-b-5">RETARD</span>
                                                                                    @endif

                                                                                </td>

                                                                            </tr>
                                                                        @endforeach

                                                                    </tbody>
                                                                </table>
                                                                <div>
                                                                    {{ $emargements->links() }}
                                                                </div>
                                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .profile-social a {
                            font-size: 16px;
                            margin: 0 10px;
                            color: #999;
                        }

                        .profile-social a:hover {
                            color: #485b6f;
                        }

                        .profile-stat-count {
                            font-size: 22px
                        }
                    </style>

                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <<!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->

    </body>

    </html>
</div>
