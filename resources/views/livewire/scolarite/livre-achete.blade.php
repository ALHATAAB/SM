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
                                <!-- Button trigger modal -->
                                @if (!empty(Permission('Ajouter')))
                                    <a href="{{ url('/nouvel/achat/') }}">
                                        <button type="button" class="btn btn-ligth"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>  
                                @endif
                            </div>
                        </div>

                        <div class="ibox-body">

                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Fournisseur</th>
                                        <th style="text-align:right">Total (F CFA)</th>
                                         @if (!empty(Permission('Consulter')))
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($achats as $achat)
                                    @if ($achat->statuts=="annuler")
                                        <tr style="background-color: rgb(240, 152, 152)">
                                            <td>{{ $achat->id }}</td>
                                            <td>{{ Carbon\Carbon::parse($achat->date_comptable)->format('d/m/Y') }}</td>
                                            <td>{{ $achat->fournisseur ?? '-' }}</td>
                                            <td style="text-align:right">
                                                {{ number_format($achat->montant_total, 0, ',', ' ') }}</td>
                                            @if (!empty(Permission('Consulter')))
                                                <td>

                                                    <a href="{{ url('achat/livre/detail/'.encrypt($achat->id)) }}">
                                                        <button class="btn btn-sm btn-ligth ">
                                                            <i class="fa fa-eye"></i> Voir
                                                        </button>
                                                    </a>

                                                        {{-- <a href="{{ route('achats.edit', $achat) }}" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('achats.destroy', $achat) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                                                <i class="fa fa-trash"></i> Supprimer
                                                            </button>
                                                        </form> --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $achat->id }}</td>
                                            <td>{{ Carbon\Carbon::parse($achat->date_comptable)->format('d/m/Y') }}</td>
                                            <td>{{ $achat->fournisseur ?? '-' }}</td>
                                            <td style="text-align:right">
                                                {{ number_format($achat->montant_total, 0, ',', ' ') }}</td>
                                            @if (!empty(Permission('Consulter')))
                                                <td>

                                                    <a href="{{ url('achat/livre/detail/'.encrypt($achat->id)) }}">
                                                        <button class="btn btn-sm btn-ligth ">
                                                            <i class="fa fa-eye"></i> Voir
                                                        </button>
                                                    </a>

                                                        {{-- <a href="{{ route('achats.edit', $achat) }}" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('achats.destroy', $achat) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                                                <i class="fa fa-trash"></i> Supprimer
                                                            </button>
                                                        </form> --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @endif
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun achat trouvé.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div>
                                {{ $achats->links() }}
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
