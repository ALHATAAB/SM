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
                            <h6 class="m-0">Reçu abonnement</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $abonnement->matricule }}</span>
                                </li>

                                <li class="list-group-item"><strong>Nom </strong>
                                    <span class=" float-right">{{ $abonnement->preInscription->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span class="float-right">{{ $abonnement->preInscription->prenom }}</span>
                                </li>
                                {{-- <li class="list-group-item"><strong>Date et lieu naissance: </strong>
                                    <span class=" float-right">{{ $abonnement->preabonnement->date_naissance }}
                                        {{ $abonnement->lieu_naissance }}</span>
                                </li>
                                <li class="list-group-item"><strong>Telephone : </strong>
                                    <span class="float-right">{{ $abonnement->preabonnement->telephone }}</span>
                                </li> --}}
                                <li class="list-group-item"><strong>Type Abonnement : </strong>
                                    <span class="float-right">{{ $abonnement->type_abonnement->type_abonnement }}</span>
                                </li>
                                
                                <li class="list-group-item"><strong>Periode : </strong>
                                    <span class="float-right">{{ $abonnement->nombre }} {{ $abonnement->periode }}</span>
                                </li>
                                
                                <li class="list-group-item"><strong>Date debut </strong>
                                    <span class="float-right">{{ $abonnement->date_debut }}</span>
                                </li>
                                <li class="list-group-item"><strong>Date fin </strong>
                                    <span class="float-right">{{ $abonnement->date_fin }}</span>
                                </li>
                                <li class="list-group-item"><strong>Frais abonnement : </strong>
                                    <span class="float-right">{{ number_format($abonnement->frais_abonnement + $abonnement->remise , 0, ',', ' ') }}</span>
                                </li>
                                @if($abonnement->remise!=Null)
                                    <li class="list-group-item"><strong>Remise : </strong>
                                        <span class="float-right" >{{ number_format($abonnement->remise, 0, ',', ' ') }}</span>
                                    </li>
                                @endif
                                <li class="list-group-item"><strong>Frais payer : </strong>
                                    <span class="float-right">{{ number_format($abonnement->frais_abonnement , 0, ',', ' ') }}</span>
                                </li>
                                
                               <li class="list-group-item"><strong>Mode paiement : </strong>
                                <span class="float-right">{{ $abonnement->mode_paiement }}</span>
                            </li>
                            @if($abonnement->coordonnee_paiement!= NULL)
                            <li class="list-group-item">
                                <strong>Numero {{$abonnement->mode_paiement }} :</strong>
                                <span>{{$abonnement->coordonnee_paiement }} </span>
                            </li>
                            @endif
                            </ul>
                            <br>
                            <p><strong>NB : </strong> Aucun remboursement n'est possible.</p>
                            <script>
                                document.getElementById("date").textContent = new Date().toLocaleDateString();
                            </script>
                            
                        </div>
                        <div class="card-footer ">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ url('/nouvel/abonnement') }}" class="link-blue"><i class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'abonnement', 'id' => $abonnement->id])
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

