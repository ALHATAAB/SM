<div>
    <body class="fixed-navbar">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')

            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    <div class="ibox ibox-primary">
                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ $titre }}</div>
                        </div>

                        <div class="ibox-body">
                            <span><strong><i class="ti ti-filter"></i> {{ __('message.Filtre') }}</strong></span>

                            <form class="form-inline mt-2" wire:submit.prevent>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" type="text"
                                    wire:model.live.debounce.300ms="recherche" placeholder="{{ __('message.Recherche') }}">

                                @if ($categorie === 'scolaire')
                                    <select class="mb-2 form-control mr-sm-2 mb-sm-0" wire:model.live="classe">
                                        <option value="">Toutes les classes</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->classe }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="mb-2 form-control mr-sm-2 mb-sm-0" wire:model.live="population">
                                        @foreach ($populations as $cle => $libelle)
                                            <option value="{{ $cle }}">{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                <select class="mb-2 form-control mr-sm-2 mb-sm-0" wire:model.live="filtre">
                                    <option value="">Toutes</option>
                                    <option value="sans">Sans carte valide</option>
                                    <option value="avec">Avec carte valide</option>
                                </select>
                                <div wire:loading class="small text-muted">{{ __('message.Chargement') }}…</div>
                            </form>

                            <div class="mt-3">
                                @include('livewire.alert.message')
                                @unless ($anneeActive)
                                    <div class="alert alert-warning" role="alert">{{ __('message.Aucune_annee_active') }}</div>
                                @endunless
                            </div>

                            @if ($anneeActive)
                                <div class="mb-2">
                                    @if ($peutAjouter)
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="genererManquantes"
                                            wire:confirm="Générer les cartes manquantes pour la sélection actuelle ?">
                                            <i class="ti ti-id-badge"></i> Générer les cartes manquantes
                                        </button>
                                    @endif
                                    <a class="btn btn-warning btn-sm" target="_blank"
                                        href="{{ url('carte/lot/' . $categorie) . '?' . http_build_query(array_filter(['classe' => $classe, 'population' => $population, 'recherche' => $recherche])) }}">
                                        <i class="ti ti-printer"></i> Imprimer les cartes valides de la sélection
                                    </a>
                                </div>
                            @endif

                            <div class="table-responsive mt-2">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>{{ __('message.Matricule') }}</th>
                                            <th>{{ __('message.Nom') }}</th>
                                            <th>{{ __('message.Prenom') }}</th>
                                            <th>{{ $categorie === 'scolaire' ? 'Classe' : 'Fonction' }}</th>
                                            <th>N° carte</th>
                                            <th>Statut</th>
                                            <th>Expire le</th>
                                            <th class="text-nowrap">{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lignes as $l)
                                            @php
                                                $carte = $l['carte'];
                                                $statut = $carte?->statutAffiche();
                                                $couleurs = ['active' => 'success', 'expiree' => 'warning', 'perdue' => 'danger', 'annulee' => 'secondary', 'remplacee' => 'secondary'];
                                            @endphp
                                            <tr wire:key="carte-{{ $type }}-{{ $l['id'] }}">
                                                <td>{{ $l['matricule'] }}</td>
                                                <td>{{ $l['nom'] }}</td>
                                                <td>{{ $l['prenom'] }}</td>
                                                <td>{{ $l['detail'] }}</td>
                                                <td>{{ $carte?->numero ?? '—' }}</td>
                                                <td>
                                                    @if ($carte)
                                                        <span class="badge badge-{{ $couleurs[$statut] ?? 'secondary' }}">{{ \App\Services\CarteService::STATUTS[$statut] ?? $statut }}</span>
                                                    @else
                                                        <span class="text-muted">Sans carte</span>
                                                    @endif
                                                </td>
                                                <td>{{ $carte?->date_expiration?->format('d/m/Y') ?? '—' }}</td>
                                                <td class="text-nowrap">
                                                    @if (!$carte)
                                                        @if ($peutAjouter)
                                                            <button type="button" class="btn btn-primary btn-sm" wire:click="generer({{ $l['id'] }})"
                                                                wire:loading.attr="disabled" title="Générer la carte">
                                                                <i class="ti ti-id-badge"></i> Générer
                                                            </button>
                                                        @endif
                                                    @else
                                                        @if ($carte->estValide())
                                                            <a class="btn btn-warning btn-sm" target="_blank" title="Imprimer"
                                                                href="{{ url('carte/imprimer/' . encrypt($carte->id)) }}"><i class="ti ti-printer"></i></a>
                                                        @endif
                                                        @if ($peutAjouter)
                                                            <button type="button" class="btn btn-info btn-sm" wire:click="remplacer({{ $carte->id }})"
                                                                wire:confirm="Générer un duplicata ? La carte {{ $carte->numero }} sera remplacée."
                                                                title="Duplicata / renouvellement"><i class="ti ti-reload"></i></button>
                                                        @endif
                                                        @if ($peutModifier && $carte->statut === 'active')
                                                            <button type="button" class="btn btn-outline-danger btn-sm" wire:click="declarerPerdue({{ $carte->id }})"
                                                                wire:confirm="Déclarer la carte {{ $carte->numero }} perdue ?" title="Déclarer perdue"><i class="ti ti-alert"></i></button>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="annuler({{ $carte->id }})"
                                                                wire:confirm="Annuler la carte {{ $carte->numero }} ?" title="Annuler"><i class="ti ti-close"></i></button>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center text-muted">Aucun résultat.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{ $lignes->links() }}
                        </div>
                    </div>
                </div>

                @include('components.layouts.footer')
            </div>
        </div>

        @include('components.layouts.loading')
    </body>
</div>
