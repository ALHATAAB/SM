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
                            <h6 class="m-0">Reçu paiement_scolarisation</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <h3>
                                    <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Numero
                                            operation
                                        </strong>
                                        <span class=" float-right">{{ $paiement_scolarisation->id }}</span>
                                    </li>
                                </h3>

                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $paiement_scolarisation->matricule }}</span>
                                </li>

                                <li class="list-group-item"><strong>Nom </strong>
                                    <span
                                        class=" float-right">{{ $paiement_scolarisation->inscription->preInscription->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span
                                        class="float-right">{{ $paiement_scolarisation->inscription->preInscription->prenom }}</span>
                                </li>


                                <li class="list-group-item"><strong>Niveau : </strong>
                                    <span
                                        class="float-right">{{ $paiement_scolarisation->inscription->niveau->niveau }}</span>
                                </li>
                                <li class="list-group-item"><strong>Filiere : </strong>
                                    <span
                                        class="float-right">{{ $paiement_scolarisation->inscription->filiere->filiere }}</span>
                                </li>
                                <li class="list-group-item"><strong>Classe affecter : </strong>
                                    <span
                                        class="float-right">{{ $paiement_scolarisation->inscription->classe->classe }}</span>
                                </li>
                                    <li class="list-group-item"><strong>Total frais de scolarité : </strong>
                                        <span class="float-right"><strong>{{ number_format($paiement_scolarisation->inscription->frais_scolarite, 0, ',', ' ') }}</strong></span>
                                    </li>
                                    {{-- <li class="list-group-item"><strong>Frais scolarite : </strong>
                                        <span
                                            class="float-right">{{ number_format($paiement_scolarisation->inscription->frais_scolarite, 0, ',', ' ') }}</span>
                                    </li>
                                    <li class="list-group-item"><strong>Frais rester : </strong>
                                        <span
                                            class="float-right color-red">{{ number_format($paiement_scolarisation->inscription->rester, 0, ',', ' ') }}</span>
                                    </li> --}}
                                    <li class="list-group-item"><strong>Frais payé : </strong>
                                        <span class="float-right" >{{ number_format($paiement_scolarisation->frais_payer, 0, ',', ' ') }}</span>
                                    </li>
                                    @if($paiement_scolarisation->remise!=Null)
                                    <li class="list-group-item"><strong>Remise : </strong>
                                        <span class="float-right" >{{ number_format($paiement_scolarisation->remise, 0, ',', ' ') }}</span>
                                    </li>
                                    @endif
                                <li class="list-group-item"><strong>Mode paiement : </strong>
                                    <span class="float-right">{{ $paiement_scolarisation->mode_paiement }}</span>
                                </li>
                                @if ($nomBanque)
                                    <li class="list-group-item"><strong>Banque : </strong>
                                        <span class="float-right">{{ $nomBanque }}</span>
                                    </li>
                                @endif
                                @if($paiement_scolarisation->coordonnee_paiement!= NULL)
                            <li class="list-group-item">
                                <strong>Numero {{$paiement_scolarisation->mode_paiement }} :</strong>
                                <span>{{$paiement_scolarisation->coordonnee_paiement }} </span>
                            </li>
                            @endif
                            @if ($totalPaye !== null)
                                <li class="list-group-item"><strong>Total payé : </strong>
                                    <span class="float-right"><strong>{{ number_format($totalPaye, 0, ',', ' ') }}</strong></span>
                                </li>
                            @endif
                            @if ($resteAPayer !== null)
                                <li class="list-group-item"><strong>Reste à payer : </strong>
                                    <span class="float-right color-red"><strong>{{ number_format($resteAPayer, 0, ',', ' ') }}</strong></span>
                                </li>
                            @endif
                            </ul>
                            <br>
                            <p><strong>NB : </strong> Aucun remboursement n'est possible.e</p>
                            <script>
                                document.getElementById("date").textContent = new Date().toLocaleDateString();
                            </script>

                        </div>
                        <div class="card-footer ">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ url('reglement/frais/scolaire') }}" class="link-blue"><i
                                        class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'scolarite', 'id' => $paiement_scolarisation->id])
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
