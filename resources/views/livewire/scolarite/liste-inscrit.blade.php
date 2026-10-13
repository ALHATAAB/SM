<div>

    <body class="fixed-navbar">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')

            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    <div class="ibox ibox-primary">
                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_inscrit') }}</div>
                        </div>

                        <div class="ibox-body">
                            <span><strong><i class="ti ti-filter"></i> {{ __('message.Filtre') }}</strong></span>

                            <form class="form-inline mt-2">
                                <label class="sr-only" for="filtre-recherche">{{ __('message.Recherche') }}</label>
                                <input id="filtre-recherche" class="mb-2 form-control mr-sm-2 mb-sm-0" type="text"
                                    wire:model.live.debounce.300ms="recherche"
                                    placeholder="{{ __('message.Recherche') }} ">
                                <div wire:loading wire:target="recherche" class="small text-muted">
                                    {{ __('message.Chargement') }}…
                                </div>

                            </form>

                            <div class="mt-3">
                                @include('livewire.alert.message')
                                @unless ($anneeActive)
                                    <div class="alert alert-warning" role="alert">
                                        {{ __('message.Aucune_annee_active') }}
                                    </div>
                                @endunless
                            </div>

                            <div class="table-responsive mt-2">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-nowrap">{{ __('message.Matricule') }}</th>
                                            <th class="text-nowrap">{{ __('message.Nom') }}</th>
                                            <th class="text-nowrap">{{ __('message.Prenom') }}</th>
                                            <th class="text-nowrap">{{ __('message.Telephone') }}</th>
                                            <th class="text-nowrap">{{ __('message.Niveau_etude') }}</th>
                                            <th class="text-nowrap">{{ __('message.Filiere_etude') }}</th>
                                            <th class="text-nowrap">{{ __('message.Classe_etude') }}</th>
                                            <th class="text-nowrap">{{ __('message.Annee') }}</th>
                                            @if (!empty(Permission('Consulter')))
                                                <th class="text-nowrap">{{ __('message.Actions') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($inscrits as $inscrit)
                                            <tr wire:key="inscrit-{{ $inscrit->id }}">
                                                <td class="text-truncate" style="max-width: 140px;">
                                                    {{ $inscrit->matricule }}</td>
                                                <td class="text-truncate" style="max-width: 160px;">
                                                    {{ optional($inscrit->preInscription)->nom }}</td>
                                                <td class="text-truncate" style="max-width: 160px;">
                                                    {{ optional($inscrit->preInscription)->prenom }}</td>
                                                <td class="text-truncate" style="max-width: 160px;">
                                                    {{ optional($inscrit->preInscription)->telephone }}</td>
                                                <td class="text-truncate" style="max-width: 140px;">
                                                    {{ optional($inscrit->niveau)->niveau }}</td>
                                                <td class="text-truncate" style="max-width: 160px;">
                                                    {{ optional($inscrit->filiere)->filiere }}</td>
                                                <td class="text-truncate" style="max-width: 140px;">
                                                    {{ optional($inscrit->classe)->classe }}</td>
                                                <td class="text-truncate" style="max-width: 120px;">
                                                    {{ optional($inscrit->annee)->annee }}</td>

                                                @if (!empty(Permission('Consulter')) || !empty(Permission('Modifier')))
                                                    <td class="text-nowrap">
                                                        @if (!empty(Permission('Consulter')))
                                                            <a class="btn btn-light btn-sm"
                                                                href="{{ url('/detail/inscrit/' . encrypt($inscrit->id)) }}">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        @endif

                                                        @if (!empty(Permission('Scolarite')))
                                                        <a href="{{ url('/fiche/apprenant/' . encrypt($inscrit->id)) }}" target="_blank" class="btn btn-warning btn-sm" title="Fiche complète (PDF)">
                                                            <i class="ti ti-printer"></i>
                                                        </a>
                                                        @endif

                                                        @if (!empty(Permission('Modifier')))
                                                            <a class="btn btn-info btn-sm"
                                                                href="{{ url('/editer/inscrit/' . encrypt($inscrit->id)) }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>

                                                            <button type="button"
                                                                wire:click.prevent="affectationClasse({{ $inscrit->id }})"
                                                                class="btn btn-success btn-sm" data-toggle="modal"
                                                                data-target="#affectationClasse">
                                                                <i class="ti ti-control-shuffle"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Suppression (si tu veux réactiver) --}}
                                                        {{-- @if (!empty(Permission('Supprimer')))
                              <button type="button"
                                      wire:click.prevent="supprimerInscription({{ $inscrit->id }})"
                                      class="btn btn-danger btn-sm"
                                      data-toggle="modal"
                                      data-target="#supprimerInscription">
                                <i class="ti ti-trash"></i>
                              </button>
                            @endif --}}
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">
                                                    {{ __('message.Aucun_resultat') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-end">
                                {{ $inscrits->onEachSide(1)->links() }}
                            </div>

                            {{-- Modal affectationClasse --}}
                            <div wire:ignore.self class="modal fade" id="affectationClasse" tabindex="-1"
                                role="dialog" aria-labelledby="affectationClasseTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit="updateAffectationClasse">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="affectationClasseTitle">
                                                    {{ __('message.Modifier_classe_affecter') }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Classe_affecter') }}</label>
                                                    <select class="form-control" wire:model="classe">
                                                        <option value="">—</option>
                                                        @foreach ($classes as $cl)
                                                            <option value="{{ $cl->id }}">{{ $cl->classe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('classe')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    {{ __('message.Fermer') }}
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('message.Modifier') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal supprimerInscription --}}
                            <div wire:ignore.self class="modal fade" id="supprimerInscription" tabindex="-1"
                                role="dialog" aria-labelledby="supprimerInscriptionTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit="UpdateSupprimerInsctiption">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="supprimerInscriptionTitle">
                                                    {{ __('message.Supprimer') }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="alert alert-danger alert-dismissable fade show mb-0">
                                                    <h4 class="alert-heading">Alert !</h4>
                                                    <p class="mb-0">
                                                        {{ __('message.Confirmation_suppression') }}
                                                        {{-- Exemple : "Vous êtes sûr de supprimer l'inscription de : {{ $nom.' '.$prenom }}, Niveau : {{ $niveau_etude }}, Option : {{ $filiere_etude }}" --}}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">
                                                    {{ __('message.Fermer') }}
                                                </button>
                                                <button type="submit" class="btn btn-danger">
                                                    {{ __('message.Supprimer') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div> <!-- /.ibox-body -->
                    </div> <!-- /.ibox -->
                </div> <!-- /.page-content -->

                @include('components.layouts.footer')
            </div> <!-- /.content-wrapper -->
        </div> <!-- /.page-wrapper -->

        @include('components.layouts.loading')
    </body>
</div>
