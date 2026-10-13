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
                    @if (!empty(Permission('Afficher')))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="ibox">
                                    <div class="ibox-head">
                                        <div class="ibox-title">{{ __('menu.Parametre_paie_RH') }}</div>
                                        <div class="ibox-tools">
                                            <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                            <a class="dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item">option 1</a>
                                                <a class="dropdown-item">option 2</a>
                                            </div>
                                        </div>
                                    </div>
                                    @include('livewire.alert.message')
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">
                                                        {{ __('message.Taux_tranche_familliale') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        <!-- Button trigger modal -->
                                                        <button type="button" class="btn btn-primary btn-xs m-r-5"
                                                            data-toggle="modal"
                                                            data-target="#AjouterTauxChargeFamilliale"><i
                                                                class="fa fa-plus"></i>
                                                            {{ __('message.Ajouter') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Modal -->
                                                <div wire:ignore.self class="modal fade"
                                                    id="AjouterTauxChargeFamilliale" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form class="form-valide" wire:submit='addTauxChargeFamilliale'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Nouvel Taux abattement
                                                                        charge familliale
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-validation">

                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-charge">Nombre de charge <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text"
                                                                                    wire:model='nombre_de_charge'
                                                                                    class="form-control" id="val-charge"
                                                                                    name="val-charge"
                                                                                    placeholder="Enter  charge..">
                                                                                <div>
                                                                                    @error('nombre_de_charge')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-username">Taux charge <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text"
                                                                                    wire:model='taux_charge'
                                                                                    class="form-control"
                                                                                    id="val-taux_charge"
                                                                                    name="val-taux_charge"
                                                                                    placeholder="Enter  taux charge..">
                                                                                <div>
                                                                                    @error('taux_charge')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Fermer</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success">Sauvegarder</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <table class="table table-bordered verticle-middle">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Nombre de charge</th>
                                                                <th scope="col">Taux en %</th>
                                                                <th scope="col">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($taux_charge_familliales as $taux_charge_familliale)
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <strong>
                                                                            {{ $taux_charge_familliale->nombre_de_charge }}
                                                                        </strong>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        {{ $taux_charge_familliale->taux_charge . ' %' }}
                                                                    </td>
                                                                    <td>
                                                                        <span>
                                                                            <a href="#" wire:click.prevent="editChargeFamiliale({{ $taux_charge_familliale->id }})" data-toggle="modal" data-target="#ModalEditCharge" title="Modifier"><i class="fa fa-pencil color-muted m-r-5"></i></a>
                                                                            <a href="#" wire:click.prevent="deleteChargeFamiliale({{ $taux_charge_familliale->id }})" wire:confirm="Supprimer ce taux de charge familiale ?" title="Supprimer"><i class="fa fa-close color-danger"></i></a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeApprenant" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered"
                                                            role="document">
                                                            <form wire:submit='updateCongifMatriculeApprenant'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_apprenant') }}
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Initial_matricule_apprenant') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-info">{{ __('message.Valider') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">
                                                        {{ __('message.Taux_its') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        @if ($CountConfigEnseignant <= 0)
                                                            <!-- Button trigger modal -->
                                                            <button type="button"
                                                                class="btn btn-primary btn-xs m-r-5"
                                                                data-toggle="modal"
                                                                data-target="#AjouterTauxITS"><i
                                                                    class="fa fa-plus"></i>
                                                                {{ __('message.Ajouter') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Modal -->
                                                <div wire:ignore.self class="modal fade"
                                                    id="AjouterTauxITS" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form class="form-valide" wire:submit='addTauxFiscale'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Nouvel Taux l’impôt unique
                                                                        sur les traitements
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-validation">
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-charge">Tranche Min <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" wire:model='min'
                                                                                    oninput="formatThousands(this)"
                                                                                    class="form-control"
                                                                                    id="val-min" name="val-min"
                                                                                    placeholder="Enter  min..">
                                                                                <div>
                                                                                    @error('min')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-charge">Tranche Max <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" wire:model='max'
                                                                                    oninput="formatThousands(this)"
                                                                                    class="form-control"
                                                                                    id="val-max" name="val-max"
                                                                                    placeholder="Enter  max..">
                                                                                <div>
                                                                                    @error('max')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-username">Taux fiscale<span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text"
                                                                                    wire:model='taux_fiscale'
                                                                                    class="form-control"
                                                                                    id="val-taux" name="val-taux"
                                                                                    placeholder="Enter  taux..">
                                                                                <div>
                                                                                    @error('taux_fiscale')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Fermer</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success">Sauvegarder</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <table class="table table-bordered verticle-middle">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="text-align: center;">Tranche
                                                                    minimum</th>
                                                                <th scope="col" style="text-align: center;">Tranche
                                                                    maximum</th>
                                                                <th scope="col" style="text-align: center;">Taux en
                                                                    %</th>
                                                                <th scope="col">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($tranche_taux_fiscales as $tranche_taux_fiscale)
                                                                <tr>

                                                                    <td style="text-align: right;">
                                                                        {{ number_format($tranche_taux_fiscale->min, 0, ',', ' ') }}
                                                                    </td>
                                                                    <td style="text-align: right;">
                                                                        {{ number_format($tranche_taux_fiscale->max, 0, ',', ' ') }}
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <strong>{{ $tranche_taux_fiscale->taux_fiscale }}</strong>
                                                                    </td>
                                                                    <td>
                                                                        <span>
                                                                            <a href="#" wire:click.prevent="editTauxFiscale({{ $tranche_taux_fiscale->id }})" data-toggle="modal" data-target="#ModalEditTauxIts" title="Modifier"><i class="fa fa-pencil color-muted m-r-5"></i></a>
                                                                            <a href="#" wire:click.prevent="deleteTauxFiscale({{ $tranche_taux_fiscale->id }})" wire:confirm="Supprimer cette tranche du barème ITS ?" title="Supprimer"><i class="fa fa-close color-danger"></i></a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeEnseignant" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered"
                                                            role="document">
                                                            <form wire:submit='updateCongifMatriculeEnseignant'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_enseignant') }}
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Valeur_its') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-info">{{ __('message.Valider') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">
                                                        {{ __('message.Valeur_its') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        @if ($CountConfigEmployer <= 0)
                                                            <!-- Button trigger modal -->
                                                            <button type="button"
                                                                class="btn btn-primary btn-xs m-r-5"
                                                                data-toggle="modal"
                                                                data-target="#AjouterInitialMatriculeEmployer"><i
                                                                    class="fa fa-plus"></i>
                                                                {{ __('message.Ajouter') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Modal -->
                                                <div wire:ignore.self class="modal fade"
                                                    id="AjouterInitialMatriculeEmployer" tabindex="-1"
                                                    role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form class="form-valide" wire:submit='addMontantFiscale' >
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Nouvel Taux l’impôt unique sur les traitements
                                                                    </h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-validation">
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-charge">Tranche Min <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" wire:model='tranche_min' oninput="formatThousands(this)" class="form-control"
                                                                                    id="val-min" name="val-min"
                                                                                    placeholder="Enter  min..">
                                                                                    <div>
                                                                                        @error('tranche_min')
                                                                                        <span class="text-error">{{$message}}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-charge">Tranche Max <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" wire:model='tranche_max' oninput="formatThousands(this)" class="form-control"
                                                                                    id="val-max" name="val-max"
                                                                                    placeholder="Enter  max..">
                                                                                    <div>
                                                                                        @error('tranche_max')
                                                                                        <span class="text-error">{{$message}}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label"
                                                                                for="val-username">Valeur fiscale<span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" wire:model='valeur_fiscale'  oninput="formatThousands(this)" class="form-control"
                                                                                    id="val-valeur-fiscale" name="val-valeur-fiscale"
                                                                                    placeholder="Enter  taux..">
                                                                                    <div>
                                                                                        @error('valeur_fiscale')
                                                                                        <span class="text-error">{{$message}}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Fermer</button>
                                                                    <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <table class="table table-bordered verticle-middle">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="text-align: center;">Tranche
                                                                    minimum</th>
                                                                <th scope="col" style="text-align: center;">Tranche
                                                                    maximum</th>
                                                                <th scope="col" style="text-align: center;">Valeur
                                                                </th>
                                                                <th scope="col">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($tranche_valeur_fiscales as $tranche_valeur_fiscale)
                                                                <tr>

                                                                    <td style="text-align: right;">
                                                                        {{ number_format($tranche_valeur_fiscale->tranche_min, 0, ',', ' ') }}
                                                                    </td>
                                                                    <td style="text-align: right;">
                                                                        {{ number_format($tranche_valeur_fiscale->tranche_max, 0, ',', ' ') }}
                                                                    </td>
                                                                    <td style="text-align: center;"><strong>
                                                                            {{ number_format($tranche_valeur_fiscale->valeur_fiscale, 0, ',', ' ') }}
                                                                        </strong></td>
                                                                    <td>
                                                                        <span>
                                                                            <a href="#" wire:click.prevent="editMontantFiscale({{ $tranche_valeur_fiscale->id }})" data-toggle="modal" data-target="#ModalEditMontantIts" title="Modifier"><i class="fa fa-pencil color-muted m-r-5"></i></a>
                                                                            <a href="#" wire:click.prevent="deleteMontantFiscale({{ $tranche_valeur_fiscale->id }})" wire:confirm="Supprimer cette tranche du barème ITS ?" title="Supprimer"><i class="fa fa-close color-danger"></i></a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeEmployer" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered"
                                                            role="document">
                                                            <form wire:submit='updateCongifMatriculeEmployer'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_employer') }}
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Initial_matricule_employer') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control"
                                                                                    type="text"
                                                                                    wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                        <span
                                                                                            class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button type="Submit"
                                                                            class="btn btn-info">{{ __('message.Valider') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div wire:ignore.self class="modal fade" id="ModalEditCharge" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateChargeFamiliale'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier le taux de charge familiale</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Nombre de charge</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='cf_nombre_de_charge' class="form-control">
                                            @error('cf_nombre_de_charge') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Taux en %</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='cf_taux_charge' class="form-control">
                                            @error('cf_taux_charge') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                        <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div wire:ignore.self class="modal fade" id="ModalEditTauxIts" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateTauxFiscale'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier la tranche du barème ITS (par taux)</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Borne minimale</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='tf_min' class="form-control">
                                            @error('tf_min') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Borne maximale</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='tf_max' class="form-control">
                                            @error('tf_max') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Taux en %</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='tf_taux_fiscale' class="form-control">
                                            @error('tf_taux_fiscale') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                        <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div wire:ignore.self class="modal fade" id="ModalEditMontantIts" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateMontantFiscale'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier la tranche du barème ITS (par montant)</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Borne minimale</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='mf_tranche_min' class="form-control">
                                            @error('mf_tranche_min') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Borne maximale</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='mf_tranche_max' class="form-control">
                                            @error('mf_tranche_max') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-5 col-form-label">Montant</label>
                                        <div class="col-lg-6">
                                            <input type="text" wire:model='mf_valeur_fiscale' class="form-control">
                                            @error('mf_valeur_fiscale') <span class="text-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                        <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    @livewire('paie-r-h.constante-paie', ['integre' => true], key('constantes-paie'))

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">Calcul de l'ITS et des charges patronales</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <p class="text-muted">
                                                        Base retenues ITS = brut imposable − retenue CNSS − abattement pour frais professionnels, arrondie au millier inférieur ;
                                                        l'abattement pour charges de famille s'applique ensuite, puis le barème ITS. Les primes exclues de la CNSS ou de l'ITS se
                                                        règlent rubrique par rubrique (menu Rubrique). Appliqué aux prochains calculs de paie.
                                                    </p>
                                                    <form class="form-inline" wire:submit='saveCalculIts'>
                                                        <label class="mr-2">Frais professionnels (%)</label>
                                                        <input type="text" wire:model='its_frais_pro_taux' class="form-control mr-4" style="width:100px">
                                                        <label class="mr-2">Base des charges patronales</label>
                                                        <select wire:model='cnss_patronal_base' class="form-control mr-3">
                                                            <option value="brut">Total des gains (plafonné)</option>
                                                            <option value="cnss">Même base que la CNSS salariale</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                        @error('its_frais_pro_taux') <span class="text-error ml-3">{{ $message }}</span> @enderror
                                                        @error('cnss_patronal_base') <span class="text-error ml-3">{{ $message }}</span> @enderror
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">Taux FSSP</div>
                                                </div>
                                                <div class="ibox-body">
                                                    <p class="text-muted">
                                                        Le FSSP est retenu en % du salaire net (calculé après la CNSS et l'ITS). Le nouveau taux s'applique aux
                                                        prochains calculs de paie ; les fiches déjà calculées ne changent pas.
                                                    </p>
                                                    <form class="form-inline" wire:submit='saveFsspTaux'>
                                                        <label class="mr-2">Taux FSSP (%)</label>
                                                        <input type="text" wire:model='fssp_taux' class="form-control mr-2" style="width:120px">
                                                        <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                        @error('fssp_taux') <span class="text-error ml-3">{{ $message }}</span> @enderror
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card m-t-20 m-b-20">
                                                <div class="ibox-head">
                                                    <div class="ibox-title">Cotisations sociales (CNSS)</div>
                                                    <div class="ibox-tools">
                                                        <button type="button" class="btn btn-primary btn-xs m-r-5"
                                                            wire:click="resetCotisation" data-toggle="modal"
                                                            data-target="#ModalCotisation"><i class="fa fa-plus"></i>
                                                            {{ __('message.Ajouter') }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="ibox-body">
                                                    <p class="text-muted">
                                                        Ces taux servent au calcul automatique de la CNSS (part salariale) et
                                                        de la base de l'ITS sur les bulletins du personnel. La part patronale (CNSS et ANPE,
                                                        lignes séparées) est informative, hors net. Un plafond à 0 signifie « sans plafond ».
                                                    </p>
                                                    <table class="table table-bordered verticle-middle">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Branche</th>
                                                                <th scope="col" style="text-align: center;">Organisme</th>
                                                                <th scope="col" style="text-align: center;">Taux salarié %</th>
                                                                <th scope="col" style="text-align: center;">Taux patronal %</th>
                                                                <th scope="col" style="text-align: center;">Plafond mensuel</th>
                                                                <th scope="col" style="text-align: center;">Actif</th>
                                                                <th scope="col">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($cotisations as $cotisation)
                                                                <tr>
                                                                    <td>{{ $cotisation->intitule }}</td>
                                                                    <td style="text-align: center;">{{ $cotisation->organisme ?: 'CNSS' }}</td>
                                                                    <td style="text-align: center;">{{ $cotisation->taux_salarie + 0 }}</td>
                                                                    <td style="text-align: center;">{{ $cotisation->taux_patronal + 0 }}</td>
                                                                    <td style="text-align: right;">
                                                                        {{ $cotisation->plafond_mensuel > 0 ? number_format($cotisation->plafond_mensuel, 0, ',', ' ') : 'Sans plafond' }}
                                                                    </td>
                                                                    <td style="text-align: center;">{{ $cotisation->actif ? 'Oui' : 'Non' }}</td>
                                                                    <td>
                                                                        <span>
                                                                            <a href="#" wire:click.prevent="editCotisation({{ $cotisation->id }})"
                                                                                data-toggle="modal" data-target="#ModalCotisation"
                                                                                title="Modifier"><i class="fa fa-pencil color-muted m-r-5"></i></a>
                                                                            <a href="#" wire:click.prevent="deleteCotisation({{ $cotisation->id }})"
                                                                                wire:confirm="Supprimer cette cotisation ?"
                                                                                title="Supprimer"><i class="fa fa-close color-danger"></i></a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div wire:ignore.self class="modal fade" id="ModalCotisation" tabindex="-1"
                                                    role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form class="form-valide" wire:submit='saveCotisation'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ $cot_id ? 'Modifier la cotisation' : 'Nouvelle cotisation' }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Branche <span class="text-danger">*</span></label>
                                                                        <div class="col-lg-6">
                                                                            <input type="text" wire:model='cot_intitule' class="form-control">
                                                                            @error('cot_intitule') <span class="text-error">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Organisme <span class="text-danger">*</span></label>
                                                                        <div class="col-lg-6">
                                                                            <select wire:model='cot_organisme' class="form-control">
                                                                                <option value="CNSS">CNSS</option>
                                                                                <option value="ANPE">ANPE</option>
                                                                            </select>
                                                                            @error('cot_organisme') <span class="text-error">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Taux salarié % <span class="text-danger">*</span></label>
                                                                        <div class="col-lg-6">
                                                                            <input type="text" wire:model='cot_taux_salarie' class="form-control">
                                                                            @error('cot_taux_salarie') <span class="text-error">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Taux patronal % <span class="text-danger">*</span></label>
                                                                        <div class="col-lg-6">
                                                                            <input type="text" wire:model='cot_taux_patronal' class="form-control">
                                                                            @error('cot_taux_patronal') <span class="text-error">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Plafond mensuel</label>
                                                                        <div class="col-lg-6">
                                                                            <input type="text" wire:model='cot_plafond' oninput="formatThousands(this)"
                                                                                class="form-control" placeholder="0 = sans plafond">
                                                                            @error('cot_plafond') <span class="text-error">{{ $message }}</span> @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Actif</label>
                                                                        <div class="col-lg-6">
                                                                            <input type="checkbox" wire:model='cot_actif'>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                                    <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
                    <!-- END PAGE CONTENT-->
                    @include('components.layouts.footer')
                </div>
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
            <script>
                function formatThousands(input) {
                    // Supprime les caractères non numériques (à l'exception des points décimaux)
                    let value = input.value.replace(/[^0-9.]/g, '');

                    // Ajoute des séparateurs de milliers
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

                    input.value = value;
                }
            </script>
        </div> {{-- fin .page-wrapper --}}
    </body>

</div>
