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
            <div class="col-md-7">

                @if (session('succes'))
                    <div class="alert alert-success">{{ session('succes') }}</div>
                @endif
                @if (session('erreur'))
                    <div class="alert alert-danger">{{ session('erreur') }}</div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">État actuel de la licence</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($etat['annee']))
                            <p><strong>Année scolaire :</strong> {{ $etat['annee'] }}</p>
                        @endif
                        <p>
                            <strong>Statut :</strong>
                            @if ($etat['active'])
                                <span class="badge badge-success">Active</span>
                                — expire dans {{ $etat['jours_restants'] }} jour(s) ({{ $etat['date_fin'] }})
                            @else
                                <span class="badge badge-danger">Invalide / Expirée</span>
                                @if (!empty($etat['date_fin']))
                                    (date de fin : {{ $etat['date_fin'] }})
                                @endif
                            @endif
                        </p>

                        <table class="table table-sm">
                            <thead>
                                <tr><th></th><th>Utilisés</th><th>Limite</th><th></th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Élèves inscrits</td>
                                    <td>{{ $etat['inscrits']['utilises'] }}</td>
                                    <td>{{ $etat['inscrits']['limite'] ?? '—' }}</td>
                                    <td>
                                        @if (!$etat['inscrits']['disponible'])
                                            <span class="badge badge-warning">Quota atteint</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Utilisateurs</td>
                                    <td>{{ $etat['utilisateurs']['utilises'] }}</td>
                                    <td>{{ $etat['utilisateurs']['limite'] ?? '—' }}</td>
                                    <td>
                                        @if (!$etat['utilisateurs']['disponible'])
                                            <span class="badge badge-warning">Quota atteint</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="small text-muted mb-0">
                            Seule l'expiration bloque l'accès à l'application. Le dépassement de quota
                            est affiché ici à titre indicatif pour le moment.
                        </p>
                    </div>
                </div>

                @if (!empty($etat['par_annee']))
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Quota par année scolaire</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Année</th><th>Inscrits</th><th>Utilisateurs max</th><th>Validité</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($etat['par_annee'] as $ligne)
                                        <tr>
                                            <td>
                                                {{ $ligne['annee'] }}
                                                @if ($ligne['en_cours'])
                                                    <span class="badge badge-primary">En cours</span>
                                                @endif
                                            </td>
                                            <td>{{ $ligne['utilises'] }} / {{ $ligne['limite'] }}</td>
                                            <td>{{ $ligne['utilisateurs'] }}</td>
                                            <td>{{ $ligne['date_debut'] }} → {{ $ligne['date_fin'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Importer la licence de l'année en cours</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit="importer">
                            <div class="mb-3">
                                <label class="font-weight-bold">Fichier de licence (.zip ou .xml reçu du fournisseur)</label>
                                <input type="file" wire:model="fichier_licence" class="form-control @error('fichier_licence') is-invalid @enderror" accept=".zip,.xml">
                                @error('fichier_licence')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div wire:loading wire:target="fichier_licence" class="small text-muted mt-1">Chargement du fichier...</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="fichier_licence,importer">
                                <span wire:loading.remove wire:target="importer">Importer et activer</span>
                                <span wire:loading wire:target="importer">Import en cours...</span>
                            </button>
                        </form>
                        <hr>
                     <div>
<a href="{{ route('login') }}" class="btn btn-primary w-100" >
                            Connexion login
                        </a>
                     </div>
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
