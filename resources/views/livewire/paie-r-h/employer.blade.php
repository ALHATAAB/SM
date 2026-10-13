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

                    <div class="ibox ibox-primary">

                        <div class="ibox-head ibox ibox-danger">
                            <div class="ibox-title">{{ __('message.Liste_employer') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                @if(!empty(Permission('Ajouter')))
                                <button type="button" class="btn btn-ligth" data-toggle="modal"
                                    data-target="#exampleModalCenter"><i class="fa fa-plus"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <!-- Modal -->
                        <div wire:ignore.self class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form wire:submit='addEmployer'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                {{ __('message.Ajouter_employer') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>{{ __('message.Matricule')}}</label>
                                                    <input class="form-control" type="text" wire:model='matricule' readonly>
                                                    <div>
                                                        @error('matricule')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 form-group">
                                                    <label>{{ __('message.Prenom') }}</label>
                                                    <input class="form-control" type="text" wire:model='prenom'>
                                                    <div>
                                                        @error('prenom')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-5 form-group">
                                                    <label>{{ __('message.Nom') }}</label>
                                                    <input class="form-control" type="text" wire:model='nom'>
                                                    <div>
                                                        @error('nom')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Date_naissance') }}</label>
                                                    <input class="form-control" type="date" wire:model='date_naissance'>
                                                    <div>
                                                        @error('date_naissance')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Lieu_naissance') }}</label>
                                                    <input class="form-control" type="text" wire:model='lieu_naissance'>
                                                    <div>
                                                        @error('lieu_naissance')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Sexe') }} </label>
                                                <select class="form-control" wire:model="sexe">
                                                    <option value=""></option>
                                                    <option value="Masculin">Masculin</option>
                                                    <option value="Feminin">Feminin</option>
                                                </select>
                                            <div>
                                                @error('sexe')
                                                <span class="text-error">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    @include('livewire._champ-photo')
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Adresse') }}</label>
                                                    <input class="form-control" type="text" wire:model='adresse'>
                                                    <div>
                                                        @error('adresse')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Email') }}</label>
                                                    <input class="form-control" type="email" wire:model='email'>
                                                    <div>
                                                        @error('email')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Telephone') }}</label>
                                                    <input class="form-control" type="text" wire:model='telephone'>
                                                    <div>
                                                        @error('telephone')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Personnes_a_charge') }}</label>
                                                    <input class="form-control" type="number" min="0" placeholder="0" wire:model='charge_familliale'>
                                                    <div>
                                                        @error('charge_familliale')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('message.Poste') }}</label>
                                                <input class="form-control" type="text" wire:model='poste'>
                                                <div>
                                                    @error('poste')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Salaire_base') }}</label>
                                                    <input class="form-control" type="text" wire:model='salaire'>
                                                    <div>
                                                        @error('salaire')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                                <div class="col-sm-6 form-group">
                                                    <label>{{ __('message.Date_embauche') }}</label>
                                                    <input class="form-control" type="date" wire:model='date_embauche'>
                                                    <div>
                                                        @error('date_embauche')
                                                        <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <label class="ui-checkbox ui-checkbox-primary mr-auto mb-0">
                                                <input type="checkbox" wire:model="generer_carte">
                                                <span class="input-span"></span>Générer la carte professionnelle</label>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{
                                                __('message.Fermer') }}</button>
                                            <button type="Submit" class="btn btn-primary">{{ __('message.Enregistrer')
                                                }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @include('livewire.alert.message')
                        <div class="ibox-body">
                            <form class="form-inline">
                                <label class="sr-only" for="ex-email">{{ __('message.Nom') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-email" type="text"
                                wire:model.live='rechercheNom'    placeholder="{{ __('message.Recherche') }} {{ __('message.Nom') }} ">
                                <label class="sr-only" for="ex-pass">{{ __('message.Prenom') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                wire:model.live='recherchePrenom'     placeholder="{{ __('message.Recherche') }} {{ __('message.Prenom') }} ">
                                <label class="sr-only" for="ex-pass">{{ __('message.Telephone') }}</label>
                                <input class="mb-2 form-control mr-sm-2 mb-sm-0" id="ex-pass" type="text"
                                wire:model.live='rechercheTelephone'     placeholder="{{ __('message.Recherche') }} {{ __('message.Telephone') }}">
                            </form>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="50px">Code</th>
                                            <th>{{ __('message.Nom') }}</th>
                                            <th>{{ __('message.Prenom') }}</th>
                                            <th>{{ __('message.Telephone') }}</th>
                                            <th>{{ __('message.Adresse') }}</th>
                                            @if(  !empty(Permission('Consulter'))  )
                                            <th>{{ __('message.Actions') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employers as $employer )
                                        <tr>
                                            <td>
                                                
                                                    {{ $employer->matricule }}
                                               
                                            </td>
                                            <td>{{ $employer->nom }}</td>
                                            <td>{{ $employer->prenom }}</td>
                                            <td>{{ $employer->telephone }}</td>
                                            <td>{{ $employer->adresse }}</td>
                                            @if(  !empty(Permission('Consulter'))  )
                                            <td>
                                                <a href="{{url('/detail/employer/'.encrypt($employer->id))}}"> <button type="button" class="btn btn-ligth" ><i class="ti ti-eye"></i></button></a>
                                            <a href="{{ url('/fiche/employer/' . encrypt($employer->id)) }}" target="_blank" class="btn btn-warning btn-sm" title="Fiche complète (PDF)">
                                                <i class="ti ti-printer"></i>
                                            </a>
                                            @if ($carte = $cartes[$employer->id] ?? null)
                                                <a href="{{ url('/carte/imprimer/' . encrypt($carte->id)) }}" target="_blank" class="btn btn-info btn-sm"
                                                    title="Imprimer la carte professionnelle {{ $carte->numero }}"><i class="fa fa-id-card"></i></a>
                                            @elseif (!empty(Permission('Ajouter')))
                                                <button type="button" wire:click="genererCarte({{ $employer->id }})" wire:loading.attr="disabled"
                                                    class="btn btn-success btn-sm" title="Générer la carte professionnelle"><i class="fa fa-id-card"></i> +</button>
                                            @endif
                                            </td>
                                            @endif
                                        </tr> 
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                                <div>
                                    {{ $employers->links() }}
                                </div>
                            </div>
                        </div>
                    </div>


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
