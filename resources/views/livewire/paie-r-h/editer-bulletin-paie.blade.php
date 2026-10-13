<div>
    <body class="fixed-navbar">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')

            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="ibox">
                                <div class="ibox-head bg-danger text-white">
                                    <div class="ibox-title">BULLETIN DE PAIE – {{ $etatsPaies->first()->mois->mois ?? '' }} {{ $etatsPaies->first()->annee->annee ?? '' }}</div>
                                    <div class="ibox-tools bg-danger text-white">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-cogs"></i> Action<i
                                                    class="fa fa-angle-down"></i></button>
                                            <ul class="dropdown-menu text-white">
                                                    <li class="bg-primary text-white">
                                                        <a href="#" class="dropdown-item" role="button" tabindex="0"
                                                        wire:click.prevent="retour()">Retour</a>
                                                    </li>
                                                    <li class="bg-primary text-white">
                                                        <a href="#" class="dropdown-item" role="button" tabindex="0"
                                                        wire:click.prevent="printEtatPaie({{ $mois_id }})">Imprimer</a>
                                                    </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-body">
                                    @php
                                        $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');
                                        $dec = fn ($v) => $v === null ? '' : rtrim(rtrim(number_format((float) $v, 2, ',', ' '), '0'), ',');
                                    @endphp

                                    @foreach ($bulletins as $b)
                                        <div class="ibox">
                                            <div class="ibox-body">
                                                <button class="btn btn-sm btn-primary float-right" wire:loading.attr="disabled"
                                                    wire:click.prevent="imprimerBulletin({{ $b['fiche_id'] }})">
                                                    <i class="fa fa-file-pdf-o"></i> PDF</button>

                                                <div class="row">
                                                    @foreach (array_chunk($b['salarie'], 2) as $paire)
                                                        @foreach ($paire as $info)
                                                            <div class="col-md-6"><strong>{{ $info[0] }} :</strong> {{ $info[1] }}</div>
                                                        @endforeach
                                                    @endforeach
                                                </div>

                                                <div class="m-t-10 m-b-10">
                                                    <span class="m-r-20">Salaire brut : <strong>{{ $fmt($b['brut']) }}</strong></span>
                                                    <span>Base de cotisation : <strong>{{ $fmt($b['base_cotisation']) }}</strong></span>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="bg-secondary text-white">
                                                            <tr>
                                                                <th>Rubrique</th>
                                                                <th class="text-right">Base</th>
                                                                <th class="text-center">Taux</th>
                                                                <th class="text-center">Nombre</th>
                                                                <th class="text-right">Gains</th>
                                                                <th class="text-right">Retenues</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($b['gains'] as $l)
                                                                <tr>
                                                                    <td>{{ $l['libelle'] }}</td>
                                                                    <td class="text-right">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                                                                    <td class="text-center">{{ $dec($l['taux']) }}</td>
                                                                    <td class="text-center">{{ $dec($l['nombre']) }}</td>
                                                                    <td class="text-right text-success">{{ $fmt($l['montant']) }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach
                                                            @foreach ($b['retenues'] as $l)
                                                                <tr>
                                                                    <td>{{ $l['libelle'] }}</td>
                                                                    <td class="text-right">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                                                                    <td class="text-center">{{ $dec($l['taux']) }}</td>
                                                                    <td class="text-center">{{ $dec($l['nombre']) }}</td>
                                                                    <td></td>
                                                                    <td class="text-right text-danger">{{ $fmt($l['montant']) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            @if (empty($b['gains']) && empty($b['retenues']))
                                                                <tr><td colspan="6" class="text-center text-muted">Aucune rubrique sur ce bulletin. Relancez « Calculer » sur l'état de paie.</td></tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot class="font-weight-bold">
                                                            <tr>
                                                                <td colspan="4">TOTAUX</td>
                                                                <td class="text-right">{{ $fmt($b['total_gain']) }}</td>
                                                                <td class="text-right">{{ $fmt($b['total_retenu']) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <div class="text-right">
                                                    <h3 class="text-primary">{{ $fmt($b['net']) }} F CFA</h3>
                                                    <small class="text-muted">Net à payer</small>
                                                </div>
                                                @if (!empty($b['patronales']))
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Charges patronales <small class="text-muted">(à la charge de l'employeur, non déduites du net)</small></th>
                                                                    <th class="text-right">Base</th>
                                                                    <th class="text-center">Taux</th>
                                                                    <th class="text-right">Montant</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($b['patronales'] as $l)
                                                                    <tr>
                                                                        <td>{{ $l['libelle'] }}</td>
                                                                        <td class="text-right">{{ $l['base'] !== null ? $fmt($l['base']) : '' }}</td>
                                                                        <td class="text-center">{{ $dec($l['taux']) }}</td>
                                                                        <td class="text-right">{{ $fmt($l['montant']) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('components.layouts.footer')
            </div>
        </div>
        @include('components.layouts.loading')
    </body>
</div>
