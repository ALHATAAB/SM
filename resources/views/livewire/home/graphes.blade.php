@php
    $anneeFiltreLibelle = optional($anneesDisponibles->firstWhere('id', (int) $anneeFiltre))->annee ?? '—';
    $totalInscrits      = (int) ($statsInscrits['total'] ?? 0);
@endphp

{{-- ===== Filtre des graphiques ===== --}}
<div class="row">
    <div class="col-12">
        <div class="ibox">
            <div class="ibox-body">
                <form class="form-inline" wire:submit.prevent>
                    <label class="mr-2" for="f_annee">{{ __('message.Annee_scolaire') }}</label>
                    <select id="f_annee" class="form-control mr-3" wire:model.live="anneeFiltre">
                        @foreach ($anneesDisponibles as $a)
                            <option value="{{ $a->id }}">{{ $a->annee }}{{ $a->etat === 'actif' ? ' ★' : '' }}</option>
                        @endforeach
                    </select>

                    <label class="mr-2" for="f_du">{{ __('message.Du') }}</label>
                    <input id="f_du" type="date" class="form-control mr-3" wire:model.live="dateDebut">

                    <label class="mr-2" for="f_au">{{ __('message.Au') }}</label>
                    <input id="f_au" type="date" class="form-control mr-3" wire:model.live="dateFin">

                    <button type="button" class="btn btn-default" wire:click="reinitialiserFiltre">
                        {{ __('message.Reinitialiser') }}
                    </button>
                    <span wire:loading wire:target="anneeFiltre,dateDebut,dateFin,reinitialiserFiltre" class="ml-3 text-muted">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </form>
                <small class="text-muted">{{ __('message.Filtre_graphiques_note') }}</small>
            </div>
        </div>
    </div>
</div>

{{-- ===== Ligne 1 : évolution mensuelle + inscrits ===== --}}
<div class="row">
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-body">
                <div class="mb-4 flexbox">
                    <div>
                        <h3 class="m-0">{{ __('message.Statistique_par_mois') }}</h3>
                        <div>{{ __('message.Recette_depense') }}</div>
                    </div>
                    <div class="d-inline-flex">
                        <div class="px-3" style="border-left:1px solid rgba(0,0,0,.1);border-right:1px solid rgba(0,0,0,.1);">
                            <div class="text-muted">{{ __('message.Total_recette') }}</div>
                            <span class="m-0 h2">{{ number_format((float) ($statsTotaux['recettes'] ?? 0), 0, ',', ' ') }}</span><br>
                            <span class="ml-2 text-success">
                                <i class="fa fa-level-up"></i> {{ __('message.Annee_scolaire') }} : {{ $anneeFiltreLibelle }}
                            </span>
                        </div>
                        <div class="px-3" style="border-right:1px solid rgba(0,0,0,.1);">
                            <div class="text-muted">{{ __('message.Total_depense') }}</div>
                            <span class="m-0 h2">{{ number_format((float) ($statsTotaux['depenses'] ?? 0), 0, ',', ' ') }}</span><br>
                            <span class="ml-2 text-danger">
                                <i class="fa fa-level-down"></i> {{ __('message.Annee_scolaire') }} : {{ $anneeFiltreLibelle }}
                            </span>
                        </div>
                    </div>
                </div>
                {{-- wire:ignore : les graphiques sont redessinés par le JS (événement stats-updated) --}}
                <div wire:ignore style="height:260px;"><canvas id="bar_chart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-head">
                <div class="ibox-title">{{ __('message.Statistique_inscrit') }}</div>
            </div>
            <div class="ibox-body">
                <div wire:ignore style="height:170px;"><canvas id="doughnut_chart"></canvas></div>

                {{-- Légende : effectif et pourcentage par catégorie --}}
                <ul class="list-unstyled mt-3 mb-0">
                    @foreach ($statsInscrits['items'] ?? [] as $it)
                        <li class="d-flex justify-content-between py-1" style="border-bottom:1px solid rgba(0,0,0,.06);">
                            <span><i class="fa fa-circle m-r-10" style="color: {{ $it['couleur'] }}"></i>{{ $it['label'] }}</span>
                            <span>
                                <strong>{{ number_format($it['total'], 0, ',', ' ') }}</strong>
                                <small class="text-muted">
                                    ({{ $totalInscrits > 0 ? number_format($it['total'] * 100 / $totalInscrits, 1, ',', ' ') : '0' }} %)
                                </small>
                            </span>
                        </li>
                    @endforeach
                    <li class="d-flex justify-content-between py-1">
                        <strong>{{ __('message.Total') }}</strong>
                        <strong>{{ number_format($totalInscrits, 0, ',', ' ') }}</strong>
                    </li>
                </ul>

                <hr>

                {{-- Répartition par niveau / filière / classe --}}
                <div class="flexbox mb-3">
                    <strong>{{ __('message.Repartition_par') }}</strong>
                    <div class="btn-group btn-group-sm">
                        @foreach (['niveau' => 'Niveau', 'filiere' => 'Filiere', 'classe' => 'Classe'] as $cle => $libelle)
                            <button type="button"
                                    class="btn {{ $repartitionPar === $cle ? 'btn-primary' : 'btn-default' }}"
                                    wire:click="$set('repartitionPar', '{{ $cle }}')">
                                {{ __('message.' . $libelle) }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div wire:ignore style="height:220px;"><canvas id="repartition_chart"></canvas></div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Ligne 2 : recettes par type, dépenses par catégorie, impayés par niveau ===== --}}
