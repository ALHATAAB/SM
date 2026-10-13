<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'SMARTSCHOOL' }}</title>
    <link rel="icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">
    <link rel="shortcut icon" href="{{ \App\Services\SocieteService::info()['logo_url'] }}">

    <!-- GLOBAL MAINLY STYLES-->
    <link href="{{ url('') }}/assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ url('') }}/assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="{{ url('') }}/assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- PLUGINS STYLES-->
    <link href="{{ url('') }}/assets/vendors/jvectormap/jquery-jvectormap-2.0.3.css" rel="stylesheet" />
    <!-- THEME STYLES-->
    <link href="{{ url('') }}/assets/css/main.css" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->
    <link href="{{ url('') }}/assets/css/pages/auth-light.css" rel="stylesheet" />
    <link href="{{ url('') }}/assets/css/main.min.css" rel="stylesheet" />
    <!-- MODERN UI (doit rester en dernier pour surcharger le thème) -->
    <link href="{{ url('') }}/assets/css/modern.css?v={{ @filemtime(public_path('assets/css/modern.css')) }}" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->
    @livewireStyles
</head>
<body>
    {{ $slot }}

    <!-- PAGE LEVEL PLUGINS -->
    <!-- PAGE LEVEL SCRIPTS-->
    <!-- CORE PLUGINS-->
    <script src="{{ url('') }}/assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
    <script src="{{ url('') }}/assets/vendors/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/chart.js/dist/chart.umd.js?v=4.4.9" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <script src="{{ url('') }}/assets/vendors/jvectormap/jquery-jvectormap-us-aea-en.js" type="text/javascript"></script>

    <!-- CORE SCRIPTS-->
    <script src="{{ url('') }}/assets/js/app.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $('#login-form').validate({
                errorClass: "help-block",
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                },
                highlight: function(e) {
                    $(e).closest(".form-group").addClass("has-error")
                },
                unhighlight: function(e) {
                    $(e).closest(".form-group").removeClass("has-error")
                },
            });
        });
    </script>
    @livewireScripts
</body>
</html>
