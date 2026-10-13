<div>
    <body class="fixed-navbar">
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
                            <div class="ibox-title">
                                Gestion du stock des livres
                            </div>
                            <div class="ibox-tools">
                                ...
                            </div>
                        </div>
                        <div class="ibox-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" wire:model.live.debounce.500ms="searchTitre"
                                        class="form-control" placeholder="Rechercher par titre...">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" wire:model.live="dateDebut" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" wire:model.live="dateFin" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <button wire:click="exportPdf" class="btn btn-danger w-100">
                                        📄 Imprimer PDF
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <!-- Tableau -->
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Titre</th>
                                            <th>Total Entrées</th>
                                            <th>Montant Achat</th>
                                            <th>Total Sorties</th>
                                            <th>Montant Vente</th>
                                            <th>Stock Réel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($livres as $index => $livre)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $livre->titre }}</td>
                                                <td>{{ $livre->total_entree ?? 0 }}</td>
                                                <td>{{ $livre->montant_entree ?? 0 }}</td>
                                                <td>{{ $livre->total_sortie ?? 0 }}</td>
                                                <td>{{ $livre->montant_sortie ?? 0 }}</td>
                                                <td><strong>{{ $livre->stock_actuel }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucun livre trouvé</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="2">TOTAL</th>
                                            <th>{{ $etat['total_entree'] ?? 0 }}</th>
                                            <th>{{ number_format($etat['montant_entree'] ?? 0, 0, ',', ' ') }} CFA</th>
                                            <th>{{ $etat['total_sortie'] ?? 0 }}</th>
                                            <th>{{ number_format($etat['montant_sortie'] ?? 0, 0, ',', ' ') }} CFA</th>
                                            <th>{{ $etat['stock_global'] ?? 0 }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div>
                                    {{-- {{ $livres->links() }} --}}
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
