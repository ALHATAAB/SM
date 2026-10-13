<div>
    <body class="fixed-navbar ">
        <div class="page-wrapper">
            @include('components.layouts.header')
            @include('components.layouts.sidebar')
            <div class="content-wrapper">
                <div class="page-content fade-in-up">
                    @if (!empty(Permission('Afficher')))
                        @include('livewire.alert.message')
                        @include('livewire.paie-r-h.partials.constante-contenu')
                        @else
                        <div class="ibox ibox-primary">
                            <div class="ibox-body text-center">
                                <h1 class="m-t-20">404</h1>
                                <p class="error-title">PAGE NOT FOUND</p>
                            </div>
                        </div>
                    @endif
                </div>
                @include('components.layouts.footer')
            </div>
        </div>
        @include('components.layouts.loading')
    </body>
</div>
