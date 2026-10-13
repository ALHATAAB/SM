<header class="header">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        {{-- Toggle sidebar + Logo + Nom --}}
        <div class="d-flex align-items-center">
            <a class="sidebar-toggler js-sidebar-toggler" role="button" aria-label="Menu">
                <i class="ti-menu"></i>
            </a>
            <a href="{{ url('home') }}" class="sm-brand d-flex align-items-center text-decoration-none">
                <img src="{{ \App\Services\SocieteService::info()['logo_url'] }}" alt="Logo">
                <span class="sm-brand-name">{{ __('menu.Smart_School') }}</span>
            </a>
        </div>

        {{-- Toolbar --}}
        <div class="d-flex align-items-center">

            {{-- Année scolaire --}}
            @if (!empty(AnneeScolaire()))
                <span class="sm-year-pill">
                    <i class="fa fa-calendar"></i>
                    <span>{{ __('message.Annee_scolaire') }} :
                        <strong>{{ date('Y', strtotime(AnneeScolaire()->date_debut)) . ' - ' . date('Y', strtotime(AnneeScolaire()->date_fin)) }}</strong>
                    </span>
                </span>
            @endif

            {{-- Sélecteur de langue --}}
            @php($langue = [
                'fr' => 'Français',
                'en' => 'English',
                'ar' => 'العربية',
            ])
            <div class="dropdown mr-3">
                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdown-menu"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-globe mr-1"></i> {{ $langue[Session::get('locale', 'fr')] ?? 'Français' }}
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'fr']) }}">Français</a>
                    <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'en']) }}">English</a>
                    <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'ar']) }}">العربية</a>
                </div>
            </div>

            {{-- Déconnexion --}}
            <a class="btn btn-danger btn-sm" href="{{ route('logout') }}" title="Déconnexion">
                <i class="fa fa-power-off"></i>
            </a>
        </div>
    </div>
</header>
