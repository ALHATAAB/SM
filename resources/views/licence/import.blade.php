<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion de la licence - SmartSchool</title>
    <link rel="icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <link rel="shortcut icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
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
                                <span class="badge bg-success">Active</span>
                                — expire dans {{ $etat['jours_restants'] }} jour(s) ({{ $etat['date_fin'] }})
                            @else
                                <span class="badge bg-danger">Invalide / Expirée</span>
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
                                            <span class="badge bg-warning text-dark">Quota atteint</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Utilisateurs</td>
                                    <td>{{ $etat['utilisateurs']['utilises'] }}</td>
                                    <td>{{ $etat['utilisateurs']['limite'] ?? '—' }}</td>
                                    <td>
                                        @if (!$etat['utilisateurs']['disponible'])
                                            <span class="badge bg-warning text-dark">Quota atteint</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
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
                                                    <span class="badge bg-primary">En cours</span>
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
                        <form method="POST" action="{{ route('licence.importer') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Fichier de licence (.zip ou .xml reçu du fournisseur)</label>
                                <input type="file" name="fichier_licence" class="form-control" accept=".zip,.xml" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Importer et activer</button>
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
</body>
</html>
