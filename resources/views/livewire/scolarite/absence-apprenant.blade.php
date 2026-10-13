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
                            <div class="ibox-title">{{ __('message.Nouvel_absence_apprenant') }}</div>
                            <div class="ibox-tools">
                                 
                                <a href="{{ url('liste/absence/apprenant/') }}" >
                                   <button type="button" class="btn btn-ligth" data-toggle="modal"
                                            data-target="#exampleModalCenter"><i class="fa fa-arrow-left"></i> Retour à la liste
                                        </button>
                                    
                                </a>
                            </div>
                        </div>

                        <div class="ibox-body">
                            <div class="container mt-4">
                                @include('livewire.alert.message')

                                <!-- Sélection classe -->
                                <div class="form-group">
                                    <label for="classe">Classe</label>
                                    <select wire:model.live="classe" id="classe" class="form-control">
                                        <option value="">-- Choisir une classe --</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->classe }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Liste élèves avec cases à cocher -->
                                @if ($classe && $apprenants)
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <strong>Cocher les élèves absents – {{ $classeSelect->classe }}</strong>
                                        </div>
                                        <div class="card-body p-0">
                                            <ul class="list-group list-group-flush">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Absent</th>
                                                            <th>Matricule</th>
                                                            <th>Nom</th>
                                                            <th>Prénom</th>
                                                            <th>Niveau</th>
                                                            <th>Filiere</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($apprenants as $apprenant)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" wire:model.live="absents"
                                                                        value="{{ $apprenant->id }}"
                                                                        id="apprenant-{{ $apprenant->id }}"
                                                                        class="form-check-input">
                                                                </td>
                                                                <td>{{ $apprenant->preInscription->matricule }}</td>
                                                                <td>{{ $apprenant->preInscription->nom }}</td>
                                                                <td>{{ $apprenant->preInscription->prenom }}</td>
                                                                <td>{{ $apprenant->niveau->niveau }}</td>
                                                                <td>{{ $apprenant->filiere->filiere }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                <!-- Formulaire commun -->
                                @if (count($absents) > 0)
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Saisie collective de l’absence</strong>
                                        </div>
                                        <div class="card-body">

                                            <form wire:submit.prevent="enregistrer">
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Enseignant') }}</label>
                                                        <select class="form-control" wire:model.live="enseignant">
                                                            <option value="">-- Enseignant --</option>
                                                            @foreach ($teachers as $t)
                                                                <option value="{{ $t->id }}">
                                                                    {{ $t->nom }}
                                                                    {{ $t->prenom }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('enseignant')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                    </div>
                                                    <div class="col-sm-6 form-group">
                                                        <label>{{ __('message.Seance') }}</label>
                                                        <select class="form-control" wire:model.live="seance_id">
                                                            <option value="">-- Cours --</option>
                                                            @foreach ($courses as $c)
                                                                <option value="{{ $c->id }}">
                                                                    {{ $c->jour . ' : ' . $c->heure_debut . ' à ' . $c->heure_fin . ' - ' . $c->matiere->matiere_etude }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @error('seance_id')
                                                                <span class="text-error">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <input type="date" wire:model="date" id="date"
                                                        class="form-control">
                                                    @error('date')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="motif">Motif (facultatif)</label>
                                                    <input type="text" wire:model="motif" id="motif"
                                                        class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label class="ui-checkbox">
                                                        <input type="checkbox" wire:model="justifiee"
                                                            class="form-check-input" id="justifiee">
                                                        <span class="input-span"></span>Absence
                                                        justifiée</label>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Enregistrer les
                                                    absences</button>
                                            </form>
                                        </div>
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
