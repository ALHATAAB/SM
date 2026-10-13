<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Licence - SmartSchool' }}</title>
    <link rel="icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <link rel="shortcut icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="{{ $bodyClass ?? 'bg-light' }}">
    {{ $slot }}
    @livewireScripts
</body>
</html>
