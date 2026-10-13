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

                    <div class="row">
                        <div class="col-lg-8 col-md-8">
                            <div class="ibox">
                                <div class="ibox-head ibox ibox-danger">
                                    <div class="ibox-title">{{ __('message.Etat_calculer') }}</div>
                                    <div class="ibox-tools">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-cogs"></i> Mois de paie <i
                                                    class="fa fa-angle-down"></i></button>
                                            <ul class="dropdown-menu">
                                                @foreach ($etat_mois as $etat_moi)
                                                    <li>
                                                        <a href="#" class="dropdown-item" role="button"
                                                            tabindex="0"
                                                            wire:click.prevent="paieDuMois({{ $etat_moi->id }})">{{ $etat_moi->mois }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-body">
                                    @if ($countEtatsPaie > 0)
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">{{ __('message.Matricule') }}</th>
                                                        <th width="100px">{{ __('message.Nom') }}</th>
                                                        <th width="100px">{{ __('message.Prenom') }}</th>
                                                        <th width="100px">{{ __('message.Poste') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Solde') }}</th>
                                                        <th width="100px">{{ __('message.Mois') }}</th>
                                                        <th width="100px">{{ __('message.Annee') }}</th>
                                                        <th width="80px">{{ __('message.Statut') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($etatsPaies as $etatsPaie)
                                                        <tr>
                                                            <td>{{ $etatsPaie->employer->matricule }}</td>
                                                            <td>{{ $etatsPaie->employer->nom }}</td>
                                                            <td>{{ $etatsPaie->employer->prenom }}</td>
                                                            <td>{{ $etatsPaie->employer->poste }}</td>
                                                            <td>
                                                                {{ number_format($etatsPaie->salaire_net, 0, ',', ' ') }}
                                                            </td>
                                                            <td>{{ $etatsPaie->mois->mois }}</td>
                                                            <td>{{ $etatsPaie->annee->annee }}</td>
                                                            <td>{{ $etatsPaie->statuts }}</td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>

                                                <tfoot class="table-secondary">
                                                    <tr>
                                                        <th colspan="4">TOTAL</th>
                                                        <th>{{ number_format($totalPaie ?? 0, 0, ',', ' ') }} CFA</th>
                                                        <th colspan="3">...</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <h4 class="m-t-20">404</h4>
                                            <p class="error-title">PAGE NOT FOUND</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            @include('livewire.alert.message')
                            <div class="ibox">
                                <div class="ibox-body">
                                    <button class="btn btn-success btn-block " data-toggle="modal"
                                        data-target="#CalculEtat">Calcul paie</button>
                                    <button class="btn btn-danger btn-block"data-toggle="modal"
                                        data-target="#EditerEtat">Editer etat</button>
                                    <button class="btn btn-info btn-block"data-toggle="modal"
                                        data-target="#EditerBulletin">Editer bulletin</button>
                                    {{-- <button class="btn btn-primary btn-block">VIREMENT</button> --}}
                                </div>
                            </div>

                            <!-- Modal calcul etat-->
                            <div wire:ignore.self class="modal fade" id="CalculEtat" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addCalculEtat'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Calcul_Paie') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Mois_paie') }}</label>
                                                    <select class="form-control" wire:model='mois_paie'>
                                                        <option value=""> </option>
                                                        @foreach ($mois_paies as $mois_paie)
                                                            <option value="{{ $mois_paie->id }}">
                                                                {{ $mois_paie->mois }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('mois_paie')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                <button type="Submit"
                                                    class="btn btn-primary">{{ __('message.Valider') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal editer etat-->
                            <div wire:ignore.self class="modal fade" id="EditerEtat" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addEditerEtat'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Editer_etat') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Mois_paie') }}</label>
                                                    <select class="form-control" wire:model='mois_paie'>
                                                        <option value=""> </option>
                                                        @foreach ($mois_paies as $mois_paie)
                                                            <option value="{{ $mois_paie->id }}">
                                                                {{ $mois_paie->mois }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('mois_paie')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                <button type="Submit"
                                                    class="btn btn-primary">{{ __('message.Editer') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal editer bulletin-->
                            <div wire:ignore.self class="modal fade" id="EditerBulletin" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form wire:submit='addEditerBulletin'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ __('message.Editer_bulletin') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('message.Mois_paie') }}</label>
                                                    <select class="form-control" wire:model='mois_paie'>
                                                        <option value=""> </option>
                                                        @foreach ($mois_paies as $mois_paie)
                                                            <option value="{{ $mois_paie->id }}">
                                                                {{ $mois_paie->mois }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('mois_paie')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('message.Fermer') }}</button>
                                                <button type="Submit"
                                                    class="btn btn-primary">{{ __('message.Editer') }}</button>
                                            </div>
                                        </div>
                                    </form>
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
