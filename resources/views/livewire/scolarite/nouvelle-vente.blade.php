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
                    @if ($countCaisse > 0)
                        <div class="ibox ibox-primary">

                            <div class="ibox-head ibox ibox-danger">
                                <div class="ibox-title">{{ __('message.Vente_livre') }}</div>
                                <div class="ibox-tools">
                                    <a href="{{ url('/livre/vendu/') }}">
                                        <button type="button" class="btn btn-ligth"><i class="fa fa-arrow-left"></i>
                                            Retour
                                            à la liste
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-body">

                                <div>
                                    @include('livewire.alert.message')

                                    @if ($caisse->etat == 'ouverte')
                                        @if ($caisse->date_comptable == JourneeOuverte()?->date_comptable)
                                            <h4 class="mb-4">{{ __('Nouvelle vente') }}</h4>

                                            <form wire:submit.prevent="save">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">{{ __('Client') }} :</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" wire:model="client" class="form-control"
                                                            placeholder="{{ __('Nom du client') }}">
                                                        @error('client')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <hr>

                                                <h5>{{ __('Lignes de vente') }}</h5>

                                                <table class="table table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>{{ __('Livre') }}</th>
                                                            <th>{{ __('Quantité') }}</th>
                                                            <th>{{ __('Prix unitaire') }}</th>
                                                            <th>{{ __('Total ligne') }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($lines as $index => $line)
                                                            <tr>
                                                                <td>
                                                                    <select
                                                                        wire:model.live="lines.{{ $index }}.livre_id"
                                                                        class="form-control">
                                                                        <option value="">{{ __('-- Livre --') }}
                                                                        </option>
                                                                        @foreach ($livres as $livre)
                                                                            <option value="{{ $livre->id }}">
                                                                                {{ $livre->titre }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error("lines.$index.livre_id")
                                                                        <small
                                                                            class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="1"
                                                                        wire:model.live="lines.{{ $index }}.quantite"
                                                                        class="form-control"
                                                                        placeholder="{{ __('Quantité') }}">
                                                                    @error("lines.$index.quantite")
                                                                        <small
                                                                            class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="0" step="0.01"
                                                                        wire:model="lines.{{ $index }}.prix_unitaire"
                                                                        class="form-control"
                                                                        placeholder="{{ __('Prix unitaire') }}">
                                                                    @error("lines.$index.prix_unitaire")
                                                                        <small
                                                                            class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $qte = (float) ($line['quantite'] ?? 0);
                                                                        $pu = (float) ($line['prix_unitaire'] ?? 0);
                                                                        $total = $qte * $pu;
                                                                    @endphp

                                                                    <input type="text" readonly
                                                                        class="form-control-plaintext"
                                                                        value="{{ number_format($total, 2) }} F CFA">
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        wire:click="removeLine({{ $index }})"
                                                                        class="btn btn-danger btn-sm">&times;</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                                <div class="form-group">
                                                    <button type="button" wire:click="addLine"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa fa-plus"></i> {{ __('Ajouter une ligne') }}
                                                    </button>
                                                </div>

                                                <hr>

                                                <div class="form-group row">
                                                    <label
                                                        class="col-sm-8 col-form-label font-weight-bold">{{ __('Total général') }}
                                                        :</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" readonly
                                                            class="form-control-plaintext font-weight-bold text-success"
                                                            value="{{ number_format($this->total, 2) }} F CFA">
                                                    </div>
                                                </div>
                                               
                                                <hr>

                                                <div class="row">
                                                    
                                                    <div class=" col-sm-3 form-group ">
                                                       <label>{{ __('message.Remise') }} </label>
                                                        <input class="form-control"  type="text" wire:model="remise">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>{{ __('message.Type_reglement') }} <span
                                                                style="color:red">*</span></label>
                                                        <select class="form-control"
                                                            wire:model.live.debounce.900ms="mode_paiement">
                                                            <option value=""> </option>
                                                            @foreach ($mode_paiements as $mode_paiement)
                                                                <option value="{{ $mode_paiement->mode_paiement }}">
                                                                    {{ $mode_paiement->mode_paiement }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('mode_paiement')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    @if ($mode_paie == 'DEPOT' || $mode_paie == 'TRANSFERT' || $mode_paie == 'CHEQUE')
                                                        <div class="col-sm-3 form-group">
                                                            <label>{{ __('message.Banque') }}
                                                                {{ $mode_paie }}<span
                                                                    style="color:red">*</span></label>
                                                            <select class="form-control" wire:model="banque">
                                                                <option value=""> </option>
                                                                @foreach ($banques as $banque)
                                                                    <option value="{{ $banque->id }}">
                                                                        {{ $banque->banque }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div>
                                                                @error('banque')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>{{ __('message.Numero') }}
                                                                {{ $mode_paie }}<span
                                                                    style="color:red">*</span></label>
                                                            <input class="form-control" type="text"
                                                                wire:model="coordonnee_paiement">
                                                            <div>
                                                                @error('coordonnee_paiement')
                                                                    <span class="text-error">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                    @endif
                                                </div>
                                                
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-save"></i> {{ __('Enregistrer la vente') }}
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-danger alert-bordered"><strong> Attention !
                                                </strong> Regularisé la date comptable
                                                .</div>
                                        @endif
                                    @else
                                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong>
                                            Caisse
                                            Fermer.</div>
                                    @endif
                                </div>



                                {{-- <div>
                                <h4 class="mb-4">{{ __('Nouvel vente') }}</h4>
                                @include('livewire.alert.message')
                                <form wire:submit.prevent="save">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">client :</label>
                                        <div class="col-sm-10">
                                            <input type="text" wire:model="client" class="form-control"
                                                placeholder="Nom du client">
                                        </div>
                                    </div>

                                    <hr>

                                    <h5>Lignes d'vente</h5>

                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Livre</th>
                                                <th>Quantité</th>
                                                <th>Prix unitaire</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lines as $index => $line)
                                                <tr>
                                                    <td>
                                                        <select wire:model.live="lines.{{ $index }}.livre_id"
                                                            class="form-control">
                                                            <option value="">-- Livre --</option>
                                                            @foreach ($livres as $livre)
                                                                <option value="{{ $livre->id }}">{{ $livre->titre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1"
                                                            wire:model="lines.{{ $index }}.quantite"
                                                            class="form-control" placeholder="Quantité">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.01"
                                                            wire:model="lines.{{ $index }}.prix_unitaire"
                                                            class="form-control" placeholder="Prix unitaire">
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            wire:click="removeLine({{ $index }})"
                                                            class="btn btn-danger btn-sm">&times;</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="form-group">
                                        <button type="button" wire:click="addLine" class="btn btn-primary btn-sm">
                                            <i class="fa fa-plus"></i> Ajouter une ligne
                                        </button>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> Enregistrer l'vente
                                        </button>
                                    </div>
                                </form>
                            </div> --}}


                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez pas de
                            caisse.</div>
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
