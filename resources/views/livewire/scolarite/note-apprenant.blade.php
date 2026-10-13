<div>

    <body class="fixed-navbar">
        <div class="page-wrapper">

            <!-- START HEADER -->
            @include('components.layouts.header')
            <!-- END HEADER -->

            <!-- START SIDEBAR -->
            @include('components.layouts.sidebar')
            <!-- END SIDEBAR -->

            <div class="content-wrapper">

                <!-- START PAGE CONTENT -->

                <div class="page-content fade-in-up">

                    <div class="ibox">
                        <div class="ibox-head bg-danger text-white">
                            <div class="ibox-title">{{ __('message.Nouvelle_note_apprenant') }}</div>
                            <div class="ibox-tools">

                                {{-- <a href="{{ url('liste/absence/apprenant/') }}">
                                    <button type="button" class="btn btn-ligth" data-toggle="modal"
                                        data-target="#exampleModalCenter"><i class="fa fa-arrow-left"></i> Retour à la
                                        liste
                                    </button>

                                </a> --}}
                            </div>
                        </div>

                        <div class="ibox-body">

                            <div>
    <div class="row mb-3">
        <div class="col">
            <label>Classe</label>
            <select wire:model.live="classe_id" class="form-control">
                <option value="">-- Choisir une classe --</option>
                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->classe }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>Matière</label>
            <select wire:model.live="matiere_id" class="form-control">
                <option value="">-- Choisir une matière --</option>
                @foreach($matieres as $matiere)
                    <option value="{{ $matiere->matiere_etude_id }}">{{ $matiere->MatiereEtude->matiere_etude }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>Trimestre</label>
            <select wire:model.live="trimestre" class="form-control">
                <option value="">-- Trimestre --</option>
                @foreach($trimestres as $trimestre)
                    <option value="{{ $trimestre->id }}">{{ $trimestre->trimestre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col">
            <label>Type</label>
            <select wire:model.live="type" class="form-control">
                <option value="classe">Note de classe</option>
                <option value="composition">Composition</option>
            </select>
        </div>
    </div>

    @if($apprenants && count($apprenants) > 0)
    <form wire:submit.prevent="saveNotes">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Niveau</th>
                    <th>Filiere</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apprenants as $apprenant)
                <tr>
                    <td>{{ $apprenant->preInscription->prenom }} {{ $apprenant->preInscription->nom }}</td>
                    <td>{{ $apprenant->niveau->niveau }}</td>
                    <td>{{ $apprenant->filiere->filiere}} </td>
                    <td>
                        <input type="number" min="0" max="20" step="0.01"
                               wire:model.defer="notes.{{ $apprenant->id }}"
                               class="form-control" required />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
    @endif

    @if(session()->has('message'))
        <div class="alert alert-success mt-2">
            {{ session('message') }}
        </div>
    @endif
</div>



                        </div>
                    </div>




                </div>
                <!-- END PAGE CONTENT -->

                @include('components.layouts.footer')

            </div>
        </div>

        <!-- PAGE BACKDROPS / LOADING -->
        @include('components.layouts.loading')

    </body>
</div>
