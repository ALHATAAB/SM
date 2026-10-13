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
                            <div class="ibox-title">{{ __('message.Vente_livre') }}</div>
                            <div class="ibox-tools">
                                <!-- Button trigger modal -->
                                {{-- @if (!empty(Permission('Ajouter')))
                                    <a href="{{ url('/nouvel/vente/') }}">
                                        <button type="button" class="btn btn-ligth"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                @endif --}}
                            </div>
                        </div>
                        @include('livewire.alert.message')
                        <div class="ibox-body">

                            <h4 class="mb-4">Détail de la vente #{{ $vente->id }}</h4>

                            <div class="card mb-4">
                                <div class="card-body">
                                    <p><strong>Date :</strong> {{ Carbon\Carbon::parse($vente->date_comptable)->format('d/m/Y') }}</p>
                                    <p><strong>Client :</strong> {{ $vente->client ?? '-' }}</p>
                                 
                                      <p><strong>Total général:</strong> {{ number_format($vente->remise+$vente->montant_total,0, ',', ' ') }}
                                        F CFA</p>
                                        <p><strong>Remise:</strong> {{ number_format($vente->remise,0, ',', ' ') }}
                                        F CFA</p>
                                        <p><strong>Payer :</strong> {{ number_format($vente->montant_total,0, ',', ' ') }}
                                        F CFA</p>
                                    
                                     <p><strong>Mode paiement :</strong> {{ $vente->mode_paiement ?? '-' }}</p>
                                     {{-- <p><strong>Reference paiement :</strong> {{ $vente->mode_paiement ?? '-' }}</p> --}}
                                </div>
                            </div>

                            <h5>Lignes d’vente</h5>

                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Livre</th>
                                        <th>Quantité</th>
                                        <th style="text-align: right" >Prix unitaire (F CFA)</th>
                                        <th style="text-align: right">Total ligne (F CFA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vente->ligneVentes as $line)
                                        <tr>
                                            <td>{{ $line->livre->titre ?? '-' }}</td>
                                            <td>{{ $line->quantite }}</td>
                                            <td style="text-align: right">{{ number_format($line->prix_unitaire, 0, ',', ' ') }}</td>
                                            <td style="text-align: right">{{ number_format($line->prix_total,0, ',', ' ') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune ligne d’vente.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <a href="{{ url('/livre/vendu/') }}" class="btn btn-secondary mt-3">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                            </a>
                             @if ($vente->statuts!="annuler")
                                @if (!empty(Permission('Supprimer')))
                                    <button wire:click="annuler" class="btn btn-danger mt-3" onclick="return confirm('Confirmer l\'annulation ?')">
                                        <i class="fa fa-times"></i> Annuler cet vente
                                    </button>
                                @endif
                            @else
                                <div class="alert alert-warning mt-3">
                                    Cet vente a déjà été annulé.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>


            </div>
            <!-- END PAGE CONTENT-->
            @include('components.layouts.footer')
        </div>
<!-- BEGIN PAGA BACKDROPS-->
@include('components.layouts.loading')
<!-- END PAGA BACKDROPS-->

</body>
</div>
