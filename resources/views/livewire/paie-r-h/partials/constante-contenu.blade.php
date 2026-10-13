<div class="ibox ibox-primary">
                            <div class="ibox-head ibox ibox-danger">
                                <div class="ibox-title">Constantes de paie (tranche, calcul, test, autre)</div>
                                <div class="ibox-tools">
                                    @if (!empty(Permission('Ajouter')))
                                        <button type="button" class="btn btn-ligth" data-toggle="modal"
                                            data-target="#addConstanteModal" wire:click="resetInput"><i class="fa fa-plus"></i></button>
                                    @endif
                                </div>
                            </div>
                            <div wire:ignore.self class="modal fade" id="addConstanteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form wire:submit='addConstante'>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une constante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Code</label>
        <input class="form-control" type="text" wire:model='code' placeholder="ex. PLAFOND_CNSS">
        @error('code') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    <div class="col-sm-8 form-group">
        <label>Libellé</label>
        <input class="form-control" type="text" wire:model='libelle'>
        @error('libelle') <span class="text-error">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Catégorie</label>
        <select class="form-control" wire:model.live='categorie'>
            @foreach (\App\Models\PaieRH\ConstantePaieModel::CATEGORIES as $cle => $nom)
                <option value="{{ $cle }}">{{ $nom }}</option>
            @endforeach
        </select>
        @error('categorie') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    <div class="col-sm-4 form-group">
        <label>{{ $categorie === 'tranche' ? 'Borne minimale (CODE)' : 'Valeur' }}</label>
        <input class="form-control" type="text" wire:model='valeur'>
        @error('valeur') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    @if ($categorie === 'tranche')
        <div class="col-sm-4 form-group">
            <label>Borne maximale (CODE_MAX)</label>
            <input class="form-control" type="text" wire:model='valeur_max'>
            @error('valeur_max') <span class="text-error">{{ $message }}</span> @enderror
        </div>
    @endif
</div>
<div class="row">
    <div class="col-sm-12 form-group">
        <label>Description (facultatif)</label>
        <input class="form-control" type="text" wire:model='description'>
        @error('description') <span class="text-error">{{ $message }}</span> @enderror
    </div>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('message.Fermer') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
                            <div class="ibox-body">
                                <form class="form-inline">
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" type="text" wire:model.live='recherche'
                                        placeholder="{{ __('message.Recherche') }} code ou libellé">
                                    <div class="btn-group mb-2 mb-sm-0 mr-sm-2" role="group">
    <button type="button" class="btn btn-sm {{ $filtre_categorie === '' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filtre_categorie', '')">Toutes</button>
    @foreach (\App\Models\PaieRH\ConstantePaieModel::CATEGORIES as $cle => $nom)
        <button type="button" class="btn btn-sm {{ $filtre_categorie === $cle ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filtre_categorie', '{{ $cle }}')">{{ ucfirst($cle) }}</button>
    @endforeach
</div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Libellé</th>
                                                <th>Catégorie</th>
                                                <th style="text-align:right">Valeur</th>
                                                <th style="text-align:right">Valeur max</th>
                                                @if (!empty(Permission('Modifier')))
                                                    <th>{{ __('message.Actions') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($constantes as $c)
                                                <tr>
                                                    <td><code>{{ $c->code }}</code></td>
                                                    <td>{{ $c->libelle }}</td>
                                                    <td><span class="badge badge-info">{{ ucfirst($c->categorie) }}</span></td>
                                                    <td style="text-align:right">{{ rtrim(rtrim(number_format((float) $c->valeur, 4, ',', ' '), '0'), ',') }}</td>
                                                    <td style="text-align:right">{{ $c->valeur_max !== null ? rtrim(rtrim(number_format((float) $c->valeur_max, 4, ',', ' '), '0'), ',') : '—' }}</td>
                                                    @if (!empty(Permission('Modifier')))
                                                        <td>
                                                            <button type="button" wire:click.prevent="editConstante({{ $c->id }})"
                                                                class="btn btn-sm btn-info" data-toggle="modal" data-target="#editConstanteModal">
                                                                <i class="feather fa fa-edit"></i></button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div>{{ $constantes->links() }}</div>
                                    <div wire:ignore.self class="modal fade" id="editConstanteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form wire:submit='updateConstante'>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la constante</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Code</label>
        <input class="form-control" type="text" wire:model='code' placeholder="ex. PLAFOND_CNSS">
        @error('code') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    <div class="col-sm-8 form-group">
        <label>Libellé</label>
        <input class="form-control" type="text" wire:model='libelle'>
        @error('libelle') <span class="text-error">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Catégorie</label>
        <select class="form-control" wire:model.live='categorie'>
            @foreach (\App\Models\PaieRH\ConstantePaieModel::CATEGORIES as $cle => $nom)
                <option value="{{ $cle }}">{{ $nom }}</option>
            @endforeach
        </select>
        @error('categorie') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    <div class="col-sm-4 form-group">
        <label>{{ $categorie === 'tranche' ? 'Borne minimale (CODE)' : 'Valeur' }}</label>
        <input class="form-control" type="text" wire:model='valeur'>
        @error('valeur') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    @if ($categorie === 'tranche')
        <div class="col-sm-4 form-group">
            <label>Borne maximale (CODE_MAX)</label>
            <input class="form-control" type="text" wire:model='valeur_max'>
            @error('valeur_max') <span class="text-error">{{ $message }}</span> @enderror
        </div>
    @endif
</div>
<div class="row">
    <div class="col-sm-12 form-group">
        <label>Description (facultatif)</label>
        <input class="form-control" type="text" wire:model='description'>
        @error('description') <span class="text-error">{{ $message }}</span> @enderror
    </div>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('message.Fermer') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
                                </div>
                                <small class="text-muted">
                                    Utilisation dans une formule de rubrique : écrire le code (ex. <code>base*TAUX_PRIME</code>) ;
                                    pour une tranche, <code>CODE</code> = borne min et <code>CODE_MAX</code> = borne max ;
                                    une constante « test » sert dans une condition : <code>si(base&gt;SEUIL;base*taux;0)</code>.
                                </small>
                            </div>
                        </div>
