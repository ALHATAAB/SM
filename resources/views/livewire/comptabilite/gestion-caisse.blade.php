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
                <div class="page-content fade-in-up">
                    <!-- START PAGE CONTENT-->
                    @if ($countCaisse > 0)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="ibox ibox-primary">
                                    {{-- <img class="card-img-top" src="{{ url('') }}/assets/img/blog/macarons.jpg" > --}}
                                    <div class="ibox-head" style="background-color: #bdc3c7;color:#000000">

                                        <div class="ibox-title">{{ __('message.Gestion_caisse') }}</div>
                                        <div class="ibox-tools">
                                            <span class="pull-right text-dark font-13">{{ $caisse->caisse }}</span>
                                        </div>
                                    </div>
                                    <div class="ibox-body">
                                        @if ($caisse->etat == 'ouverte')
                                            <div class= "row">
                                                <div class="col-sm-12">
                                                    <ul class="list-group list-group-divider no-margin">
                                                        <li class="list-group-item" style="border-top-color:#0f0f0f;">
                                                            Solde repport
                                                            <span
                                                                class="badge badge-info  float-right">{{ number_format($caisse->repport, 0, ',', ' ') }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ul class="list-group list-group-divider no-margin">
                                                        <li class="list-group-item " style="border-top-color:#0f0f0f;">
                                                            Nombre operation entree
                                                            <span
                                                                class="badge badge-success badge-circle float-right">{{ $countEntree }}</span>
                                                        </li>
                                                        <li class="list-group-item" style="border-top-color:#0f0f0f;">
                                                            Nombre operation sortie
                                                            <span
                                                                class="badge badge-danger badge-circle float-right">{{ $countSortie }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ul class="list-group list-group-divider no-margin">

                                                        <li class="list-group-item " style="border-top-color:#0f0f0f;">
                                                            Montant total entree
                                                            <span
                                                                class="badge badge-success  float-right">{{ number_format($montantEntree, 0, ',', ' ') }}</span>
                                                        </li>
                                                        <li class="list-group-item" style="border-top-color:#0f0f0f;">
                                                            Montant total sortie
                                                            <span
                                                                class="badge badge-danger  float-right">{{ number_format($montantSortie, 0, ',', ' ') }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-sm-12">
                                                    <ul class="list-group list-group-divider no-margin"
                                                        style="background-color: #e74c3c; color:#ffffff">
                                                        <li class="list-group-item" style="border-top-color:#0f0f0f;">
                                                            Solde caisse
                                                            <span class="badge badge-default  float-right"
                                                                style="font-family: 'Poppins', sans-serif;">{{ number_format($caisse->solde, 0, ',', ' ') }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-danger alert-bordered"><strong> Attention !
                                                </strong> Caisse Fermer.</div>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        @if ($caisse->etat == 'ouverte')
                                            <a class="text-danger"
                                                wire:click.prevent="clotureCaisse({{ $caisse->id }})"><i
                                                    class="ti ti-lock"></i> Cloture ta caisse</a>
                                        @else
                                            <a class="text-success"
                                                wire:click.prevent="ouvertureCaisse({{ $caisse->id }})"><i
                                                    class="ti ti-unlock"></i> Ouvre ta caisse</a>
                                        @endif
                                        <span
                                            class="pull-right text-dark font-13">{{ \Carbon\Carbon::parse($caisse->date_comptable)->format('d/m/Y') }}</span>
                                        {{-- <span class="pull-right text-dark font-13" id="date">date jour</span> --}}

                                    </div>
                                    <script>
                                        document.getElementById("date").textContent = new Date().toLocaleDateString();
                                    </script>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                @if ($caisse->etat == 'ouverte')
                                    <div class="ibox ibox-primary">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="100px">{{ __('message.Date') }}</th>
                                                        <th width="450px">{{ __('message.Description') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Entree') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Sortie') }}</th>
                                                        <th width="150px" style="text-align:right">
                                                            {{ __('message.Solde') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($mouvementCaisses as $mouvementCaisse)
                                                        {{-- <tr>
                                                       
                                                    </tr> --}}
                                                        <tr>
                                                            @if ($mouvementCaisse->description == 'Solde cloture' || $mouvementCaisse->description == 'Solde repport')
                                                                <td><Strong>{{ \Carbon\Carbon::parse($mouvementCaisse->date_comptable)->format('d/m/Y') }}</Strong>
                                                                </td>
                                                                <td><strong>{{ $mouvementCaisse->description }}</strong>
                                                                </td>
                                                                <td style="text-align:right">
                                                                    <strong>{{ number_format($mouvementCaisse->entree, 0, ',', ' ') }}</strong>
                                                                </td>
                                                                <td style="text-align:right">
                                                                    <strong>{{ number_format($mouvementCaisse->sortie, 0, ',', ' ') }}</strong>
                                                                </td>
                                                                <td style="text-align:right">
                                                                    <strong>{{ number_format($mouvementCaisse->solde, 0, ',', ' ') }}</strong>
                                                                </td>
                                                            @else
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($mouvementCaisse->date_comptable)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $mouvementCaisse->description }}</td>
                                                                <td style="text-align:right">
                                                                    {{ number_format($mouvementCaisse->entree, 0, ',', ' ') }}
                                                                </td>
                                                                <td style="text-align:right">
                                                                    {{ number_format($mouvementCaisse->sortie, 0, ',', ' ') }}
                                                                </td>
                                                                <td style="text-align:right">
                                                                    {{ number_format($mouvementCaisse->solde, 0, ',', ' ') }}
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ibox-footer">
                                            <button class="btn btn-default btn-sm" wire:click='recuPrint()'><i
                                                    class="fa fa-print"></i> Imprimer</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez
                            pas de caisse.</div>
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
