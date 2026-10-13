<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Licence expirée - SmartSchool</title>
    <link rel="icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <link rel="shortcut icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">🔒 Licence expirée ou invalide</h4>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-3">
                            L'accès à SmartSchool est bloqué car la licence de cette installation
                            est expirée, absente, ou invalide.
                        </p>
                        @if (!empty($etat['annee']))
                            <p class="small">Année scolaire en cours : <strong>{{ $etat['annee'] }}</strong>.
                                Chaque année scolaire a sa propre licence.</p>
                        @endif
                        @if (!empty($etat['designation']))
                            <p class="text-muted small">
                                Dernière licence connue : {{ $etat['designation'] }}
                                @if (!empty($etat['date_fin']))
                                    (expirée le {{ $etat['date_fin'] }})
                                @endif
                            </p>
                        @endif
                        <a href="{{ route('licence.formulaire') }}" class="btn btn-primary">
                            Importer une nouvelle licence
                        </a>
                       
                        <hr>
                        <p class="small text-muted mb-0">
                            Contactez Alhataab Digital pour renouveler votre licence :<br>
                            WhatsApp : +227 90 93 19 89 — Email : contact@alhataab.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
