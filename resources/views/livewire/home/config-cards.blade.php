@php
    $cartesConfig = [
        ['color' => 'warning',   'value' => CountClasse(),         'label' => 'Classe_etude',      'icon' => 'fa fa-graduation-cap'],
        ['color' => 'info',      'value' => CountMatiere(),        'label' => 'Matiere_etude',     'icon' => 'ti-files'],
        ['color' => 'success',   'value' => $countInscrit ?? 0,    'label' => 'Nombre_inscrit',    'icon' => 'ti-write'],
        ['color' => 'primary',   'value' => $countEnseignant ?? 0, 'label' => 'Nombre_enseignant', 'icon' => 'fa fa-user'],
        ['color' => 'secondary', 'value' => CountNiveau(),         'label' => 'Niveau_etude',      'icon' => 'ti-panel'],
        ['color' => 'primary',   'value' => CountFiliere(),        'label' => 'Filiere_etude',     'icon' => 'ti-files'],
        ['color' => 'warning',   'value' => CountSalle(),          'label' => 'Salle_etude',       'icon' => 'fa fa-home'],
        ['color' => 'danger',    'value' => $countPersonnel ?? 0,  'label' => 'Nombre_personnel',  'icon' => 'fa fa-users'],
    ];
@endphp

<div class="row">
    @foreach ($cartesConfig as $carte)
        @include('livewire.home.stat-card', $carte + ['format' => false])
    @endforeach
</div>
