@php
    // Recettes = flèche "up" ; dépenses = flèche "down"
    $rangeesFinance = [
        [
            ['color' => 'success', 'value' => $SoldeTotalCaisse ?? 0,    'label' => 'Total_solde_caisse',  'icon' => 'ti-package'],
            ['color' => 'success', 'value' => $SoldeTotalBanque ?? 0,    'label' => 'Total_solde_banque',  'icon' => 'ti-credit-card'],
            ['color' => 'primary', 'value' => $montantEncaissement ?? 0, 'label' => 'Encaissement_divers', 'icon' => 'ti-import', 'trend' => 'up'],
            ['color' => 'primary', 'value' => $montantDepotBanque ?? 0,  'label' => 'Depot_banque',        'icon' => 'ti-import', 'trend' => 'up'],
        ],
        [
            ['color' => 'warning', 'value' => $montantInscrit ?? 0,   'label' => 'Solde_inscription',   'icon' => 'ti-write',     'trend' => 'up'],
            ['color' => 'warning', 'value' => $montantScolarite ?? 0, 'label' => 'Solde_scolarisation', 'icon' => 'ti-pie-chart', 'trend' => 'up'],
            ['color' => 'warning', 'value' => $totalAbonnement ?? 0,  'label' => 'Abonnement',          'icon' => 'ti-id-badge',  'trend' => 'up'],
            ['color' => 'warning', 'value' => $VenteLivre ?? 0,       'label' => 'Total_vente_livre',   'icon' => 'ti-stats-up',  'trend' => 'up'],
        ],
        [
            ['color' => 'secondary', 'value' => $totalDepenses,              'label' => 'Total_depense',       'icon' => 'ti-shopping-cart-full', 'trend' => 'down'],
            ['color' => 'danger',    'value' => $gestionDepense ?? 0,        'label' => 'Charge_divers',       'icon' => 'ti-gift',               'trend' => 'down'],
            ['color' => 'danger',    'value' => $gestionPaieEnseignant ?? 0, 'label' => 'Paiement_enseignant', 'icon' => 'fa fa-user',            'trend' => 'down'],
            ['color' => 'danger',    'value' => $gestionPaie ?? 0,           'label' => 'Salaire_personnel',   'icon' => 'fa fa-users',           'trend' => 'down'],
        ],
    ];
@endphp

@foreach ($rangeesFinance as $rangee)
    <div class="row">
        @foreach ($rangee as $carte)
            @include('livewire.home.stat-card', $carte)
        @endforeach
    </div>
@endforeach
