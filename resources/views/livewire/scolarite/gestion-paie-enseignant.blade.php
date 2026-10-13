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
                        <div class="col-lg-12 col-md-12">
                            <div class="ibox">
                                <div class="ibox-head ibox ibox-danger">
                                    <div class="ibox-title">{{ __('message.Etat_paie_enseignant') }}</div>
                                    <div class="ibox-tools">
                                        <div class="btn-group">
                                            <button class="btn btn-success btn-block " data-toggle="modal"
                                                data-target="#CalculEtat">Calcul paie enseignant</button>
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
                                    <!-- Modal calcul etat-->
                                    <div wire:ignore.self class="modal fade" id="CalculEtat" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <form wire:submit='addCalculPaieEnseignant'>
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
                                </div>
                                <div class="ibox-body">
                                    @include('livewire.alert.message')
                                    @if ($countEtatsPaie > 0)

                                        <div class="table-responsive">
                                            <button class="btn btn-secondary"
                                                wire:click.prevent="printEtatPaie({{ $mois_id }})">
                                                Imprimer</button>
                                            <button class="btn btn-primary" wire:loading.attr="disabled"
                                                wire:click.prevent="imprimerBulletins({{ $mois_id }})">
                                                <i class="fa fa-print"></i> Bulletins de paie</button>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">{{ __('message.Matricule') }}</th>
                                                        <th width="100px">{{ __('message.Nom') }}</th>
                                                        <th width="100px">{{ __('message.Prenom') }}</th>
                                                        <th width="100px">{{ __('message.Nombre_heure') }}</th>
                                                        <th width="100px">{{ __('message.Salaire_fixe') }}</th>
                                                        <th width="100px">{{ __('message.Salaire_horaire') }}</th>
                                                        {{-- <th width="100px">{{ __('message.Poste') }}</th> --}}
                                                        <th width="150px" style="text-align:right">
                                                            Total brut</th>
                                                            <th width="110px" style="text-align:right">Retenue ITS</th>
                                                            <th width="110px" style="text-align:right">Autres retenues / Avance</th>
                                                            <th width="130px" style="text-align:right">{{ __('message.Salaire_net') }}</th>
                                                        <th width="100px">{{ __('message.Mois') }}</th>
                                                        <th width="100px">{{ __('message.Annee') }}</th>
                                                        <th width="80px">{{ __('message.Statut') }}</th>
                                                        <th width="90px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($etatsPaies as $etatsPaie)
                                                        <tr>
                                                            <td>{{ $etatsPaie->enseignant->matricule }}</td>
                                                            <td>{{ $etatsPaie->enseignant->nom }}</td>
                                                            <td>{{ $etatsPaie->enseignant->prenom }}</td>
                                                            <td>{{ $etatsPaie->heures_sup }}</td>
                                                            <td>{{ $etatsPaie->montant_base }}</td>
                                                            <td>{{ $etatsPaie->heures_sup > 0 ? $etatsPaie->montant_heures_sup / $etatsPaie->heures_sup : 0 }}
                                                            </td>
                                                            {{-- <td>{{ $etatsPaie->enseignant->poste }}</td> --}}
                                                            <td style="text-align:right">
                                                                {{ number_format($etatsPaie->total_paye, 0, ',', ' ') }}
</td>
                                                                @php
                                                                    $rIts    = $retenues[$etatsPaie->enseignant_id]['its'] ?? 0;
                                                                    $rAutres = $retenues[$etatsPaie->enseignant_id]['autres'] ?? 0;
                                                                @endphp
                                                                <td style="text-align:right">{{ number_format($rIts, 0, ',', ' ') }}</td>
                                                                <td style="text-align:right">{{ number_format($rAutres, 0, ',', ' ') }}</td>
                                                                <td style="text-align:right"><strong>{{ number_format($etatsPaie->total_paye - $rIts - $rAutres, 0, ',', ' ') }}</strong></td>
                                                            <td>{{ $etatsPaie->mois->mois }}</td>
                                                            <td>{{ $etatsPaie->annee }}</td>
                                                            <td>{{ $etatsPaie->statut }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" wire:loading.attr="disabled"
                                                                    wire:click.prevent="imprimerBulletin({{ $etatsPaie->id }})">
                                                                    <i class="fa fa-file-pdf-o"></i> Bulletin</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                                <tfoot class="table-secondary">
                                                    <tr>
                                                        <th colspan="6">TOTAL</th>
                                                        <th style="text-align:right">
                                                            {{ number_format($totalPaie ?? 0, 0, ',', ' ') }}</th>
                                                            <th style="text-align:right">{{ number_format($totalIts ?? 0, 0, ',', ' ') }}</th>
                                                            <th style="text-align:right">{{ number_format($totalAutres ?? 0, 0, ',', ' ') }}</th>
                                                            <th style="text-align:right">{{ number_format($totalNet ?? 0, 0, ',', ' ') }}</th>
                                                        <th colspan="4">...</th>
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
