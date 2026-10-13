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
                            <h6 class="m-0">Reçu inscription</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $inscription->matricule }}</span>
                                </li>

                                {{-- <li class="list-group-item"><strong>Nom </strong>
                                    <span class=" float-right">{{ $inscription->preInscription->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span class="float-right">{{ $inscription->preInscription->prenom }}</span>
                                </li>

                                <li class="list-group-item"><strong>Date et lieu naissance: </strong>
                                    <span class=" float-right">{{ $inscription->preInscription->date_naissance }}
                                        {{ $inscription->lieu_naissance }}</span>
                                </li>
                                <li class="list-group-item"><strong>Telephone : </strong>
                                    <span class="float-right">{{ $inscription->preInscription->telephone }}</span>
                                </li> --}}
                                <li class="list-group-item"><strong>Niveau : </strong>
                                    <span class="float-right">{{ $inscription->niveau->niveau }}</span>
                                </li>
                                <li class="list-group-item"><strong>Filiere : </strong>
                                    <span class="float-right">{{ $inscription->filiere->filiere }}</span>
                                </li>
                                <li class="list-group-item"><strong>Classe affecter : </strong>
                                    <span class="float-right">{{ $inscription->classe->classe }}</span>
                                </li>
                                <li class="list-group-item"><strong>Frais inscription : </strong>
                                    <span class="float-right">{{ number_format($inscription->frais_inscription , 0, ',', ' ') }}</span>
                                </li>
                               <li class="list-group-item"><strong>Mode paiement : </strong>
                                <span class="float-right">{{ $inscription->mode_paiement }}</span>
                            </li>
                            @if($inscription->coordonnee_paiement!= NULL)
                            <li class="list-group-item">
                                <strong>Numero {{$inscription->mode_paiement }} :</strong>
                                <span>{{$inscription->coordonnee_paiement }} </span>
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
                                <a href="{{ url('/inscription') }}" class="link-blue"><i class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'inscription', 'id' => $inscription->id])
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
