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

                    @php
                        // Permissions : calculées une seule fois, chaque bloc est indépendant
                        $isParametre     = !empty(Permission('Parametre'));
                        $isConfiguration = !empty(Permission('Configuration'));
                        $isTresorier     = !empty(Permission('Tresorier'));

                        $voirFinance = $isParametre || $isTresorier;
                        $voirConfig  = $isParametre || $isConfiguration;
                        $voirGraphes = $isParametre;

                        // Valeurs communes (une seule source de vérité)
                        $anneeActive   = AnneeScolaire()->annee ?? '—';
                        $totalDepenses = ($gestionDepense ?? 0) + ($gestionPaie ?? 0)
                                       + ($gestionPaieEnseignant ?? 0);
                    @endphp

                    @unless ($isParametre)
                        <div class="alert alert-success alert-dismissable fade show text-center">
                            <button class="close" data-dismiss="alert" aria-label="Close"></button>
                            <h4>{{ __('message.Salut') }},</h4>
                            <h2>{{ __('message.Salutation') }}</h2>
                        </div>
                    @endunless

                    @if ($voirFinance)
                        @include('livewire.home.finance-cards')
                    @endif

                    @if ($voirGraphes)
                        @include('livewire.home.graphes')
                    @endif

                    @if ($voirConfig)
                        @include('livewire.home.config-cards')
                    @endif

                    @if ($voirFinance)
                        @include('livewire.home.situation-scolarite')
                    @endif

                </div>
                <!-- END PAGE CONTENT-->
                @include('components.layouts.footer')
            </div>
            <!-- BEGIN PAGA BACKDROPS-->
            @include('components.layouts.loading')
            <!-- END PAGA BACKDROPS-->
        </div>
    </body>
</div>
