<div>

    <body class="fixed-navbar">
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
                    @if (!empty(Permission('Programme')))
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('menu.Bulletin_scolaire') }}</div>
                            </div>
                            @include('livewire.alert.message')

                            @if (!$anneeActive)
                                <div class="ibox-body">
                                    <div class="alert alert-warning">Pas d'année scolaire active.</div>
                                </div>
                            @else
                                @if (count($trimestres) === 0)
                                    <div class="alert alert-warning m-l-15 m-r-15">
                                        Aucun trimestre n'est défini pour l'année scolaire active : créez-les dans le menu
                                        « Trimestre », seule la synthèse « Année complète » est possible sans trimestre.
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="ibox">
                                            <div class="ibox-body" style="background-color:darkgray; color:azure">
                                                <p><strong><i class="ti ti-filter"></i> Filtre</strong></p>
                                                <div class="form-group">
                                                    <label>Classe</label>
                                                    <select class="form-control" wire:model.live="classe_id">
                                                        <option value=""></option>
                                                        @foreach ($classes as $classe)
                                                            <option value="{{ $classe->id }}">{{ $classe->classe }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Période</label>
                                                    <select class="form-control" wire:model.live="periode">
                                                        <option value=""></option>
                                                        @foreach ($trimestres as $trimestre)
                                                            <option value="{{ $trimestre->id }}">{{ $trimestre->trimestre }}</option>
                                                        @endforeach
                                                        <option value="annuel">Année complète</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="ibox">
                                            <div class="ibox-body">
                                                @if ($resultat === null)
                                                    <div class="alert alert-info">Choisissez une classe et une période.</div>
                                                @elseif (empty($resultat['eleves']))
                                                    <div class="alert alert-warning">Aucun élève inscrit dans cette classe.</div>
                                                @elseif (empty($resultat['matieres']))
                                                    <div class="alert alert-warning">
                                                        Aucune matière avec coefficient pour cette classe.
                                                        Renseignez-les dans « Coefficient matière ».
                                                    </div>
                                                @else
                                                    @if (($resultat['stats']['notes'] ?? 0) === 0)
                                                        <div class="alert alert-info">
                                                            Aucune note saisie pour cette période : les bulletins seront vides.
                                                            Saisissez-les dans « Note apprenant par classe ».
                                                        </div>
                                                    @endif
                                                    <div class="row m-b-15">
                                                        <div class="col-md-8">
                                                            <span class="badge badge-primary">Effectif : {{ $resultat['stats']['effectif'] }}</span>
                                                            <span class="badge badge-info">Moyenne de la classe : {{ $resultat['stats']['moyenne'] !== null ? number_format($resultat['stats']['moyenne'], 2, ',', '') : '-' }}</span>
                                                            <span class="badge badge-success">Moyenne atteinte : {{ $resultat['stats']['admis'] }}</span>
                                                        </div>
                                                        <div class="col-md-4 text-right">
                                                            <a class="btn btn-secondary" href="{{ $this->urlImpression() }}" target="_blank" rel="noopener">
                                                                <i class="fa fa-print"></i> Imprimer tous les bulletins
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Rang</th>
                                                                    <th>Matricule</th>
                                                                    <th>Nom et prénom</th>
                                                                    <th>Total points</th>
                                                                    <th>Moyenne</th>
                                                                    <th>Appréciation</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach (collect($resultat['eleves'])->sortBy(fn ($e) => $e['rang'] ?? PHP_INT_MAX) as $eleve)
                                                                    <tr>
                                                                        <td>{{ $eleve['rang'] === null ? '-' : ($eleve['rang'] === 1 ? '1er' : $eleve['rang'] . 'e') }}</td>
                                                                        <td>{{ $eleve['matricule'] }}</td>
                                                                        <td>{{ $eleve['nom'] }} {{ $eleve['prenom'] }}</td>
                                                                        <td>{{ number_format($eleve['total_points'], 2, ',', '') }}</td>
                                                                        <td>
                                                                            @if ($eleve['moyenne'] === null)
                                                                                <span class="text-muted">Pas de note</span>
                                                                            @else
                                                                                <strong class="{{ $eleve['admis'] ? 'text-success' : 'text-danger' }}">
                                                                                    {{ number_format($eleve['moyenne'], 2, ',', '') }}
                                                                                </strong>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $eleve['appreciation'] ?? '-' }}</td>
                                                                        <td class="text-right">
                                                                            <a class="btn btn-sm btn-primary"
                                                                                href="{{ $this->urlImpression($eleve['inscription_id']) }}"
                                                                                target="_blank" rel="noopener">
                                                                                <i class="fa fa-print"></i> Bulletin
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="ibox ibox-primary">
                            <div class="ibox-body text-center">
                                <h1 class="m-t-20">404</h1>
                                <p class="error-title">PAGE NOT FOUND</p>
                            </div>
                        </div>
                    @endif
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
        </div> {{-- fin .page-wrapper --}}
    </body>
</div>
