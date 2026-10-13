<div>

    <body class="fixed-navbar ">
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

                    @if (!empty(Permission('Afficher')))
                        <div class="ibox ibox-primary">
                            <div class="ibox-head ibox ibox-danger">
                                <div class="ibox-title">{{ __('message.Liste_rubrique') }} </div>
                                <div class="ibox-tools">
                                    <!-- Button trigger modal -->
                                    @if (!empty(Permission('Ajouter')))
                                        <button type="button" class="btn btn-ligth" data-toggle="modal"
                                            data-target="#exampleModalCenter" wire:click="resetInput"><i class="fa fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <!-- Modal -->
                            <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addRubrique'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter_rubrique') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>{{ __('message.Code') }}</label>
                                                        <input class="form-control" type="text"
                                                            wire:model='code_rubrique'>
                                                        <div>
                                                            @error('code_rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-9 form-group">
                                                        <label>{{ __('message.Rubrique') }}</label>
                                                        <input class="form-control" type="text"
                                                            wire:model='rubrique'>
                                                        <div>
                                                            @error('rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8 form-group">
                                                        <label>{{ __('message.Type_rubrique') }}</label>
                                                        <select class="form-control" wire:model='type_rubrique'>
                                                            <option value=""> </option>
                                                            <option value="Brute I">
                                                                Brute I
                                                            </option>
                                                            <option value="Brute NI">
                                                                Brute NI
                                                            </option>
                                                            <option value="Cotisation">
                                                                Cotisation
                                                            </option>
                                                            <option value="Non soumise">
                                                                Non soumise
                                                            </option>

                                                        </select>
                                                        <div>
                                                            @error('type_rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 form-group">
                                                        <label>{{ __('message.Sens') }}</label>
                                                        <select class="form-control" wire:model='sens_rubrique'>
                                                            <option value=""> </option>
                                                            <option value="Gain">
                                                                Gain
                                                            </option>
                                                            <option value="Retenu">
                                                                Retenu
                                                            </option>
<option value="Patronal">Patronal (hors net)</option>
                                                        </select>
                                                        <div>
                                                            @error('sens_rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                                                                            <div class="row">
                                                    <div class="col-sm-12 form-group">
                                                        <label>{{ __('message.Categorie_rubrique') }}</label>
                                                        <select class="form-control" wire:model='categorie_rubrique'>
                                                            <option value=""> </option>
                                                            <option value="Commun">Commun (Personnel et Enseignant)</option>
                                                            <option value="Personnel">Personnel uniquement</option>
                                                            <option value="Enseignant">Enseignant uniquement</option>
                                                        </select>
                                                        <div>
                                                            @error('categorie_rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
<div class="row">
    <div class="col-sm-6 form-group">
        <label>Base CNSS (gains « Brute I / NI »)</label>
        <select class="form-control" wire:model='soumis_cnss'>
            <option value="">Selon le type de rubrique</option>
            <option value="1">Soumise à la CNSS</option>
            <option value="0">Exclue de la CNSS</option>
        </select>
    </div>
    <div class="col-sm-6 form-group">
        <label>Base ITS (gains « Brute I / NI »)</label>
        <select class="form-control" wire:model='soumis_its'>
            <option value="">Selon le type de rubrique</option>
            <option value="1">Soumise à l'ITS</option>
            <option value="0">Exclue de l'ITS</option>
        </select>
    </div>
    <div class="col-sm-12">
        <small class="text-muted">Ex. indemnité de transport : « Exclue de la CNSS » + « Soumise à l'ITS ». Par défaut, « Brute I » est soumise aux deux et « Brute NI » à aucune.</small>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Mode de la rubrique</label>
        <select class="form-control" wire:model.live='mode_rubrique'>
            <option value="manuel">Manuel (montant saisi)</option>
            <option value="automatique">Automatique (formule)</option>
        </select>
        @error('mode_rubrique') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    @if ($mode_rubrique === 'automatique')
        <div class="col-sm-8 form-group">
            <label>Formule</label>
            <input class="form-control" type="text" wire:model='formule' placeholder="ex. base*taux*nombre">
            @error('formule') <span class="text-error">{{ $message }}</span> @enderror
        </div>
        <div class="col-sm-12">
            <small class="text-muted">
                Variables : <code>base</code>, <code>taux</code> (en %, 5 = 5 %), <code>nombre</code>, <code>anciennete</code>, <code>charge_familiale</code>, <code>taux_charge</code> (automatiques) ·
                constantes : leur code (menu « Constante paie ») ·
                exemples : <code>base*nombre</code>, <code>base*taux</code>, <code>base*taux*nombre</code>, <code>base+nombre</code>,
                <code>min(base;PLAFOND)*taux</code>, <code>si(base&gt;SEUIL;base*taux;0)</code>
            </small>
        </div>
        <div class="col-sm-12 form-group">
            <label>Variables, constantes et paramètres de paie (cliquer pour ajouter à la formule)</label>
            <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:8px;">
                @foreach ($catalogue as $groupe)
                    <div class="mb-2">
                        <strong>{{ $groupe['groupe'] }}</strong><br>
                        @foreach ($groupe['items'] as $item)
                            <button type="button" class="btn btn-xs btn-outline-secondary mb-1"
                                wire:click="insererDansFormule('{{ $item['code'] }}')"
                                title="{{ $item['libelle'] }}{{ $item['valeur'] !== null ? ' = '.rtrim(rtrim(number_format($item['valeur'], 4, ',', ' '), '0'), ',') : '' }}">
                                {{ $item['code'] }}@if ($item['valeur'] !== null) <span class="text-muted">= {{ rtrim(rtrim(number_format($item['valeur'], 4, ',', ' '), '0'), ',') }}</span>@endif
                            </button>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <small class="text-muted">
                Les taux CNSS / ANPE patronaux, le FSSP, les charges de famille et le barème ITS viennent de Paramètres paie : modifiés là-bas,
                ils sont repris par la formule au prochain calcul. Pas de nombre écrit en dur (hors 0, 1, 100) : créez une constante.
            </small>
        </div>
    @endif
</div>
<div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                <button type="Submit"
                                                    class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="ibox-body">
                                <form class="form-inline">
                                    {{-- <label class="sr-only" for="ex-email">{{ __('message.Matricule') }}</label>
                            <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                placeholder=" {{ __('message.Matricule') }}"> --}}
                                    <label class="sr-only" for="ex-email">{{ __('message.rubrique') }}</label>
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                        wire:model.live='recherche'
                                        placeholder="{{ __('message.Recherche') }} {{ __('message.rubrique') }} ">

                                </form>
                                <div class="table-responsive">
                                    @include('livewire.alert.message')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="50px">{{ __('message.Code') }}</th>
                                                <th>{{ __('message.Rubrique') }}</th>
                                                <th>{{ __('message.Type') }}</th>
                                                <th>{{ __('message.Sens') }}</th>
                                                <th>{{ __('message.Categorie_rubrique') }}</th>
                                                <th>Mode</th>
                                                @if (!empty(Permission('Modifier')))
                                                    <th>{{ __('message.Actions') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rubriques as $rubrique)
                                                <tr>
                                                    <td>{{ $rubrique->code_rubrique }}</td>
                                                    <td>{{ $rubrique->rubrique }}</td>
                                                    <td>{{ $rubrique->type_rubrique }}</td>
                                                    <td>{{ $rubrique->sens_rubrique === 'Patronal' || in_array($rubrique->calcul_auto, ['CNSS_PAT', 'ANPE_PAT'], true) ? 'Patronal (hors net)' : $rubrique->sens_rubrique }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $rubrique->categorie_rubrique === 'Enseignant' ? 'info' : ($rubrique->categorie_rubrique === 'Personnel' ? 'warning' : 'secondary') }}">
                                                            {{ $rubrique->categorie_rubrique ?? 'Commun' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($rubrique->calcul_auto)
                                                            <span class="badge badge-success">Automatique (barème)</span>
                                                        @elseif ($rubrique->mode_rubrique === 'automatique')
                                                            <span class="badge badge-success">Automatique</span>
                                                            <code>{{ $rubrique->formule }}</code>
                                                        @else
                                                            <span class="badge badge-secondary">Manuel</span>
                                                        @endif
                                                        @if ($rubrique->soumis_cnss !== null || $rubrique->soumis_its !== null)
                                                            <br><small class="text-muted">CNSS : {{ $rubrique->soumis_cnss === null ? 'selon type' : ($rubrique->soumis_cnss ? 'oui' : 'non') }} · ITS : {{ $rubrique->soumis_its === null ? 'selon type' : ($rubrique->soumis_its ? 'oui' : 'non') }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty(Permission('Modifier')))
                                                            <button type="button"
                                                                wire:click.prevent="editRubrique({{ $rubrique->id }})"
                                                                class="btn btn-sm  btn-info" data-toggle="modal"
                                                                data-target="#editRubrique"
                                                                data-whatever="@getbootstrap"><i
                                                                    class="feather fa fa-edit"></i></button>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <!-- Liens de pagination -->
                                    <div>
                                        {{ $rubriques->links() }}
                                    </div>
                                    {{-- Modal editsalle_etude --}}
                                    <div wire:ignore.self class="modal fade" id="editRubrique" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateRubrique'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('message.Modifier_rubrique') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>{{ __('message.Code') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='code_rubrique' readonly>
                                                                <div>
                                                                    @error('code_rubrique')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-9 form-group">
                                                                <label>{{ __('message.Rubrique') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='rubrique'>
                                                                <div>
                                                                    @error('rubrique')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-8 form-group">
                                                                <label>{{ __('message.Type_rubrique') }}</label>
                                                                <select class="form-control" wire:model='type_rubrique'>
                                                                    <option value=""> </option>
                                                                    <option value="Brute I">
                                                                        Brute I
                                                                    </option>
                                                                    <option value="Brute NI">
                                                                        Brute NI
                                                                    </option>
                                                                    <option value="Cotisation">
                                                                        Cotisation
                                                                    </option>
                                                                    <option value="Non soumise">
                                                                        Non soumise
                                                                    </option>
        
                                                                </select>
                                                                <div>
                                                                    @error('type_rubrique')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>{{ __('message.Sens') }}</label>
                                                                <select class="form-control" wire:model='sens_rubrique'>
                                                                    <option value=""> </option>
                                                                    <option value="Gain">
                                                                        Gain
                                                                    </option>
                                                                    <option value="Retenu">
                                                                        Retenu
                                                                    </option>
<option value="Patronal">Patronal (hors net)</option>
                                                                </select>
                                                                <div>
                                                                    @error('sens_rubrique')
                                                                        <span class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                    </div>
                                                                                                    <div class="row">
                                                    <div class="col-sm-12 form-group">
                                                        <label>{{ __('message.Categorie_rubrique') }}</label>
                                                        <select class="form-control" wire:model='categorie_rubrique'>
                                                            <option value=""> </option>
                                                            <option value="Commun">Commun (Personnel et Enseignant)</option>
                                                            <option value="Personnel">Personnel uniquement</option>
                                                            <option value="Enseignant">Enseignant uniquement</option>
                                                        </select>
                                                        <div>
                                                            @error('categorie_rubrique')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
<div class="row">
    <div class="col-sm-6 form-group">
        <label>Base CNSS (gains « Brute I / NI »)</label>
        <select class="form-control" wire:model='soumis_cnss'>
            <option value="">Selon le type de rubrique</option>
            <option value="1">Soumise à la CNSS</option>
            <option value="0">Exclue de la CNSS</option>
        </select>
    </div>
    <div class="col-sm-6 form-group">
        <label>Base ITS (gains « Brute I / NI »)</label>
        <select class="form-control" wire:model='soumis_its'>
            <option value="">Selon le type de rubrique</option>
            <option value="1">Soumise à l'ITS</option>
            <option value="0">Exclue de l'ITS</option>
        </select>
    </div>
    <div class="col-sm-12">
        <small class="text-muted">Ex. indemnité de transport : « Exclue de la CNSS » + « Soumise à l'ITS ». Par défaut, « Brute I » est soumise aux deux et « Brute NI » à aucune.</small>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 form-group">
        <label>Mode de la rubrique</label>
        <select class="form-control" wire:model.live='mode_rubrique'>
            <option value="manuel">Manuel (montant saisi)</option>
            <option value="automatique">Automatique (formule)</option>
        </select>
        @error('mode_rubrique') <span class="text-error">{{ $message }}</span> @enderror
    </div>
    @if ($mode_rubrique === 'automatique')
        <div class="col-sm-8 form-group">
            <label>Formule</label>
            <input class="form-control" type="text" wire:model='formule' placeholder="ex. base*taux*nombre">
            @error('formule') <span class="text-error">{{ $message }}</span> @enderror
        </div>
        <div class="col-sm-12">
            <small class="text-muted">
                Variables : <code>base</code>, <code>taux</code> (en %, 5 = 5 %), <code>nombre</code>, <code>anciennete</code>, <code>charge_familiale</code>, <code>taux_charge</code> (automatiques) ·
                constantes : leur code (menu « Constante paie ») ·
                exemples : <code>base*nombre</code>, <code>base*taux</code>, <code>base*taux*nombre</code>, <code>base+nombre</code>,
                <code>min(base;PLAFOND)*taux</code>, <code>si(base&gt;SEUIL;base*taux;0)</code>
            </small>
        </div>
        <div class="col-sm-12 form-group">
            <label>Variables, constantes et paramètres de paie (cliquer pour ajouter à la formule)</label>
            <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:8px;">
                @foreach ($catalogue as $groupe)
                    <div class="mb-2">
                        <strong>{{ $groupe['groupe'] }}</strong><br>
                        @foreach ($groupe['items'] as $item)
                            <button type="button" class="btn btn-xs btn-outline-secondary mb-1"
                                wire:click="insererDansFormule('{{ $item['code'] }}')"
                                title="{{ $item['libelle'] }}{{ $item['valeur'] !== null ? ' = '.rtrim(rtrim(number_format($item['valeur'], 4, ',', ' '), '0'), ',') : '' }}">
                                {{ $item['code'] }}@if ($item['valeur'] !== null) <span class="text-muted">= {{ rtrim(rtrim(number_format($item['valeur'], 4, ',', ' '), '0'), ',') }}</span>@endif
                            </button>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <small class="text-muted">
                Les taux CNSS / ANPE patronaux, le FSSP, les charges de famille et le barème ITS viennent de Paramètres paie : modifiés là-bas,
                ils sont repris par la formule au prochain calcul. Pas de nombre écrit en dur (hors 0, 1, 100) : créez une constante.
            </small>
        </div>
    @endif
</div>
<div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-primary">{{ __('message.Enregistrer') }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="ibox ibox-primary">
                            <div class="ibox-body text-center">
                                <h1 class="m-t-20">404</h1>
                                <p class="error-title">PAGE NOT FOUND</p>
                            </div>
                        </div>
                    @endif
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
