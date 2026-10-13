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

                        <div class="col-lg-4 col-md-4">
                            <div class="ibox">
                                <div class="ibox-body text-center">
                                    @livewire('photo-personne', ['type' => 'apprenant', 'personneId' => $inscrit->pre_inscription_id], key('photo-apprenant-' . $inscrit->pre_inscription_id))
                                    {{-- <div class="m-t-20">
                                        <img class="img-circle" src="{{ url('') }}/assets/img/users/u3.jpg" />
                                    </div> --}}
                                    <h5 class="font-strong m-b-10 m-t-10">
                                        {{ $inscrit->preInscription->nom . ' ' . $inscrit->preInscription->prenom }}
                                    </h5>
                                    <div class="m-b-20 text-muted">Matricule : {{ $inscrit->preInscription->matricule }}
                                    </div>
                                    <a href="{{ url('/fiche/apprenant/' . encrypt($inscrit->id)) }}" target="_blank" class="btn btn-warning btn-sm m-b-10" title="Fiche complète (PDF)"> Fiche complète
                                        <i class="ti ti-printer"></i>
                                    </a>

                                </div>
                            </div>
                            <div class="ibox">
                                <div class="ibox-body">
                                    <div class="row text-center m-b-20">
                                        <div class="col-4">
                                            <div class="font-18 profile-stat-count">
                                                {{ number_format($somme_frais_inscription, 0, ',', ' ') }} </div>
                                            <div class="text-muted"> inscription </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="font-18 profile-stat-count color-green">
                                                {{ number_format($somme_frais_scolarite, 0, ',', ' ') }}
                                            </div>
                                            <div class="text-muted"> scolarite </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="font-18 profile-stat-count color-red">
                                                {{ number_format($somme_frais_scolarite + $somme_frais_inscription, 0, ',', ' ') }}
                                            </div>
                                            <div class="text-muted"> Total </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox">
                                <div class="ibox-body">
                                    <ul class="list-group list-group-full list-group-divider">
                                        <li class="list-group-item">Date naissance
                                            <span
                                                class="pull-right ">{{ $inscrit->preInscription->date_naissance }}</span>
                                        </li>
                                        <li class="list-group-item">Lieu naissance
                                            <span
                                                class="pull-right ">{{ $inscrit->preInscription->lieu_naissance }}</span>
                                        </li>
                                        <li class="list-group-item">Adresse
                                            <span class="pull-right ">{{ $inscrit->preInscription->adresse }}</span>
                                        </li>
                                        <li class="list-group-item">Telephone
                                            <span class="pull-right ">{{ $inscrit->preInscription->telephone }}</span>
                                        </li>
                                        <li class="list-group-item">Groupe
                                            <span class="pull-right ">{{ $inscrit->preInscription->groupe }}</span>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <div class="ibox">
                                @include('livewire.alert.message')
                                <div class="ibox-body">
                                    <ul class="nav nav-tabs tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#tab-1" data-toggle="tab"><i
                                                    class="ti-user"></i> Information générale</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-2" data-toggle="tab"><i
                                                    class="ti-editer"></i> Modification</a>
                                        </li>
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" href="#tab-3" data-toggle="tab"><i
                                                    class="ti-panel"></i> Historique paiement</a>
                                        </li> --}}
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="tab-1">
                                            <div class="row">
                                                <div class="col-md-6" style="border-right: 1px solid #eee;">
                                                    <h5 class="text-info m-b-20 m-t-10"><i class="fa fa-bar-chart"></i>
                                                        Historique Paiement frais scolarité </h5>
                                                    <ul class="list-group list-group-full list-group-divider">
                                                        @foreach ($historiquePaiements as $historiquePaiement)
                                                            <li class="media">
                                                                {{-- <a class="media-img" href="javascript:;">
                                                                <img class="img-circle" src="{{url('')}}/assets/img/users/u1.jpg"
                                                                    width="40" />
                                                            </a> --}}
                                                                <div class="media-body">
                                                                    <div class="media-heading ">
                                                                        <span class="color-green">
                                                                            {{ number_format($historiquePaiement->frais_payer, 0, ',', ' ') }}
                                                                        </span>
                                                                        <small
                                                                            class="float-right text-muted">{{ $historiquePaiement->created_at }}</small>
                                                                    </div>
                                                                    <div class="font-13">
                                                                        {{ 'OPE n° ' . $historiquePaiement->id }}
                                                                        du {{ $historiquePaiement->description }}</div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="text-info m-b-20 m-t-10"><i class="fa fa-user-plus"></i>
                                                        Historique Abonnement</h5>
                                                    <ul class="media-list media-list-divider m-0">
                                                        @foreach ($historiqueAbonnements as $historiqueAbonnement)
                                                            <li class="media">
                                                                {{-- <a class="media-img" href="javascript:;">
                                                                <img class="img-circle" src="{{url('')}}/assets/img/users/u1.jpg"
                                                                    width="40" />
                                                            </a> --}}
                                                                <div class="media-body">
                                                                    <div class="media-heading ">
                                                                        Abonnement
                                                                        {{ $historiqueAbonnement->type_abonnement->type_abonnement }}
                                                                        @php
                                                                            $dateFin = Carbon\Carbon::parse(
                                                                                $historiqueAbonnement->date_fin,
                                                                            );
                                                                            $aujourdhui = Carbon\Carbon::now();
                                                                        @endphp
                                                                        @if ($aujourdhui->gt($dateFin))
                                                                            <span class="color-red">Expirer.</span>
                                                                        @else
                                                                            <span class="color-green">En cours.</span>
                                                                        @endif
                                                                        <br>
                                                                        <span class="color-green">
                                                                            {{ number_format($historiqueAbonnement->frais_abonnement, 0, ',', ' ') }}
                                                                        </span>
                                                                        <small
                                                                            class="float-right text-muted">{{ $historiqueAbonnement->created_at }}</small>
                                                                    </div>
                                                                    <div class="font-13">
                                                                        Abonnement :
                                                                        {{ $historiqueAbonnement->type_abonnement->type_abonnement }}
                                                                        de
                                                                        {{ $historiqueAbonnement->nombre . ' ' . $historiqueAbonnement->periode }}
                                                                        allant du
                                                                        {{ $historiqueAbonnement->date_debut . ' ' . $historiqueAbonnement->date_fin }}
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach

                                                    </ul>
                                                </div>
                                            </div>
                                            <h5 class="text-info m-b-20 m-t-20"><i class="fa fa-shopping-basket"></i>
                                                Historique Inscription</h5>
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="91px">Annee</th>
                                                        <th>Niveau</th>
                                                        <th>Filiere</th>
                                                        <th>Frais insctiption payé</th>
                                                        <th>Frais scolarité payé</th>
                                                        <th>Frais scolarité restant</th>
                                                        <th>Frais scolarité remise</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($inscriptions as $inscription)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge badge-warning">{{ $inscription->annee->annee }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $inscription->niveau->niveau }}</td>
                                                            <td>{{ $inscription->filiere->filiere }}</td>
                                                            <td>
                                                                {{ number_format($inscription->frais_inscription, 0, ',', ' ') }}

                                                            </td>
                                                            <td>
                                                                <span class="color-green">
                                                                    {{ number_format($inscription->payer, 0, ',', ' ') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($inscription->rester == 0 && $inscription->payer == 0)
                                                                    <span class="color-red">
                                                                        {{ number_format($inscription->frais_scolarite, 0, ',', ' ') }}
                                                                    </span>
                                                                @else
                                                                    <span class="color-red">
                                                                        {{ number_format($inscription->rester, 0, ',', ' ') }}
                                                                    </span>
                                                                @endif

                                                            </td>
                                                             <td>
                                                                <span class="color-red">
                                                                        {{ number_format($inscription->remise ?? 0, 0, ',', ' ') }}
                                                                    </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="tab-2">
                                            <div class="ibox ibox-primary">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">{{ __('message.Identite') }}</div>
                                                </div>

                                                <form wire:submit='editPreInscription'>
                                                    <div class="ibox-body">

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Nom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='nom'>
                                                                <div>
                                                                    @error('nom')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Prenom') }} </label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='prenom'>
                                                                <div>
                                                                    @error('prenom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Sexe') }} </label>
                                                                <select class="form-control" wire:model="sexe">
                                                                    <option value=""></option>
                                                                    <option value="Masculin">Masculin</option>
                                                                    <option value="Feminin">Feminin</option>
                                                                </select>
                                                                <div>
                                                                    @error('sexe')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Groupe') }} </label>
                                                                <select class="form-control" wire:model="groupe">
                                                                    <option value=""></option>
                                                                    <option value="Homme">Homme</option>
                                                                    <option value="Femme">Femme</option>
                                                                    <option value="Enfant">Enfant</option>
                                                                </select>
                                                                <div>
                                                                    @error('groupe')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="ibox">
                                                                <div class="ibox-head">
                                                                    <div class="ibox-title">
                                                                        {{ __('message.Information_personnel') }}</div>
                                                                    <div class="ibox-tools">
                                                                        {{-- <a class="ibox-collapse"><i class="fa fa-minus"></i></a> --}}
                                                                        {{-- <a class="dropdown-toggle" data-toggle="dropdown"><i
                                                            class="fa fa-ellipsis-v"></i></a> --}}
                                                                        {{-- <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item">option 1</a>
                                                        <a class="dropdown-item">option 2</a>
                                                    </div> --}}
                                                                    </div>
                                                                </div>
                                                                <div class="ibox-body">
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
                                                                        <label>{{ __('message.Telephone') }}</label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model='telephone'>
                                                                        <div>
                                                                            @error('telephone')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('message.Adresse') }}</label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model='adresse'>
                                                                        <div>
                                                                            @error('adresse')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="ibox">
                                                                <div class="ibox-head">
                                                                    <div class="ibox-title">
                                                                        {{ __('message.Information_tuteur') }}</div>
                                                                    <div class="ibox-tools">
                                                                        {{-- <a class="ibox-collapse"><i class="fa fa-minus"></i></a> --}}
                                                                        <a class="fullscreen-link"><i
                                                                                class="fa fa-expand"></i></a>
                                                                    </div>
                                                                </div>
                                                                <div class="ibox-body">
                                                                    <div class="form-group ">
                                                                        <label>{{ __('message.Nom_prenom_tuteur') }}</label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model='prenom_parent'>
                                                                        <div>
                                                                            @error('prenom_parent')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label>{{ __('message.Telephone') }}</label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model='telephone_parent'>
                                                                        <div>
                                                                            @error('telephone_parent')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label>{{ __('message.Adresse') }}</label>
                                                                        <input class="form-control" type="text"
                                                                            wire:model='adresse_parent'>
                                                                        <div>
                                                                            @error('adresse_parent')
                                                                                <span
                                                                                    class="text-error">{{ $message }}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="ibox-body">
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <div class="col-md-6">
                                                                    @if (!empty(Permission('Modifier')))
                                                                        <button class="btn btn-info"
                                                                            type="submit">{{ __('message.Modifier') }}</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab-3">
                                            <h5 class="text-info m-b-20 m-t-20"><i class="fa fa-bullhorn"></i>
                                                Historique paiement</h5>
                                            <ul class="media-list media-list-divider m-0">
                                                <li class="media">
                                                    <div class="media-img"><i class="ti-user font-18 text-muted"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="media-heading">New customer <small
                                                                class="float-right text-muted">12:05</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                                <li class="media">
                                                    <div class="media-img"><i
                                                            class="ti-info-alt font-18 text-muted"></i></div>
                                                    <div class="media-body">
                                                        <div class="media-heading text-warning">Server Warning <small
                                                                class="float-right text-muted">12:05</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                                <li class="media">
                                                    <div class="media-img"><i
                                                            class="ti-announcement font-18 text-muted"></i></div>
                                                    <div class="media-body">
                                                        <div class="media-heading">7 new feedback <small
                                                                class="float-right text-muted">Today</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                                <li class="media">
                                                    <div class="media-img"><i class="ti-check font-18 text-muted"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="media-heading text-success">Issue fixed <small
                                                                class="float-right text-muted">12:05</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                                <li class="media">
                                                    <div class="media-img"><i
                                                            class="ti-shopping-cart font-18 text-muted"></i></div>
                                                    <div class="media-body">
                                                        <div class="media-heading">7 New orders <small
                                                                class="float-right text-muted">12:05</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                                <li class="media">
                                                    <div class="media-img"><i
                                                            class="ti-reload font-18 text-muted"></i></div>
                                                    <div class="media-body">
                                                        <div class="media-heading text-danger">Server warning <small
                                                                class="float-right text-muted">12:05</small></div>
                                                        <div class="font-13">Lorem Ipsum is simply dummy text.</div>
                                                    </div>
                                                </li>
                                            </ul>
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
