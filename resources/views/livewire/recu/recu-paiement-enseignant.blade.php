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
                    <div class="card col-sm-4">
                        <div class="card-header">
                            <img class="img-circle m-r-10 pull-left"
                                src="{{ \App\Services\SocieteService::info()['logo_url'] }}" style="width:28px;" />
                            <h6 class="m-0">Reçu paiement_enseignant</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <h3>
                                    <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Numero
                                            operation
                                        </strong>
                                        <span class=" float-right">{{ $paiement_enseignant->id }}</span>
                                    </li>
                                </h3>

                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $paiement_enseignant->matricule }}</span>
                                </li>

                                <li class="list-group-item"><strong>Nom </strong>
                                    <span
                                        class=" float-right">{{ $paiement_enseignant->enseignant->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span
                                        class="float-right">{{ $paiement_enseignant->enseignant->prenom }}</span>
                                </li>
                                <h2>
                                    {{-- <li class="list-group-item"><strong>Frais scolarite : </strong>
                                        <span
                                            class="float-right">{{ number_format($paiement_enseignant->enseignant->frais_scolarite, 0, ',', ' ') }}</span>
                                    </li>
                                    <li class="list-group-item"><strong>Frais rester : </strong>
                                        <span
                                            class="float-right color-red">{{ number_format($paiement_enseignant->enseignant->rester, 0, ',', ' ') }}</span>
                                    </li> --}}
                                    <li class="list-group-item "> Salaire payer : 
                                        <span
                                            class="float-right color-green">{{ number_format($paiement_enseignant->salaire_payer, 0, ',', ' ') }}</span>
                                    </li>
                                    @if ($rubriques)
                                        @if ($rubriques['heures'] !== null)
                                            <li class="list-group-item">Volume horaire total :
                                                <span class="float-right">{{ $rubriques['heures'] }} H</span>
                                            </li>
                                        @endif
                                        @if ($rubriques['tarif'] > 0)
                                            <li class="list-group-item">Tarif horaire :
                                                <span class="float-right">{{ number_format($rubriques['tarif'], 0, ',', ' ') }} F</span>
                                            </li>
                                        @endif
                                        <li class="list-group-item"><strong>Total brut :</strong>
                                            <span class="float-right"><strong>{{ number_format($rubriques['brut'], 0, ',', ' ') }} F</strong></span>
                                        </li>
                                        <li class="list-group-item">Retenue / ITS ({{ $rubriques['taux'] }} %) :
                                            <span class="float-right">{{ number_format($rubriques['its'], 0, ',', ' ') }} F</span>
                                        </li>
                                        <li class="list-group-item">Autres retenues / Avance :
                                            <span class="float-right">{{ number_format($rubriques['autres'], 0, ',', ' ') }} F</span>
                                        </li>
                                    @endif
                                    <li class="list-group-item">Mois de paie:
                                        <span
                                            class="float-right ">{{$paiement_enseignant->mois->mois}} {{$paiement_enseignant->annee->annee}}</span>
                                    </li>
                                </h2>
                                <li class="list-group-item"><strong>Mode paiement : </strong>
                                    <span class="float-right">{{ $paiement_enseignant->mode_paiement }}</span>
                                </li>
                                @if($paiement_enseignant->coordonnee_paiement!= NULL)
                            <li class="list-group-item">
                                <strong>Numero {{$paiement_enseignant->mode_paiement }} :</strong>
                                <span>{{$paiement_enseignant->coordonnee_paiement }} </span>
                            </li>
                            @endif
                            </ul>
                            <br>
                            
                            <script>
                                document.getElementById("date").textContent = new Date().toLocaleDateString();
                            </script>

                        </div>
                        <div class="card-footer ">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ url('reglement/paie/enseignant') }}" class="link-blue"><i
                                        class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'enseignant', 'id' => $paiement_enseignant->id])
                            </div>
                        </div>
                    </div>
                    <br><br>
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
