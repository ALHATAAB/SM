<div>
    <body class="fixed-navbar">
        <div class="page-wrapper">
            <!-- START HEADER-->
            {{-- @include('components.layouts.header') --}}
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            {{-- @include('components.layouts.sidebar') --}}
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <!-- START PAGE CONTENT-->
                <div class="page-content fade-in-up">
<div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Contrat de licence SmartSchool</h5>
                        @if ($etat['active'])
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Invalide / Expirée</span>
                        @endif
                    </div>
                    <div class="card-body">

                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width:40%">Client</th>
                                    <td>{{ $etat['id_client'] ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Désignation</th>
                                    <td>{{ $etat['designation'] ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Période de validité</th>
                                    <td>
                                        @if (!empty($etat['date_debut']) && !empty($etat['date_fin']))
                                            Du {{ $etat['date_debut'] }} au {{ $etat['date_fin'] }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut actuel</th>
                                    <td>
                                        @if ($etat['active'])
                                            Active — expire dans {{ $etat['jours_restants'] }} jour(s)
                                        @else
                                            Invalide ou expirée
                                            @if (!empty($etat['date_fin']))
                                                (date de fin prévue : {{ $etat['date_fin'] }})
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Élèves inscrits autorisés</th>
                                    <td>{{ $etat['inscrits']['limite'] ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Utilisateurs autorisés</th>
                                    <td>{{ $etat['utilisateurs']['limite'] ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Prix de la licence</th>
                                    <td>
                                        @if (!empty($etat['prix_ventes']))
                                            {{ number_format($etat['prix_ventes'], 0, ',', ' ') }} FCFA
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr>

                        <h6>Conditions générales</h6>
                        <ol class="small text-muted">
                            <li>La présente licence autorise l'installation désignée ci-dessus à utiliser SmartSchool dans la limite du nombre d'élèves inscrits et d'utilisateurs indiqués ci-dessus, pour la période de validité mentionnée.</li>
                            <li>Toute utilisation au-delà des quotas autorisés (élèves inscrits, utilisateurs) doit faire l'objet d'un renouvellement ou d'une mise à niveau de la licence auprès du fournisseur.</li>
                            <li>À l'expiration de la période de validité, l'accès à l'application est automatiquement suspendu jusqu'à l'importation d'une nouvelle licence valide.</li>
                            <li>La clé d'activation associée à cette licence est strictement liée à l'identifiant client de cette installation et ne peut être transférée à une autre installation sans autorisation du fournisseur.</li>
                            <li>Pour toute question, renouvellement ou assistance technique, contactez Alhataab Digital (WhatsApp : +227 90 93 19 89 — Email : contact@alhataab.com).</li>
                        </ol>

                        <a href="{{ route('licence.formulaire') }}" class="btn btn-secondary btn-sm">
                            Retour à la gestion de la licence
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
                            Retour à la page d'accueil
                        </a>
                    </div>
                             
                </div>

            </div>
        </div>
                </div>
                <!-- END PAGE CONTENT-->
                <!-- START FOOTER-->
                @include('components.layouts.footer')
                <!-- END FOOTER-->
            </div>
        </div>
    </body>
</div>
