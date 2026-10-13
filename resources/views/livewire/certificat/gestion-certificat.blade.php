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

                                @if ($scolarite)
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
                                    <option value="">Tous</option>
                                    <option value="sans">Sans certificat valide</option>
                                    <option value="avec">Avec certificat valide</option>
                                </select>
                                <div wire:loading class="small text-muted">{{ __('message.Chargement') }}…</div>
                            </form>

                            <div class="mt-3">
                                @include('livewire.alert.message')
                                @unless ($anneeActive)
                                    <div class="alert alert-warning" role="alert">{{ __('message.Aucune_annee_active') }}</div>
                                @endunless
                            </div>

                            @if ($formulaire)
                                <div class="card mb-3 border-primary">
                                    <div class="card-header">
                                        <strong>{{ $formRemplaceId ? 'Duplicata / nouvelle édition' : 'Nouveau certificat' }}</strong>
                                        @if ($formRemplaceId)
                                            <small class="text-muted">— le certificat précédent sera remplacé</small>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @unless ($scolarite)
                                                <div class="form-group col-md-3">
                                                    <label class="mb-1">Prise de fonction <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" wire:model="dateDebut">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="mb-1">Fin de fonction</label>
                                                    <input type="date" class="form-control" wire:model="dateFin">
                                                    <small class="text-muted">Laisser vide si la personne est toujours en poste.</small>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="mb-1">Fonction occupée <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" maxlength="150" wire:model="fonction">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="libre" wire:model="libre">
                                                        <label class="form-check-label" for="libre">
                                                            Mentionner « libre de tout engagement » (uniquement si une date de fin est renseignée)
                                                        </label>
                                                    </div>
                                                </div>
                                            @endunless

                                            <div class="form-group col-md-6">
                                                <label class="mb-1">Qualité du signataire</label>
                                                <input type="text" class="form-control" maxlength="120" wire:model="qualite" placeholder="Le Directeur">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="mb-1">Nom du signataire (facultatif)</label>
                                                <input type="text" class="form-control" maxlength="120" wire:model="signataire">
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary btn-sm" wire:click="enregistrer" wire:loading.attr="disabled">
                                            <i class="ti ti-check"></i> Générer le certificat
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="fermer">Annuler</button>
                                    </div>
                                </div>
                            @endif

                            <div class="table-responsive mt-2">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>{{ __('message.Matricule') }}</th>
                                            <th>{{ __('message.Nom') }}</th>
                                            <th>{{ __('message.Prenom') }}</th>
                                            <th>{{ $scolarite ? 'Classe' : 'Fonction' }}</th>
                                            <th>N° certificat</th>
                                            <th>Statut</th>
                                            <th>Délivré le</th>
                                            <th class="text-nowrap">{{ __('message.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lignes as $l)
                                            @php
                                                $cert = $l['certificat'];
                                                $couleurs = ['valide' => 'success', 'annule' => 'secondary', 'remplace' => 'secondary'];
                                            @endphp
                                            <tr wire:key="certificat-{{ $l['id'] }}">
                                                <td>{{ $l['matricule'] }}</td>
                                                <td>{{ $l['nom'] }}</td>
                                                <td>{{ $l['prenom'] }}</td>
                                                <td>{{ $l['detail'] }}</td>
                                                <td>{{ $cert?->numero ?? '—' }}</td>
                                                <td>
                                                    @if ($cert)
                                                        <span class="badge badge-{{ $couleurs[$cert->statut] ?? 'secondary' }}">{{ \App\Services\CertificatService::STATUTS[$cert->statut] ?? $cert->statut }}</span>
                                                    @else
                                                        <span class="text-muted">Aucun</span>
                                                    @endif
                                                </td>
                                                <td>{{ $cert?->date_emission?->format('d/m/Y') ?? '—' }}</td>
                                                <td class="text-nowrap">
                                                    @if (!$cert || $cert->statut !== 'valide')
                                                        @if ($peutAjouter && $anneeActive)
                                                            <button type="button" class="btn btn-primary btn-sm" wire:click="ouvrir({{ $l['id'] }})"
                                                                wire:loading.attr="disabled" title="Générer un certificat">
                                                                <i class="ti ti-file"></i> Générer
                                                            </button>
                                                        @endif
                                                    @else
                                                        <a class="btn btn-warning btn-sm" target="_blank" title="Imprimer"
                                                            href="{{ url('certificat/imprimer/' . encrypt($cert->id)) }}"><i class="ti ti-printer"></i></a>
                                                        @if ($peutAjouter)
                                                            <button type="button" class="btn btn-info btn-sm" wire:click="ouvrirDuplicata({{ $cert->id }})"
                                                                title="Duplicata / nouvelle édition"><i class="ti ti-reload"></i></button>
                                                        @endif
                                                        @if ($peutModifier)
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="annuler({{ $cert->id }})"
                                                                wire:confirm="Annuler le certificat {{ $cert->numero }} ?" title="Annuler"><i class="ti ti-close"></i></button>
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
