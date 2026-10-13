<div>

    <body class="fixed-navbar ">
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
                        <div class="ibox-head">
                            <div class="ibox-title">Nouvel encaissement divers</div>
                        </div>
                        @if(($caisse->etat)=="ouverte")
                        <div class="ibox-body">
                            <form class="form-inline" wire:submit='addEncaissementDivers'>
                                <label class="sr-only" for="ex-email">Montant</label>
                                <input class="form-control mb-2 mr-sm-2 mb-sm-0" id="ex-email" type="text"  wire:model='montant'
                                    placeholder="Montant">
                                    {{-- <div>
                                        @error('montant')
                                        <span class="text-error">{{ $message }}</span>
                                        @enderror
                                    </div> --}}
                                <label class="sr-only" for="ex-pass">Reference</label>
                                <input class="form-control mb-2 mr-sm-2 mb-sm-0" id="ex-pass" type="text" wire:model='reference'
                                    placeholder="Reference">
                                    {{-- <div>
                                        @error('reference')
                                        <span class="text-error">{{ $message }}</span>
                                        @enderror
                                    </div> --}}
                                <button class="btn btn-success" type="submit">Valider</button>
                            </form>
                        </div>
                        @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Caisse Fermer.</div>
                        @endif
                    </div>
                    @else
                        <div class="alert alert-danger alert-bordered"><strong> Attention ! </strong> Vous n'avez pas de caisse.</div>
                    @endif
                    @if($encaissementDiversCount > 0)
                    <div class="ibox ibox-primary">
                        <div class="ibox-head">
                            <div class="ibox-title">Autorisation encaissement</div>
                        </div>
                        <div class="ibox-body">
                            <table class="table table-bordered">
                                <thead class="thead-default">
                                    <tr>
                                        <th>Caisse</th>
                                        <th>Reference</th>
                                        <th>Montant </th>
                                        <th>Caissier</th>
                                        @if (!empty(Permission('Valider')))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($encaissementDivers as $encaissement )
                                    <tr>
                                        <td>{{ $encaissement->caisse->caisse }}</td>
                                        <td>{{ $encaissement->reference }}</td>
                                        <td>{{ $encaissement->montant }}</td>
                                        <td>{{ $encaissement->user->nom.' '.$encaissement->user->prenom }}</td>
                                        @if (!empty(Permission('Valider')))
                                        <td>
                                            <button class="btn btn-success"   wire:click.prevent="autoriser({{$encaissement->id}})">Autoriser</button> 
                                            <button class="btn btn-danger"  wire:click.prevent="rejeter({{$encaissement->id}})">Rejeter</button>
                                       </td>
                                        @endif
                                    </tr> 
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
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
