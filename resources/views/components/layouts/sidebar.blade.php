<nav class="page-sidebar sidebar-blue" id="sidebar" wire:ignore>
    @php
        $user = Auth::user();
        $journee = function_exists('JourneeOuverte') ? JourneeOuverte() : null;
        $solde = function_exists('soldeCaisse') ? soldeCaisse() : null;

        // Helpers actifs
        $isActive = fn(...$patterns) => call_user_func_array([Request::class, 'is'], $patterns) ? 'active' : '';
        $isOpen = fn(...$patterns) => call_user_func_array([Request::class, 'is'], $patterns) ? 'in' : '';
        $ariaExp = fn(...$patterns) => call_user_func_array([Request::class, 'is'], $patterns) ? 'true' : 'false';
    @endphp

    <div id="sidebar-collapse">
        <div class="admin-block d-flex">
            <div>
                @auth
                    <a href="{{ url('profile/' . encrypt($user->id)) }}">
                        <img src="{{ asset('assets/img/admin-avatar.png') }}" width="25" alt="Avatar administrateur">
                    </a>
                @else
                    <img src="{{ asset('assets/img/admin-avatar.png') }}" width="25" alt="Avatar">
                @endauth
            </div>

            <div class="admin-info">
                @auth
                    <a href="{{ url('profile/' . encrypt($user->id)) }}">
                        <div class="font-strong">{{ trim(($user->nom ?? '') . ' ' . ($user->prenom ?? '')) }}</div>
                    </a>
                    <small>{{ $user->role->nom ?? '' }}</small>
                    @if ($journee)
                        <br>
                        <small>
                            {{ __('message.Date_comptable') }} :
                            {{ \Carbon\Carbon::parse($journee->date_comptable)->format('d/m/Y') }}
                        </small>
                    @endif

                    @if ($solde)
                        <br>
                        <small>{{ $solde->caisse }} :</small>
                        <br>
                        <small>{{ number_format($solde->solde, 0, ',', ' ') }}</small>
                    @endif
                @endauth
            </div>
        </div>

        <ul class="side-menu metismenu">
            {{-- Tableau de bord --}}
            <li class="{{ $isActive('home') }}">
                <a href="{{ url('home') }}" wire:current="active fw-bold">
                    <i class="sidebar-item-icon ti ti-dashboard"></i>
                    <span class="nav-label">{{ __('menu.Tableau_de_bord') }}</span>
                </a>
            </li>

            {{-- Paramètre --}}
            @if (!empty(Permission('Parametre')))
                <li class="heading">{{ __('menu.Parametre') }}</li>
                @php
                    $paramPatterns = [
                        'societe',
                        'licence/contrat',
                        'users',
                        'parametre/paieRH',
                        'parametre',
                        'devise',
                        'agence',
                        'caisse',
                        'banque',
                        'role/permission',
                        'type/abonnement',
                    ];
                @endphp
                <li class="{{ $isActive(...$paramPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$paramPatterns) }}">
                        <i class="sidebar-item-icon ti ti-settings"></i>
                        <span class="nav-label">{{ __('menu.Parametre') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$paramPatterns) }}">
                        <li><a class="{{ $isActive('licence/contrat') }}" wire:current="active fw-bold"
                                href="{{ url('licence/contrat') }}">{{ __('menu.Licence_contrat') }}</a></li>
                        </li>
                        <li>
                            <a class="{{ $isActive('parametre') }}" wire:current="active fw-bold"
                                href="{{ url('parametre') }}">{{ __('menu.Parametre_generaux') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('parametre/paieRH') }}" wire:current="active fw-bold"
                                href="{{ url('parametre/paieRH') }}">{{ __('menu.Parametre_paieRH') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('societe') }}" wire:current="active fw-bold"
                                href="{{ url('societe') }}">{{ __('menu.Parametre_societe') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('role/permission') }}" wire:current="active fw-bold"
                                href="{{ url('role/permission') }}">{{ __('menu.Parametre_role_permission') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('users') }}" wire:current="active fw-bold"
                                href="{{ url('users') }}">{{ __('menu.Parametre_utilisateurs') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('caisse') }}" wire:current="active fw-bold"
                                href="{{ url('caisse') }}">{{ __('menu.Parametre_caisses') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('banque') }}" wire:current="active fw-bold"
                                href="{{ url('banque') }}">{{ __('menu.Parametre_banques') }}</a>
                        </li>
                        <li>
                            <a class="{{ $isActive('type/abonnement') }}" wire:current="active fw-bold"
                                href="{{ url('type/abonnement') }}">{{ __('menu.Type_abonnement') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Configuration / Administration --}}
            @if (!empty(Permission('Configuration')))
                <li class="heading">{{ __('menu.Configuration') }}</li>
                @php
                    $adminPatterns = [
                        'annee/scolaire',
                        'licence/contrat',
                        'trimestre',
                        'classe/etude',
                        'salle/etude',
                        'niveau/etude',
                        'filiere/etude',
                        'frais/etude',
                        'matiere/etude',
                        'coefficient/matiere',
                        'frais/abonnement',
                    ];
                @endphp
                <li class="{{ $isActive(...$adminPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$adminPatterns) }}">
                        <i class="sidebar-item-icon fa fa-edit"></i>
                        <span class="nav-label">{{ __('menu.Administration') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$adminPatterns) }}">
                        <li><a class="{{ $isActive('annee/scolaire') }}" wire:current="active fw-bold"
                                href="{{ url('annee/scolaire') }}">{{ __('menu.Annee_scolaire') }}</a></li>
                        <li><a class="{{ $isActive('trimestre') }}" wire:current="active fw-bold"
                                href="{{ url('trimestre') }}">{{ __('menu.Trimestre') }}</a></li>
                        <li><a class="{{ $isActive('classe/etude') }}" wire:current="active fw-bold"
                                href="{{ url('classe/etude') }}">{{ __('menu.Classe_etude') }}</a></li>
                        <li><a class="{{ $isActive('salle/etude') }}" wire:current="active fw-bold"
                                href="{{ url('salle/etude') }}">{{ __('menu.Salle_etude') }}</a></li>
                        <li><a class="{{ $isActive('niveau/etude') }}" wire:current="active fw-bold"
                                href="{{ url('niveau/etude') }}">{{ __('menu.Niveau_etude') }}</a></li>
                        <li><a class="{{ $isActive('filiere/etude') }}" wire:current="active fw-bold"
                                href="{{ url('filiere/etude') }}">{{ __('menu.Filiere_etude') }}</a></li>
                        <li><a class="{{ $isActive('frais/etude') }}" wire:current="active fw-bold"
                                href="{{ url('frais/etude') }}">{{ __('menu.Frais_etude') }}</a></li>
                        <li><a class="{{ $isActive('matiere/etude') }}" wire:current="active fw-bold"
                                href="{{ url('matiere/etude') }}">{{ __('menu.Matiere_etude') }}</a></li>
                        <li><a class="{{ $isActive('coefficient/matiere') }}" wire:current="active fw-bold"
                                href="{{ url('coefficient/matiere') }}">{{ __('menu.Coefficient_matiere') }}</a></li>
                        <li><a class="{{ $isActive('frais/abonnement') }}" wire:current="active fw-bold"
                                href="{{ url('frais/abonnement') }}">{{ __('menu.Frais_abonnement') }}</a></li>
                    </ul>
                </li>
            @endif
            @if (!empty(Permission('Employer')) || !empty(Permission('Comptabilite')))
                <li class="heading">{{ __('menu.Ressource_humaine') }}</li>
                <li>
                    <a href="javascript:;">
                        <i class="sidebar-item-icon fa fa-users"></i>
                        <span class="nav-label">{{ __('menu.Personnel_Administratif') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">
                        @if (!empty(Permission('Employer')))
                            <li>
                                <a href="{{ url('/employer') }}"
                                    class="{{ Request::is('/employer') ? 'active fw-bold' : '' }}">
                                    {{ __('menu.Employer') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('rubrique') }}"
                                    class="{{ Request::is('rubrique') ? 'active fw-bold' : '' }}">
                                    {{ __('menu.Rubrique') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('etat/paie') }}"
                                    class="{{ Request::is('etat/paie') ? 'active fw-bold' : '' }}">
                                    {{ __('menu.Calcul_Paie') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('certificat/travail') . '?population=employer' }}"
                                    class="{{ Request::is('certificat/travail') ? 'active fw-bold' : '' }}">
                                    {{ __('menu.Certificat_travail') }}
                                </a>
                            </li>
                        @endif

                        @if (!empty(Permission('Comptabilite')))
                            <li>
                                <a href="{{ url('reglement/paie') }}"
                                    class="{{ Request::is('reglement/paie') ? 'active fw-bold' : '' }}">
                                    {{ __('menu.Reglement_Paie') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Enseignants --}}
            @if (!empty(Permission('Scolarite')) || !empty(Permission('Comptabilite')))
                @php
                    $ensPatterns = [
                        'liste/enseignant',
                        'emargement/enseignant',
                        'gestionnaire/paie/enseignant',
                        'reglement/paie/enseignant',
                        'carte/professionnelle',
                        'certificat/travail',
                    ];
                @endphp
                <li class="{{ $isActive(...$ensPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$ensPatterns) }}">
                        <i class="sidebar-item-icon fa fa-users"></i>
                        <span class="nav-label">{{ __('menu.Enseignants') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$ensPatterns) }}">
                        @if (!empty(Permission('Scolarite')))
                            <li><a class="{{ $isActive('liste/enseignant') }}" wire:current="active fw-bold"
                                    href="{{ url('liste/enseignant') }}">{{ __('menu.Liste_enseignant') }}</a></li>
                            <li><a class="{{ $isActive('emargement/enseignant') }}" wire:current="active fw-bold"
                                    href="{{ url('emargement/enseignant') }}">{{ __('menu.Emargement_enseignant') }}</a>
                            </li>
                            <li><a class="{{ $isActive('carte/professionnelle') }}" wire:current="active fw-bold"
                                    href="{{ url('carte/professionnelle') }}">{{ __('menu.Carte_professionnelle') }}</a></li>
                            <li><a class="{{ $isActive('certificat/travail') }}" wire:current="active fw-bold"
                                    href="{{ url('certificat/travail') }}">{{ __('menu.Certificat_travail') }}</a></li>
                            <li><a class="{{ $isActive('gestionnaire/paie/enseignant') }}"
                                    wire:current="active fw-bold"
                                    href="{{ url('gestionnaire/paie/enseignant') }}">{{ __('menu.Gestion_Paie_enseignant') }}</a>
                            </li>
                        @endif
                        @if (!empty(Permission('Comptabilite')))
                            <li><a class="{{ $isActive('reglement/paie/enseignant') }}"
                                    wire:current="active fw-bold"
                                    href="{{ url('reglement/paie/enseignant') }}">{{ __('menu.Reglement_paie_enseignant') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Scolarité --}}
            @if (!empty(Permission('Scolarite')) || !empty(Permission('Comptabilite')))
                <li class="heading">{{ __('menu.Scolarite') }}</li>
                @php
                    $scoPatterns = [
                        'preinscription',
                        'recherche/preinscrit',
                        'inscription',
                        'reglement/frais/scolaire',
                        'liste/inscrit',
                        'liste/absence/apprenant',
                        'carte/scolaire',
                        'certificat/scolarite',
                    ];
                @endphp
                <li class="{{ $isActive(...$scoPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$scoPatterns) }}">
                        <i class="sidebar-item-icon fa fa-briefcase"></i>
                        <span class="nav-label">{{ __('menu.Scolarite') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$scoPatterns) }}">
                        <li><a class="{{ $isActive('preinscription') }}" wire:current="active fw-bold"
                                href="{{ url('preinscription') }}">{{ __('menu.Pre_inscription') }}</a></li>
                        <li><a class="{{ $isActive('recherche/preinscrit') }}" wire:current="active fw-bold"
                                href="{{ url('recherche/preinscrit') }}">{{ __('menu.Recherche_preinscrit') }}</a>
                        </li>
                        @if (!empty(Permission('Comptabilite')))
                            <li><a class="{{ $isActive('inscription') }}" wire:current="active fw-bold"
                                    href="{{ url('inscription') }}">{{ __('menu.Inscription') }}</a></li>
                            <li><a class="{{ $isActive('reglement/frais/scolaire') }}" wire:current="active fw-bold"
                                    href="{{ url('reglement/frais/scolaire') }}">{{ __('menu.Reglement_frais_scolaire') }}</a>
                            </li>
                        @endif
                        @if (!empty(Permission('Scolarite')))
                            <li><a class="{{ $isActive('liste/inscrit') }}" wire:current="active fw-bold"
                                    href="{{ url('liste/inscrit') }}">{{ __('menu.Liste_inscrit') }}</a></li>
                            <li><a class="{{ $isActive('liste/absence/apprenant') }}" wire:current="active fw-bold"
                                    href="{{ url('liste/absence/apprenant') }}">{{ __('menu.Liste_absence_apprenant') }}</a>
                            </li>
                            <li><a class="{{ $isActive('carte/scolaire') }}" wire:current="active fw-bold"
                                    href="{{ url('carte/scolaire') }}">{{ __('menu.Carte_scolaire') }}</a></li>
                            <li><a class="{{ $isActive('certificat/scolarite') }}" wire:current="active fw-bold"
                                    href="{{ url('certificat/scolarite') }}">{{ __('menu.Certificat_scolarite') }}</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Programme --}}
            @if (!empty(Permission('Programme')))
                @php
                    $progPatterns = ['emplois/temps', 'liste/classe', 'note/apprenant', 'bulletin/scolaire'];
                @endphp
                <li class="{{ $isActive(...$progPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$progPatterns) }}">
                        <i class="sidebar-item-icon ti ti-calendar"></i>
                        <span class="nav-label">{{ __('menu.Programme') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$progPatterns) }}">
                        <li><a class="{{ $isActive('emplois/temps') }}" wire:current="active fw-bold"
                                href="{{ url('emplois/temps') }}">{{ __('menu.Emploi_du_temps') }}</a></li>
                        <li><a class="{{ $isActive('liste/classe') }}" wire:current="active fw-bold"
                                href="{{ url('liste/classe') }}">{{ __('menu.Liste_apprenant_par_classe') }}</a>
                        </li>
                        <li><a class="{{ $isActive('note/apprenant') }}" wire:current="active fw-bold"
                                href="{{ url('note/apprenant') }}">{{ __('menu.Note_apprenant_par_classe') }}</a>
                        </li>
                        <li><a class="{{ $isActive('bulletin/scolaire') }}" wire:current="active fw-bold"
                                href="{{ url('bulletin/scolaire') }}">{{ __('menu.Bulletin_scolaire') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Comptabilité --}}
            @if (!empty(Permission('Comptabilite')))
                <li class="heading">{{ __('menu.Comptabilite') }}</li>
                @php
                    $comptaPatterns = [
                        'date/comptable',
                        'gestion/caisse',
                        'appro/caisse',
                        'gestion/banque',
                        'encaissement/divers',
                        'gestion/depense',
                        'etat/caisse',
                        'historique/paiement/scolarite',
                        'historique/abonnement',
                        'historique/inscription',
                    ];
                @endphp
                <li class="{{ $isActive(...$comptaPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$comptaPatterns) }}">
                        <i class="sidebar-item-icon ti ti-wallet"></i>
                        <span class="nav-label">{{ __('menu.Comptabilite') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$comptaPatterns) }}">
                        @if (!empty(Permission('Tresorier')))
                            <li><a class="{{ $isActive('date/comptable') }}" wire:current="active fw-bold"
                                    href="{{ url('date/comptable') }}">{{ __('menu.Date_comptable') }}</a></li>
                        @endif
                        <li><a class="{{ $isActive('gestion/caisse') }}" wire:current="active fw-bold"
                                href="{{ url('gestion/caisse') }}">{{ __('menu.Gestion_caisse') }}</a></li>
                        <li><a class="{{ $isActive('appro/caisse') }}" wire:current="active fw-bold"
                                href="{{ url('appro/caisse') }}">{{ __('menu.Approvisionnement_caisse') }}</a></li>
                        @if (!empty(Permission('Tresorier')))
                            <li><a class="{{ $isActive('gestion/banque') }}" wire:current="active fw-bold"
                                    href="{{ url('gestion/banque') }}">{{ __('menu.Gestion_banque') }}</a></li>
                            <li><a class="{{ $isActive('encaissement/divers') }}" wire:current="active fw-bold"
                                    href="{{ url('encaissement/divers') }}">{{ __('menu.Encaissement_divers') }}</a>
                            </li>
                        @endif
                        <li><a class="{{ $isActive('gestion/depense') }}" wire:current="active fw-bold"
                                href="{{ url('gestion/depense') }}">{{ __('menu.Gestion_depense') }}</a></li>
                        <li><a class="{{ $isActive('etat/caisse') }}" wire:current="active fw-bold"
                                href="{{ url('etat/caisse') }}">{{ __('menu.Etat_caisse') }}</a></li>
                        <li><a class="{{ $isActive('historique/paiement/scolarite') }}"
                                wire:current="active fw-bold"
                                href="{{ url('historique/paiement/scolarite') }}">{{ __('menu.Historique_paiement_scolarite') }}</a>
                        </li>
                        <li><a class="{{ $isActive('historique/abonnement') }}" wire:current="active fw-bold"
                                href="{{ url('historique/abonnement') }}">{{ __('menu.Historique_abonnement_apprenant') }}</a>
                        </li>
                        <li><a class="{{ $isActive('historique/inscription') }}" wire:current="active fw-bold"
                                href="{{ url('historique/inscription') }}">{{ __('menu.Historique_inscription_apprenant') }}</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Abonnement --}}
            @if (!empty(Permission('Abonnement')) || !empty(Permission('Comptabilite')))
                <li class="heading">{{ __('menu.Abonnement') }}</li>
                @php
                    $aboPatterns = ['nouvel/abonnement', 'abonnement'];
                @endphp
                <li class="{{ $isActive(...$aboPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$aboPatterns) }}">
                        <i class="sidebar-item-icon ti ti-palette"></i>
                        <span class="nav-label">{{ __('menu.Abonnement') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$aboPatterns) }}">
                        @if (!empty(Permission('Comptabilite')))
                            <li><a class="{{ $isActive('nouvel/abonnement') }}" wire:current="active fw-bold"
                                    href="{{ url('nouvel/abonnement') }}">{{ __('menu.Nouvel_abonnement') }}</a>
                            </li>
                        @endif
                         @if (!empty(Permission('Abonnement')))
                        <li><a class="{{ $isActive('abonnement') }}" wire:current="active fw-bold"
                                href="{{ url('abonnement') }}">{{ __('menu.Suivie_abonne') }}</a></li>
                        @endif
                            </ul>
                </li>
            @endif
            {{-- Bibliothèque --}}
            @if (!empty(Permission(' Bibliotheque')) || !empty(Permission('Comptabilite')))
                <li class="heading">{{ __('menu.Bibliotheque') }}</li>
                @php
                    $biblioPatterns = ['livre', 'livre/acheter', 'livre/vendu', 'inventaire/livre', 'mouvement/livre'];
                @endphp
                <li class="{{ $isActive(...$biblioPatterns) }}">
                    <a href="javascript:;" aria-expanded="{{ $ariaExp(...$biblioPatterns) }}">
                        <i class="sidebar-item-icon ti ti-book"></i>
                        <span class="nav-label">{{ __('menu.Bibliotheque') }}</span>
                        <i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse {{ $isOpen(...$biblioPatterns) }}">
                        @if (!empty(Permission('Bibliotheque')))
                        <li><a class="{{ $isActive('livre') }}" wire:current="active fw-bold"
                                href="{{ url('livre') }}">{{ __('menu.Livre') }}</a></li>
                        <li><a class="{{ $isActive('livre/acheter') }}" wire:current="active fw-bold"
                                href="{{ url('livre/acheter') }}">{{ __('menu.Livre_acheter') }}</a></li>
                       @endif
                        @if (!empty(Permission('Comptabilite')))
                            <li><a class="{{ $isActive('livre/vendu') }}" wire:current="active fw-bold"
                                    href="{{ url('livre/vendu') }}">{{ __('menu.Livre_vendu') }}</a></li>
                        @endif
                        @if (!empty(Permission('Bibliotheque')))
                        <li><a class="{{ $isActive('inventaire/livre') }}" wire:current="active fw-bold"
                                href="{{ url('inventaire/livre') }}">{{ __('menu.Inventaire') }}</a></li>
                        <li><a class="{{ $isActive('mouvement/livre') }}" wire:current="active fw-bold"
                                href="{{ url('mouvement/livre') }}">{{ __('menu.Mouvement_stock') }}</a></li>
                     @endif
                            </ul>
                </li>
            @endif
        </ul>
    </div>
</nav>
