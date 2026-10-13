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
                            <h6 class="m-0">Reçu de Préinscription</h6><small class="text-muted"><span
                                    id="date"></span></small>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-divider no-margin">
                                <li class="list-group-item" style="border-top-color:#e1eaec;"><strong>Matricule
                                    </strong>
                                    <span class=" float-right">{{ $preinscription->matricule }}</span>
                                </li>

                                <li class="list-group-item"><strong>Nom </strong>
                                    <span class=" float-right">{{ $preinscription->nom }}</span>
                                </li>
                                <li class="list-group-item"><strong>Prenom </strong>
                                    <span class="float-right">{{ $preinscription->prenom }}</span>
                                </li>

                                <li class="list-group-item"><strong>Date et lieu naissance: </strong>
                                    <span class=" float-right">{{ $preinscription->date_naissance }}
                                        {{ $preinscription->lieu_naissance }}</span>
                                </li>
                                <li class="list-group-item"><strong>Telephone : </strong>
                                    <span class="float-right">{{ $preinscription->telephone }}</span>
                                </li>
                            </ul>
                            <br>
                            <p><strong>NB : </strong> Presentez vous à la caisse pour confirmer l'inscription à la
                                formation souhaité</p>
                            @if (!empty(Permission('Comptabilite')))
                                <a href="{{ url('/inscription?matricule=' . urlencode($preinscription->matricule)) }}"
                                    class="btn btn-primary btn-block">
                                    <i class="fa fa-arrow-right"></i> Enchaîner l'inscription
                                </a>
                                <small class="text-muted d-block text-center mt-1">Le formulaire d'inscription s'ouvre
                                    avec ce matricule déjà rempli.</small>
                            @endif
                            <script>
                                document.getElementById("date").textContent = new Date().toLocaleDateString();
                            </script>
                            
                        </div>
                        <div class="card-footer ">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ url('/preinscription') }}" class="link-blue"><i class="fa fa-reply pull-rightx"></i> Retour</a>
                                @include('livewire.recu._imprimer', ['type' => 'preinscription', 'id' => $preinscription->id])
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
