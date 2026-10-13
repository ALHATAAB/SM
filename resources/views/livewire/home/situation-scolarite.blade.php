@php
    $resumeFrais = [
        ['label' => 'Total_frais_scolarite', 'value' => $fraiScolarite ?? 0,        'color' => null],
        ['label' => 'Scolarite_remise',      'value' => $fraiScolariteRemise ?? 0,  'color' => 'red'],
        ['label' => 'Scolarite_payer',       'value' => $fraiScolaritePayer ?? 0,   'color' => 'green'],
        ['label' => 'Scolarite_impayer',     'value' => $fraiScolariteImpayer ?? 0, 'color' => 'red'],
    ];
@endphp

<div class="row">
    <div class="col-lg-9">
        <div class="ibox">
            <div class="ibox-head">
                <div class="ibox-title">{{ __('message.Situation_scolarite') }}</div>
                <div class="ibox-tools">
                    <form class="form-inline" wire:submit.prevent>
                        <label for="recherche">{{ __('message.Recherche') }}</label>
                        <input class="form-control mx-sm-2" id="recherche" type="text"
                               wire:model.live="recherche" placeholder="{{ __('message.Recherche') }}">
                    </form>
                </div>
            </div>
            <div class="ibox-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('message.Matricule') }}</th>
                                <th>{{ __('message.Nom_prenom') }}</th>
                                <th>{{ __('message.Niveau') }}</th>
                                <th>{{ __('message.Option') }}</th>
                                <th>{{ __('message.Frais_inscription') }}</th>
                                <th>{{ __('message.Frais_scolarite') }}</th>
                                <th>{{ __('message.Frais_remise') }}</th>
                                <th>{{ __('message.Frais_payer') }}</th>
                                <th>{{ __('message.Frais_restant') }}</th>
                                <th width="91px">{{ __('message.Annee') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($situationInscrits as $s)
                                <tr>
                                    <td>{{ $s->preInscription?->matricule }}</td>
                                    <td>{{ $s->preInscription?->nom }} {{ $s->preInscription?->prenom }}</td>
                                    <td>{{ $s->niveau?->niveau }}</td>
                                    <td>{{ $s->filiere?->filiere }}</td>
                                    <td>{{ number_format((float) ($s->frais_inscription ?? 0), 0, ',', ' ') }}</td>
                                    <td>{{ number_format((float) ($s->frais_scolarite ?? 0), 0, ',', ' ') }}</td>
                                    <td>{{ number_format((float) ($s->remise ?? 0), 0, ',', ' ') }}</td>
                                    <td>{{ number_format((float) ($s->payer ?? 0), 0, ',', ' ') }}</td>
                                    <td>{{ number_format((float) ($s->rester ?? 0), 0, ',', ' ') }}</td>
                                    <td><span class="badge badge-success">{{ $s->annee?->annee }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted">—</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>{{ $situationInscrits->links() }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="ibox">
            <div class="ibox-head">
                <div class="ibox-title">{{ __('message.Situation_frais_scolarite') }}</div>
            </div>
            <div class="ibox-body">
                <ul class="m-0 media-list media-list-divider">
                    @foreach ($resumeFrais as $ligne)
                        <li class="media">
                            <a class="media-img" href="javascript:;">
                                <img src="{{ url('') }}/assets/img/image.jpg" width="50" alt="">
                            </a>
                            <div class="media-body">
                                <div class="media-heading">
                                    <a href="javascript:;">{{ __('message.' . $ligne['label']) }}</a>
                                    <span class="float-right font-16" @if ($ligne['color']) style="color: {{ $ligne['color'] }}" @endif>
                                        {{ number_format((float) $ligne['value'], 0, ',', ' ') }}
                                    </span>
                                </div>
                                <div class="font-13">{{ __('message.Annee_scolaire') }} : {{ $anneeActive }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="text-center ibox-footer">
                <a href="javascript:;">{{ __('message.Tout_voir') }}</a>
            </div>
        </div>
    </div>
</div>
