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
                            <h6 class="m-0">Reçu paiement_salaire</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <h3>
                                    <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Numero
                                            operation
                                        </strong>
                                        <span class=" float-right">{{ $paiement_salaire->id }}</span>
                                    </li>
                                </h3>

                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $paiement_salaire->matricule }}</span>
                                </li>

                                <li class="list-group-item"><strong>Nom </strong>
                                    <span
                                        class=" float-right">{{ $paiement_salaire->employer->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span
                                        class="float-right">{{ $paiement_salaire->employer->prenom }}</span>
                                </li>
                                <h2>
                                    {{-- <li class="list-group-item"><strong>Frais scolarite : </strong>
                                        <span
                                            class="float-right">{{ number_format($paiement_salaire->employer->frais_scolarite, 0, ',', ' ') }}</span>
                                    </li>
                                    <li class="list-group-item"><strong>Frais rester : </strong>
                                        <span
                                            class="float-right color-red">{{ number_format($paiement_salaire->employer->rester, 0, ',', ' ') }}</span>
                                    </li> --}}
                                    <li class="list-group-item "> Salaire payer : 
                                        <span
                                            class="float-right color-green">{{ number_format($paiement_salaire->salaire_payer, 0, ',', ' ') }}</span>
                                    </li>
                                    <li class="list-group-item">Mois de paie:
                                        <span
                                            class="float-right ">{{$paiement_salaire->mois->mois}} {{$paiement_salaire->annee->annee}}</span>
                                    </li>
                                </h2>
                                <li class="list-group-item"><strong>Mode paiement : </strong>
                                    <span class="float-right">{{ $paiement_salaire->mode_paiement }}</span>
                                </li>
                                @if($paiement_salaire->coordonnee_paiement!= NULL)
                            <li class="list-group-item">
                                <strong>Numero {{$paiement_salaire->mode_paiement }} :</strong>
                                <span>{{$paiement_salaire->coordonnee_paiement }} </span>
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
                                <a href="{{ url('reglement/paie') }}" class="link-blue"><i
                                        class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'salaire', 'id' => $paiement_salaire->id])
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
