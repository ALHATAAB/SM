<div>
    <body class="fixed-navbar">
        <div class="page-wrapper">
            <!-- START HEADER-->
            {{-- @include('components.layouts.header') --}}
            <!-- END HEADER-->
            <!-- START SIDEBAR-->
            {{-- @include('components.layouts.sidebar') --}}
            <!-- END SIDEBAR-->
            <div class="content-wrapper">
                <!-- START PAGE CONTENT-->
                <div class="page-content fade-in-up">
<div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">🔒 Licence expirée ou invalide</h4>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-3">
                            L'accès à SmartSchool est bloqué car la licence de cette installation
                            est expirée, absente, ou invalide.
                        </p>
                        @if (!empty($etat['designation']))
                            <p class="text-muted small">
                                Dernière licence connue : {{ $etat['designation'] }}
                                @if (!empty($etat['date_fin']))
                                    (expirée le {{ $etat['date_fin'] }})
                                @endif
                            </p>
                        @endif
                        <a href="{{ route('licence.formulaire') }}" class="btn btn-primary">
                            Importer une nouvelle licence
                        </a>
                       
                        <hr>
                        <p class="small text-muted mb-0">
                            Contactez Alhataab Digital pour renouveler votre licence :<br>
                            WhatsApp : +227 88 15 66 77 — Email : contact@alhataab.com
                        </p>
                   
 <p class="text-muted small mt-3">
                            <strong>Note :</strong> Si vous avez déjà importé une licence valide
                            et que vous voyez toujours ce message, veuillez contacter le support
                            pour résoudre le problème.
                        </p>
                     
                    </div>
                   <div class="card-footer text-muted small">
                        @include('components.layouts.footer')
                    </div> 
                </div>
            </div>
        </div>
                </div>
                <!-- END PAGE CONTENT-->
                <!-- START FOOTER-->
                {{-- @include('components.layouts.footer') --}}
                <!-- END FOOTER-->
            </div>
        </div>
    </body>
</div>
