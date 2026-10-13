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
                            <div class="ibox-title">{{ __('message.Achat_livre') }}</div>
                            <div class="ibox-tools">
                                <a href="{{ url('/livre/acheter/') }}" >
                                    <button type="button" class="btn btn-ligth" ><i class="fa fa-arrow-left"></i> Retour à la liste
                                    </button>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-body">

                            <div>
                                 @include('livewire.alert.message')

                                <h4 class="mb-4">{{ __('Nouvel achat') }}</h4>

                                <form wire:submit.prevent="save">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">{{ __('Fournisseur') }} :</label>
                                        <div class="col-sm-10">
                                            <input type="text" wire:model="fournisseur" class="form-control"
                                                placeholder="{{ __('Nom du fournisseur') }}">
                                            @error('fournisseur')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr>

                                    <h5>{{ __('Lignes d\'achat') }}</h5>

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
                                                        <select wire:model.live="lines.{{ $index }}.livre_id"
                                                            class="form-control">
                                                            <option value="">{{ __('-- Livre --') }}</option>
                                                            @foreach ($livres as $livre)
                                                                <option value="{{ $livre->id }}">
                                                                    {{ $livre->titre }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error("lines.$index.livre_id")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1"
                                                            wire:model.live="lines.{{ $index }}.quantite"
                                                            class="form-control" placeholder="{{ __('Quantité') }}">
                                                        @error("lines.$index.quantite")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.01"
                                                            wire:model="lines.{{ $index }}.prix_unitaire"
                                                            class="form-control"
                                                            placeholder="{{ __('Prix unitaire') }}">
                                                        @error("lines.$index.prix_unitaire")
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
@php
    $qte = (float) ($line['quantite'] ?? 0);
    $pu  = (float) ($line['prix_unitaire'] ?? 0);
    $total = $qte * $pu;
@endphp

<input type="text" readonly class="form-control-plaintext"
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
                                        <button type="button" wire:click="addLine" class="btn btn-primary btn-sm">
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

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> {{ __('Enregistrer l\'achat') }}
                                        </button>
                                    </div>
                                </form>
                            </div>



                            {{-- <div>
                                <h4 class="mb-4">{{ __('Nouvel achat') }}</h4>
                                @include('livewire.alert.message')
                                <form wire:submit.prevent="save">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Fournisseur :</label>
                                        <div class="col-sm-10">
                                            <input type="text" wire:model="fournisseur" class="form-control"
                                                placeholder="Nom du fournisseur">
                                        </div>
                                    </div>

                                    <hr>

                                    <h5>Lignes d'achat</h5>

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
                                            <i class="fa fa-save"></i> Enregistrer l'achat
                                        </button>
                                    </div>
                                </form>
                            </div> --}}


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
