<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Base de données indisponible - SmartSchool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">🛑 Base de données indisponible</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">{{ $message }}</p>

                        <ul class="small text-muted mb-3">
                            @if ($type === 'base')
                                <li>Vérifiez que la base <code>{{ $database ?? '—' }}</code> existe bien sur le serveur.</li>
                                <li>Vérifiez les identifiants <code>DB_*</code> dans le fichier <code>.env</code>.</li>
                            @elseif ($type === 'tables')
                                <li>Les tables de l'application n'ont pas été créées.</li>
                                <li>Exécutez les migrations : <code>php artisan migrate</code>.</li>
                            @else
                                <li>Vérifiez que le serveur de base de données est démarré.</li>
                                <li>Vérifiez l'hôte et le port <code>DB_HOST</code> / <code>DB_PORT</code> dans le fichier <code>.env</code>.</li>
                            @endif
                        </ul>

                        @if (config('app.debug'))
                            <hr>
                            <p class="small text-muted mb-0"><strong>Détail technique :</strong><br>{{ $detail }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
