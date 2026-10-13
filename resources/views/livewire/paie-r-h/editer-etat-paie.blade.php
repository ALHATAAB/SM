<div>

    <body class="fixed-navbar  ">
        <div class="page-wrapper">
            <!-- START HEADER-->
            @include('components.layouts.header')
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            @include('components.layouts.sidebar')
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <!-- START PAGE CONTENT-->
                <div class="page-content fade-in-up">

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="ibox">
                                <div class="ibox-head ibox ibox-danger">
                                    <div class="ibox-title">{{ __('message.Etat_Paie') }}</div>
                                    <div class="ibox-tools">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-cogs"></i> Action<i
                                                    class="fa fa-angle-down"></i></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" class="dropdown-item" role="button"
                                                        tabindex="0" wire:click.prevent="retour()">Retour</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item" role="button"
                                                        tabindex="0"
                                                        wire:click.prevent="printEtatPaie({{ $mois_id }})">Imprimer</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-body">
                                    @if ($countEtatsPaie > 0)
                                        @php
                                            $fmt = fn ($v) => $v ? number_format((float) $v, 0, ',', ' ') : '';
                                            $nbG = count($etat['gains']);
                                            $nbR = count($etat['retenues']);
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">{{ __('message.Matricule') }}</th>
                                                        <th rowspan="2">{{ __('message.Nom') }}</th>
                                                        <th rowspan="2">{{ __('message.Prenom') }}</th>
                                                        <th rowspan="2">{{ __('message.Poste') }}</th>
                                                        @if ($nbG)
                                                            <th colspan="{{ $nbG }}" class="text-center bg-success text-white">GAINS</th>
                                                        @endif
                                                        <th rowspan="2" class="text-right">Total gain</th>
                                                        <th rowspan="2" class="text-right">Brut imposable</th>
                                                        <th rowspan="2" class="text-right">Brut non imposable</th>
                                                        @if ($nbR)
                                                            <th colspan="{{ $nbR }}" class="text-center bg-danger text-white">RETENUES</th>
                                                        @endif
                                                        <th rowspan="2" class="text-right">Total retenue</th>
                                                        <th rowspan="2" class="text-right">{{ __('message.Salaire_net') }}</th>
                                                        <th rowspan="2" class="text-right">CNSS patronale</th>
                                                        <th rowspan="2" class="text-right">ANPE patronale</th>
                                                        <th rowspan="2">{{ __('message.Statut') }}</th>
                                                    </tr>
                                                    <tr>
                                                        @foreach ($etat['gains'] as $libelle)
                                                            <th class="text-right">{{ $libelle }}</th>
                                                        @endforeach
                                                        @foreach ($etat['retenues'] as $libelle)
                                                            <th class="text-right">{{ $libelle }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($etat['fiches'] as $f)
                                                        @php $m = $etat['montants'][$f->employer_id] ?? ['g' => [], 'r' => []]; @endphp
                                                        <tr>
                                                            <td>{{ $f->employer->matricule }}</td>
                                                            <td>{{ $f->employer->nom }}</td>
                                                            <td>{{ $f->employer->prenom }}</td>
                                                            <td>{{ $f->employer->poste }}</td>
                                                            @foreach ($etat['gains'] as $rid => $libelle)
                                                                <td class="text-right">{{ $fmt($m['g'][$rid] ?? 0) }}</td>
                                                            @endforeach
                                                            <td class="text-right font-weight-bold">{{ number_format($f->total_gain, 0, ',', ' ') }}</td>
                                                            <td class="text-right">{{ $fmt($etat['repartitions'][$f->employer_id]['imposable'] ?? 0) }}</td>
                                                            <td class="text-right">{{ $fmt($etat['repartitions'][$f->employer_id]['non_imposable'] ?? 0) }}</td>
                                                            @foreach ($etat['retenues'] as $rid => $libelle)
                                                                <td class="text-right">{{ $fmt($m['r'][$rid] ?? 0) }}</td>
                                                            @endforeach
                                                            <td class="text-right font-weight-bold">{{ number_format($f->total_retenu, 0, ',', ' ') }}</td>
                                                            <td class="text-right font-weight-bold">{{ number_format($f->salaire_net, 0, ',', ' ') }}</td>
                                                            <td class="text-right">{{ $fmt($f->cnss_patronal) }}</td>
                                                            <td class="text-right">{{ $fmt($f->anpe_patronal) }}</td>
                                                            <td>{{ $f->statuts }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-secondary">
                                                    <tr>
                                                        <th colspan="4">TOTAL</th>
                                                        @foreach ($etat['gains'] as $rid => $libelle)
                                                            <th class="text-right">{{ $fmt($etat['totaux']['g'][$rid] ?? 0) }}</th>
                                                        @endforeach
                                                        <th class="text-right">{{ number_format($etat['totaux']['gain'], 0, ',', ' ') }}</th>
                                                        <th class="text-right">{{ $fmt($etat['totaux']['imposable']) }}</th>
                                                        <th class="text-right">{{ $fmt($etat['totaux']['non_imposable']) }}</th>
                                                        @foreach ($etat['retenues'] as $rid => $libelle)
                                                            <th class="text-right">{{ $fmt($etat['totaux']['r'][$rid] ?? 0) }}</th>
                                                        @endforeach
                                                        <th class="text-right">{{ number_format($etat['totaux']['retenu'], 0, ',', ' ') }}</th>
                                                        <th class="text-right">{{ number_format($etat['totaux']['net'], 0, ',', ' ') }}</th>
                                                        <th class="text-right">{{ $fmt($etat['totaux']['patronal']) }}</th>
                                                        <th class="text-right">{{ $fmt($etat['totaux']['anpe']) }}</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <h4 class="m-t-20">404</h4>
                                            <p class="error-title">PAGE NOT FOUND</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
        @include('components.layouts.loading')
        <!-- END PAGA BACKDROPS-->

    </body>
</div>