<div class="row">
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-head"><div class="ibox-title">{{ __('message.Recette_par_type') }}</div></div>
            <div class="ibox-body"><div wire:ignore style="height:260px;"><canvas id="recettes_chart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-head"><div class="ibox-title">{{ __('message.Depense_par_categorie') }}</div></div>
            <div class="ibox-body"><div wire:ignore style="height:260px;"><canvas id="depenses_chart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-head"><div class="ibox-title">{{ __('message.Impaye_par_niveau') }}</div></div>
            <div class="ibox-body"><div wire:ignore style="height:260px;"><canvas id="impayes_chart"></canvas></div></div>
        </div>
    </div>
</div>

{{-- Chart.js v4 est chargé par le layout (components/layouts/app.blade.php). Script classique : fonctionne quelle que soit la version des assets Livewire. --}}
<script>
(function () {
    var libelles = {
        recette: @json(__('message.Recette')),
        depense: @json(__('message.Depense')),
        impaye:  @json(__('message.Scolarite_impayer'))
    };
    var nombre = function (v) { return Number(v).toLocaleString('fr-FR'); };

    // (Re)crée un graphique : détruit l'ancien s'il existe
    function creer(id, config) {
        var ancien = Chart.getChart(id);
        if (ancien) { ancien.destroy(); }
        new Chart(document.getElementById(id), config);
    }

    var tooltipPart = { callbacks: { label: function (c) { return c.label + ' : ' + nombre(c.parsed); } } };

    function dessiner(d) {
        var m = d.mensuel, i = d.inscrits;

        creer('bar_chart', {
            type: 'line',
            data: {
                labels: m.labels,
                datasets: [
                    { label: libelles.recette, data: m.recettes, fill: true, tension: 0.4,
                      borderColor: 'rgb(88,214,141)', backgroundColor: 'rgba(88,214,141,.2)',
                      pointBackgroundColor: 'rgb(88,214,141)' },
                    { label: libelles.depense, data: m.depenses, fill: true, tension: 0.4,
                      borderColor: 'rgb(255,87,51)', backgroundColor: 'rgba(255,87,51,.2)',
                      pointBackgroundColor: 'rgb(255,87,51)' }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: { y: { beginAtZero: true, ticks: { callback: nombre } } },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: function (c) { return c.dataset.label + ' : ' + nombre(c.parsed.y); } } }
                }
            }
        });

        // Camembert des inscrits : couleurs identiques à la légende (envoyées par le serveur)
        creer('doughnut_chart', {
            type: 'doughnut',
            data: {
                labels: i.items.map(function (x) { return x.label; }),
                datasets: [{ data: i.items.map(function (x) { return x.total; }),
                    backgroundColor: i.items.map(function (x) { return x.couleur; }) }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '50%',
                plugins: { legend: { display: false }, tooltip: tooltipPart } }
        });

        // Répartition par niveau / filière / classe
        creer('repartition_chart', {
            type: 'bar',
            data: {
                labels: d.repartition.labels,
                datasets: [{ data: d.repartition.data, backgroundColor: 'rgba(52,152,219,.75)' }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { display: false } }
            }
        });

        creer('recettes_chart', {
            type: 'doughnut',
            data: {
                labels: d.recettes.labels,
                datasets: [{ data: d.recettes.data,
                    backgroundColor: ['#58d68d', '#3498db', '#f5b041', '#af7ac5', '#48c9b0'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '50%',
                plugins: { legend: { position: 'bottom' }, tooltip: tooltipPart } }
        });

        creer('depenses_chart', {
            type: 'doughnut',
            data: {
                labels: d.depenses.labels,
                datasets: [{ data: d.depenses.data,
                    backgroundColor: ['#ff5733', '#f4d03f', '#8e44ad'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '50%',
                plugins: { legend: { position: 'bottom' }, tooltip: tooltipPart } }
        });

        creer('impayes_chart', {
            type: 'bar',
            data: {
                labels: d.impayes.labels,
                datasets: [{ label: libelles.impaye, data: d.impayes.data,
                    backgroundColor: 'rgba(255,87,51,.75)' }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                scales: { x: { beginAtZero: true, ticks: { callback: nombre } } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return c.dataset.label + ' : ' + nombre(c.parsed.x); } } }
                }
            }
        });
    }

    // Affiche l'erreur sous les graphiques au lieu de rester vide (voir aussi la console F12)
    function erreur(message) {
        console.error('[Tableau de bord] ' + message);
        ['bar_chart', 'doughnut_chart', 'repartition_chart', 'recettes_chart', 'depenses_chart', 'impayes_chart']
            .forEach(function (id) {
                var canvas = document.getElementById(id);
                if (canvas && canvas.parentNode && !canvas.parentNode.querySelector('.chart-erreur')) {
                    var p = document.createElement('div');
                    p.className = 'chart-erreur text-danger small';
                    p.textContent = message;
                    canvas.parentNode.appendChild(p);
                }
            });
    }

    var donnees = @json($donneesGraphiques);

    function demarrer() {
        try {
            dessiner(donnees);
        } catch (e) {
            erreur('Graphiques : ' + e.message);
        }
        // Mise à jour quand le filtre change (événement envoyé par Home.php)
        if (window.__statsHandler) { window.removeEventListener('stats-updated', window.__statsHandler); }
        window.__statsHandler = function (e) {
            try {
                var d = e.detail;
                if (Array.isArray(d)) { d = d[0]; }
                if (d && d.stats) { d = d.stats; }
                dessiner(d);
            } catch (err) {
                erreur('Graphiques : ' + err.message);
            }
        };
        window.addEventListener('stats-updated', window.__statsHandler);
    }

    // Chart.js v4 doit être présent (le layout le charge). Sinon, on le charge ici.
    // Une ancienne version v2 (sans Chart.getChart) est remplacée.
    function lancer() {
    if (window.Chart && typeof Chart.getChart === 'function') {
        demarrer();
    } else {
        var s = document.createElement('script');
        s.src = @json(url('') . '/assets/vendors/chart.js/dist/chart.umd.js?v=4.4.9');
        s.onload = function () {
            if (window.Chart && typeof Chart.getChart === 'function') { demarrer(); }
            else { erreur('Chart.js v4 non chargé (ancienne version en cache ?). Faites Ctrl+F5.'); }
        };
        s.onerror = function () {
            erreur('Fichier introuvable : /assets/vendors/chart.js/dist/chart.umd.js');
        };
        document.head.appendChild(s);
    }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lancer);
    } else {
        lancer();
    }
})();
</script>

