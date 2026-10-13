{{-- Carte réutilisable. Paramètres : color, value, label, icon, [trend = up|down], [format = true|false] --}}
<div class="col-lg-3 col-md-6">
    <div class="ibox bg-{{ $color }} color-white widget-stat">
        <div class="ibox-body">
            <h2 class="m-b-5 font-strong">
                {{ ($format ?? true) ? number_format((float) ($value ?? 0), 0, ',', ' ') : ($value ?? 0) }}
            </h2>
            <div class="m-b-5">{{ __('message.' . $label) }}</div>
            <i class="{{ $icon }} widget-stat-icon"></i>
            <div>
                @if (!empty($trend))
                    <i class="fa fa-level-{{ $trend }} m-r-5"></i>
                    <small>{{ __('message.Annee_scolaire') }} : {{ $anneeActive }}</small>
                @else
                    <i class="ti-eye m-r-5"></i><small>{{ __('message.Detail') }}</small>
                @endif
            </div>
        </div>
    </div>
</div>
