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
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox ibox-primary">
                                {{-- <img class="card-img-top" src="{{ url('') }}/assets/img/blog/macarons.jpg" > --}}
                                <div class="ibox-head" style="background-color: #bdc3c7;color:#000000">

                                    <div class="ibox-title">{{ __('message.Etat_caisse') }}</div>
                                    <div class="ibox-tools">
                                        <span class="pull-right text-dark font-13">
                                            <div class="row">

                                                <div class="form-group">
                                                    <br>
                                                    <select class="form-control" wire:model.live='caisse'>
                                                        <option value="">-- choix caisse -- </option>
                                                        @foreach ($caisses as $caisse)
                                                            <option value="{{ $caisse->id }}">{{ $caisse->caisse }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div>
                                                        @error('caisse')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <br>
                                                    <input class="form-control" type="date" value="0"
                                                        wire:model.live='date_comptable'>
                                                    <div>
                                                        @error('date_comptable')
                                                            <span class="text-error">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group">
                                                <br>
                                                <select class="form-control" wire:model.live='date_comptable'>
                                                    <option value="">-- choix date comptable -- </option>
                                                    @foreach ($date_comptables as $date_comptable)
                                                    <option value="{{ $date_comptable->date_comptable }}">{{ $date_comptable->date_comptable }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div>
                                                    @error('date_comptable')
                                                    <span class="text-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                            </div>

                                        </span>
                                    </div>
                                </div>
                                @if ($countCaisse > 0)
                                    <div class="ibox-body">
                                        <div class= "row">
                                            <div class="col-sm-12">
                                                <ul class="list-group list-group-divider no-margin">
                                                    {{-- <li class="list-group-item" style="border-top-color:#0f0f0f;">
                                                        Solde repport
                                                        <span
                                                            class="badge badge-info  float-right">{{ number_format($caisse->repport, 0, ',', ' ') }}</span>
                                                    </li> --}}
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
                                                        Solde actuel caisse
                                                        <span class="badge badge-default  float-right"
                                                            style="font-family: 'Poppins', sans-serif;">{{ number_format($solde, 0, ',', ' ') }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">

                                        {{-- <span class="pull-right text-dark font-13">{{ \Carbon\Carbon::parse($caisse->date_comptable)->format('d/m/Y') }}</span> --}}
                                        {{-- <span class="pull-right text-dark font-13" id="date">date jour</span> --}}

                                    </div>
                                @endif
                                <script>
                                    document.getElementById("date").textContent = new Date().toLocaleDateString();
                                </script>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            @if ($countCaisse > 0)
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
                                                            <td>
                                                                <Strong>{{ \Carbon\Carbon::parse($mouvementCaisse->date_comptable)->format('d/m/Y') }}</Strong>
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
                                        {{-- <button class="btn btn-default btn-sm" wire:click='recuPrint()'><i
                                                class="fa fa-print"></i> Imprimer</button> --}}
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
