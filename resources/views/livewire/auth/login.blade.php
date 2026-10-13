<div class="sm-login">
    <div style="width:100%;max-width:440px;">
        <div class="sm-login-card">

            {{-- Logo --}}
            <div class="sm-login-logo">
                <img src="{{ \App\Services\SocieteService::info()['logo_url'] }}" alt="Logo" class="img-fluid">
            </div>
            <h1 class="sm-login-title">{{ __('menu.Smart_School') }}</h1>
            <p class="sm-login-sub">{{ __('message.Connexion') }}</p>

            {{-- Formulaire --}}
            <form id="login-form" wire:submit.prevent="auth" method="post" autocomplete="on">
                @csrf

                @include('livewire.alert.message')

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">{{ __('message.Email') }}</label>
                    <div class="sm-field">
                        <i class="fa fa-envelope-o"></i>
                        <input id="email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" wire:model.defer="email"
                            placeholder="ex: nom@domaine.com" required autocomplete="username">
                    </div>
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="form-group mb-4">
                    <label for="password">{{ __('message.Password') }}</label>
                    <div class="sm-field">
                        <i class="fa fa-lock"></i>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            wire:model.defer="password" placeholder="••••••••" required
                            autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Bouton --}}
                <button class="btn btn-login btn-block" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('message.Connexion') }}</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm"></span>
                        {{ __('message.Connexion') }}...
                    </span>
                </button>
            </form>

            {{-- Sélecteur de langue --}}
            @php
                $labels = [
                    'fr' => 'Langue : Français',
                    'en' => 'Language : English',
                    'ar' => 'اللغة : العربية',
                ];
                $locale = Session::get('locale', 'fr');
            @endphp
            <div class="sm-lang">
                <div class="text-muted small mb-2">{{ __('message.Changer_langue') }}</div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle px-4" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-globe mr-1"></i> {{ $labels[$locale] ?? $labels['fr'] }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'fr']) }}">Français</a>
                        <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'en']) }}">English</a>
                        <a class="dropdown-item" href="{{ route('change.lang', ['lang' => 'ar']) }}">العربية</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sm-login-foot">© 2026 Smart School · Alhataab Digital</div>
    </div>
</div>
