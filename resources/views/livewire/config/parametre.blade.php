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
                                        <div class="ibox-title">{{ __('menu.Parametre_generaux') }}</div>
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
                                                        {{ __('message.Initial_matricule_apprenant') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        @if($CountConfigApprenant <= 0)
                                                            <!-- Button trigger modal -->
                                                            <button type="button" class="btn btn-primary btn-xs m-r-5"
                                                            data-toggle="modal"
                                                            data-target="#AjouterInitialMatriculeApprenant"><i
                                                                class="fa fa-plus"></i>
                                                            {{ __('message.Ajouter') }}
                                                            </button>
                                                        
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Modal -->
                                                <div wire:ignore.self class="modal fade"
                                                    id="AjouterInitialMatriculeApprenant" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form wire:submit='addCongifMatriculeApprenant'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                                        {{ __('message.Ajouter') }}
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
                                                                            <input class="form-control" type="text"
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
                                                                            <input class="form-control" type="text"
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
                                                    <ul class="list-group list-group-bordered">
                                                        @if (!empty(Permission('Modifier')))
                                                            @foreach ($ConfigMatriculeApprenants as $ConfigMatriculeApprenant)
                                                                <button type="button" class="btn btn-info btn-xs m-r-5"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="editerConfigMatriculeApprenant({{ $ConfigMatriculeApprenant->id }})"
                                                                    data-target="#EditerInitialMatriculeApprenant">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeApprenant->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeApprenant->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach
                                                        @else
                                                            @foreach ($ConfigMatriculeApprenants as $ConfigMatriculeApprenant)
                                                                <button type="button"
                                                                    class="btn btn-info btn-xs m-r-5">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeApprenant->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeApprenant->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach

                                                        @endif
                                                    </ul>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeApprenant" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <form wire:submit='updateCongifMatriculeApprenant'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_apprenant') }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Initial_matricule_apprenant') }}</label>
                                                                                <input class="form-control" type="text" wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control" type="text" wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button  type="Submit"
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
                                                        {{ __('message.Initial_matricule_enseignant') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        @if($CountConfigEnseignant <= 0)
                                                            <!-- Button trigger modal -->
                                                            <button type="button" class="btn btn-primary btn-xs m-r-5"
                                                            data-toggle="modal"
                                                            data-target="#AjouterInitialMatriculeEnseignant"><i
                                                                class="fa fa-plus"></i>
                                                            {{ __('message.Ajouter') }}
                                                            </button>
                                                        
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Modal -->
                                                <div wire:ignore.self class="modal fade"
                                                    id="AjouterInitialMatriculeEnseignant" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form wire:submit='addCongifMatriculeEnseignant'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                                        {{ __('message.Ajouter') }}
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
                                                                            <label>{{ __('message.Initial_matricule_enseignant') }}</label>
                                                                            <input class="form-control" type="text"
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
                                                                            <input class="form-control" type="text"
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
                                                    <ul class="list-group list-group-bordered">
                                                        @if (!empty(Permission('Modifier')))
                                                            @foreach ($ConfigMatriculeEnseignants as $ConfigMatriculeEnseignant)
                                                                <button type="button" class="btn btn-info btn-xs m-r-5"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="editerConfigMatriculeEnseignant({{ $ConfigMatriculeEnseignant->id }})"
                                                                    data-target="#EditerInitialMatriculeEnseignant">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeEnseignant->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeEnseignant->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach
                                                        @else
                                                            @foreach ($ConfigMatriculeEnseignants as $ConfigMatriculeEnseignant)
                                                                <button type="button"
                                                                    class="btn btn-info btn-xs m-r-5">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeEnseignant->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeEnseignant->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach

                                                        @endif
                                                    </ul>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeEnseignant" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <form wire:submit='updateCongifMatriculeEnseignant'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_enseignant') }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Initial_matricule_enseignant') }}</label>
                                                                                <input class="form-control" type="text" wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control" type="text" wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button  type="Submit"
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
                                                        {{ __('message.Initial_matricule_employer') }}
                                                    </div>
                                                    <div class="ibox-tools">
                                                        @if($CountConfigEmployer <= 0)
                                                            <!-- Button trigger modal -->
                                                            <button type="button" class="btn btn-primary btn-xs m-r-5"
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
                                                    id="AjouterInitialMatriculeEmployer" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <form wire:submit='addCongifMatriculeEmployer'>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                                        {{ __('message.Ajouter') }}
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
                                                                            <input class="form-control" type="text"
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
                                                                            <input class="form-control" type="text"
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
                                                    <ul class="list-group list-group-bordered">
                                                        @if (!empty(Permission('Modifier')))
                                                            @foreach ($ConfigMatriculeEmployers as $ConfigMatriculeEmployer)
                                                                <button type="button" class="btn btn-info btn-xs m-r-5"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="editerConfigMatriculeEnseignant({{ $ConfigMatriculeEmployer->id }})"
                                                                    data-target="#EditerInitialMatriculeEmployer">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeEmployer->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeEmployer->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach
                                                        @else
                                                            @foreach ($ConfigMatriculeEmployers as $ConfigMatriculeEmployer)
                                                                <button type="button"
                                                                    class="btn btn-info btn-xs m-r-5">
                                                                    <li class="list-group-item">
                                                                        {{ $ConfigMatriculeEmployer->initiale }}
                                                                        <span
                                                                            class="badge badge-info float-right">{{ $ConfigMatriculeEmployer->nbr_caractere }}</span>
                                                                    </li>
                                                                </button>
                                                            @endforeach

                                                        @endif
                                                    </ul>
                                                    <!-- Modal editer-->
                                                    <div wire:ignore.self class="modal fade"
                                                        id="EditerInitialMatriculeEmployer" tabindex="-1"
                                                        role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <form wire:submit='updateCongifMatriculeEmployer'>
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                                            {{ __('message.Modifier') }}
                                                                            {{ __('message.Initial_matricule_employer') }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Initial_matricule_employer') }}</label>
                                                                                <input class="form-control" type="text" wire:model='initiale'>
                                                                                <div>
                                                                                    @error('initiale')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 form-group">
                                                                                <label>{{ __('message.Nombre_caractere_matricule') }}</label>
                                                                                <input class="form-control" type="text" wire:model='nbr_caractere'>
                                                                                <div>
                                                                                    @error('nbr_caractere')
                                                                                    <span class="text-error">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                                        <button  type="Submit"
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
            
        </div> {{-- fin .page-wrapper --}}
    </body>

</div>
