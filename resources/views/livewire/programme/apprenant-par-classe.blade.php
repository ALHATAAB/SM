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
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">{{ __('message.Liste_des_apprenant_par_classe') }}</div>
                                <div class="ibox-tools">
                                   ...
                                </div>
                            </div>
                            @include('livewire.alert.message')
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="ibox">
                                        <div class="ibox-body" style="background-color:darkgray; color:azure ">
                                            <p><strong> <i class="ti ti-filter"></i> Filtre</strong> </p>
                                            <div class="row">
                                                <div class="col-sm-12 form-group">
                                                    <label>{{ __('message.Classe_etude') }}</label>
                                                    <select class="form-control" wire:model.live='classe'>
                                                        <option value=""> </option>
                                                        @foreach ($classes as $classe)
                                                            <option value="{{ $classe->id }}">
                                                                {{ $classe->classe }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('classe')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="ibox">
                                        <div class="ibox-body">
                                            <div class="table-responsive">
                                                @if ($listeCount > 0)
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                {{-- <th width="50px"></th> --}}
                                                                <th>{{ __('message.Matricule') }}</th>
                                                                <th>{{ __('message.Nom') }}</th>
                                                                <th>{{ __('message.Prenom') }}</th>
                                                                <th>{{ __('message.Telephone') }}</th>
                                                                <th>{{ __('message.Niveau_etude') }}</th>
                                                                <th>{{ __('message.Filiere_etude') }}</th>
                                                                <th>{{ __('message.Classe_etude') }}</th>
                                                                <th>{{ __('message.Annee') }}</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($listeClasses as $listeClasse)
                                                                <tr>
                                                                     <td>{{ $listeClasse->matricule }}</td>
                                                                    <td>{{ $listeClasse->preInscription->nom }}</td>
                                                                    <td>{{ $listeClasse->preInscription->prenom }}</td>
                                                                    <td>{{ $listeClasse->preInscription->telephone }}</td>
                                                                    <td>{{ $listeClasse->niveau->niveau }}</td>
                                                                    <td>{{ $listeClasse->filiere->filiere }}</td>
                                                                    <td>{{ $listeClasse->classe->classe }}</td>
                                                                    <td>{{ $listeClasse->annee->annee }}</td>
                                                                   
                                                                </tr>
                                                            @endforeach
                                                           
                                                        </tbody>
                                                        
                                                    </table>
                                                     <button class="btn btn-secondary" wire:click="recuPrint({{ $classe_id }})">imprimer</button>
                                                @else
                                                    <div class="alert alert-warning alert-bordered-right"><strong>Alert
                                                            ! </strong> Pas de donnée.</div>
                                                @endif
                                                <!-- Liens de pagination -->
                                                <div>
                                                    {{-- {{ $filiere_etudes->links() }} --}}


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
