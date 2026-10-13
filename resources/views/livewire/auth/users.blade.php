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
                                <div class="ibox-title">{{ __('message.Liste_utilisateur') }}</div>
                                <div class="ibox-tools">
                                    <!-- Button trigger modal -->
                                    @if (!empty(Permission('Ajouter')))
                                        <button type="button" class="btn btn-ligth" data-toggle="modal"
                                            data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <!-- Modal -->
                            <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addUser'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Ajouter_utilisateur') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Prenom') }}</label>
                                                        <input class="form-control" type="text" wire:model='prenom'>
                                                        <div>
                                                            @error('prenom')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Nom') }}</label>
                                                        <input class="form-control" type="text" wire:model='nom'>
                                                        <div>
                                                            @error('nom')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Adresse') }}</label>
                                                    <input class="form-control" type="text" wire:model='adresse'>
                                                    <div>
                                                        @error('adresse')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Telephone') }}</label>
                                                    <input class="form-control" type="text" wire:model='telephone'>
                                                    <div>
                                                        @error('telephone')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Email') }}</label>
                                                    <input class="form-control" type="text" wire:model='email'>
                                                    <div>
                                                        @error('email')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Password') }}</label>
                                                    <input class="form-control" type="password" wire:model='password'>
                                                    <div>
                                                        @error('password')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('message.Role') }}</label>
                                                    <select class="form-control" wire:model='role_id'>
                                                        <option value=""> </option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}">{{ $role->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('role')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
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
                                <form class="form-inline">
                                    <label class="sr-only" for="ex-email">{{ __('message.Nom') }}</label>
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                        wire:model.live='rechercheNom'
                                        placeholder="{{ __('message.Recherche') }} {{ __('message.Nom') }} ">
                                    <label class="sr-only" for="ex-pass">{{ __('message.Prenom') }}</label>
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                        wire:model.live='recherchePrenom'
                                        placeholder="{{ __('message.Recherche') }} {{ __('message.Prenom') }} ">
                                    <label class="sr-only" for="ex-pass">{{ __('message.Telephone') }}</label>
                                    <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                        wire:model.live='rechercheTelephone'
                                        placeholder="{{ __('message.Recherche') }} {{ __('message.Telephone') }}">
                                </form>
                                <div class="table-responsive">
                                    @include('livewire.alert.message')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                {{-- <th width="50px"></th> --}}
                                                <th>{{ __('message.Nom') }}</th>
                                                <th>{{ __('message.Prenom') }}</th>
                                                <th>{{ __('message.Telephone') }}</th>
                                                <th>{{ __('message.Email') }}</th>
                                                <th>{{ __('message.Role') }}</th>
                                                <th>{{ __('message.Etat') }}</th>
                                                @if (
                                                    !empty(Permission('Valider')) ||
                                                        !empty(Permission('Modifier')) ||
                                                        !empty(Permission('Consulter')) ||
                                                        !empty(Permission('Supprimer')))
                                                    <th>{{ __('message.Actions') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->nom }}</td>
                                                    <td>{{ $user->prenom }}</td>
                                                    <td>{{ $user->telephone }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->role->nom }}</td>
                                                    @if ($user->etat == 0)
                                                        <td><span class="badge badge-danger">Inactif</span></td>
                                                    @else
                                                        <td><span class="badge badge-success">Actif</span></td>
                                                    @endif
                                                    <td>
                                                        @if (!empty(Permission('Valider')))
                                                            @if ($user->etat == 0)
                                                                <button type="button" data-toggle="modal"
                                                                    data-target=".activerCpt"
                                                                    wire:click.prevent="activer({{ $user->id }})"
                                                                    class="btn btn-sm  btn-success"><i
                                                                        class="feather fa fa-check-circle"></i></button>
                                                            @else
                                                                <button type="button" data-toggle="modal"
                                                                    data-target=".desactiverCpt"
                                                                    wire:click.prevent="desactiver({{ $user->id }})"
                                                                    class="btn btn-sm  btn-danger"><i
                                                                        class="feather fa fa-times-circle-o"></i></button>
                                                            @endif
                                                        @endif
                                                        @if (!empty(Permission('Modifier')))
                                                            <button type="button"
                                                                wire:click.prevent="editUser({{ $user->id }})"
                                                                class="btn btn-sm  btn-info" data-toggle="modal"
                                                                data-target="#editUser"
                                                                data-whatever="@getbootstrap"><i
                                                                    class="feather fa fa-edit"></i></button>
                                                        @endif
                                                        @if (!empty(Permission('Valider')))
                                                            <button type="button" data-toggle="modal"
                                                                data-target=".initialiserMdp"
                                                                wire:click.prevent='initialisationMdp({{ $user->id }})'
                                                                class="btn btn-sm btn-warning"><i
                                                                    class="feather ti ti-help-alt "></i></button>
                                                        @endif
                                                        @if (!empty(Permission('Consulter')))
                                                            <button type="button"
                                                                wire:click.prevent="showUser({{ $user->id }})"
                                                                data-toggle="modal" data-target="#showUser"
                                                                data-whatever="@getbootstrap"
                                                                class="btn btn-sm btn-secondary"><i
                                                                    class="feather fa fa-eye"></i></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <!-- Liens de pagination -->
                                    <div>
                                        {{ $users->links() }}
                                    </div>
                                    {{-- Modal activer compte --}}
                                    <div wire:ignore.self class="modal fade activerCpt" id="#" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-success">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmActive'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-success alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous activer ce compte ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-success">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>


                                    {{-- Modal desactiver compte --}}
                                    <div wire:ignore.self class="modal fade desactiverCpt" id="#"
                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-danger">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmDesactive'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous desactiver ce compte ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-danger">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    {{-- Modal editUser --}}
                                    <div wire:ignore.self class="modal fade" id="editUser" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='updateUser'>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('message.Modifier_utilisateur') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Prenom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='prenom'>
                                                                <div>
                                                                    @error('prenom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                <label>{{ __('message.Nom') }}</label>
                                                                <input class="form-control" type="text"
                                                                    wire:model='nom'>
                                                                <div>
                                                                    @error('nom')
                                                                        <span
                                                                            class="text-error">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Adresse') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='adresse'>
                                                            <div>
                                                                @error('adresse')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Telephone') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='telephone'>
                                                            <div>
                                                                @error('telephone')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('message.Email') }}</label>
                                                            <input class="form-control" type="text"
                                                                wire:model='email'>
                                                            <div>
                                                                @error('email')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ __('message.Role') }}</label>
                                                            <select class="form-control" wire:model='role_id'>
                                                                <option value=""> </option>
                                                                @foreach ($roles as $role)
                                                                    <option value="{{ $role->id }}">
                                                                        {{ $role->nom }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div>
                                                                @error('role')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-primary">{{ __('message.Modifier') }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Modal initialisation mdp compte --}}
                                    <div wire:ignore.self class="modal fade initialiserMdp" id="#"
                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn  btn-icon btn-warning">
                                                        <i class="feather fa fa-exclamation-triangle"></i> Alert
                                                    </button>
                                                </div>
                                                <form wire:submit='confirmMdp'>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning alert-dismissable fade show "
                                                            style="text-align:center">

                                                            <p>
                                                                Voulez vous initialiser le mots de passe ?
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                        <button type="Submit"
                                                            class="btn btn-warning">{{ __('message.Valider') }}</button>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                    {{-- Modal showUser --}}
                                    <div wire:ignore.self class="modal fade" id="showUser" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Utilisateur :
                                                        {{ $nom .
                                                            '
                                                                                                            ' .
                                                            $prenom }}
                                                    </h5>
                                                    {{-- <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                --}}
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="floating-label" for="nom">Nom : </label>
                                                        <span>{{ $nom }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="floating-label" for="prenom">Prenom : </label>
                                                        <span>{{ $prenom }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="floating-label" for="telephone">Telephone :
                                                        </label>
                                                        <span>{{ $telephone }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="floating-label" for="adresse">Adresse :
                                                        </label>
                                                        <span>{{ $adresse }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="floating-label" for="email">Email : </label>
                                                        <span>{{ $email }}</span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-dismiss="modal">Fermer</button>
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
                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
        </div>
        <!-- BEGIN PAGA BACKDROPS-->
        {{-- @include('components.layouts.loading') --}}
        <!-- END PAGA BACKDROPS-->
    </body>
</div>
