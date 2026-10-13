# Corrections apportées à SM (à partir de SM_1.zip)

## 1. Libellés du menu et des écrans (lang/fr/menu.php, lang/fr/message.php)
Seuls les textes affichés changent ; les clés de traduction restent identiques.
- Parametre → **Paramètre** (et Paramètres généraux, Paramètre société, Paramètre utilisateurs, Paramètre rôle permission, Paramètre caisses / agences / banques, Paramètre Paie RH)
- Matière d'etude / Filière d'etude / Frais d'etude → **Matière d'étude / Filière d'étude / Frais d'étude**
- Reglement frais scolaire / Reglement de paie enseignant / Reglement paie → **Règlement …** (et Règlement salaire…)
- Emargement enseignant → **Émargement enseignant**
- Bibliotheque → **Bibliothèque**
- Etat de caisse → **État de caisse**
- Gestion depense → **Gestion dépense** ; Depense → **Dépense** ; Recette et depense → **Recette et dépense** ; Total depense → **Total dépense**
- Suivie abonné / Suivis cantine / Suivis transport → **Suivi abonné / Suivi cantine / Suivi transport**
- Liste presence apprenant / enseignant → **Liste présence …**

## 2. Messages affichés à l'utilisateur
- « Opération éffectuée avec succès » → **« Opération effectuée avec succès »** (≈ 30 fichiers)
- « Approvissionnement … » → **« Approvisionnement … »** (vue appro-caisse et message de rejet)
- « vente enregistré avec succès ! » → **« Vente enregistrée avec succès ! »**
- « Caisse appro fermé » / « Caisse source fermé » → **« … fermée »** ; « caisse fermée » → **« Caisse fermée »**
- « solde caisse insuffisant » → **« Solde de caisse insuffisant »** ; « solde banque insuffisant » → **« Solde banque insuffisant »**

## 3. Titres de fenêtres et libellés codés en dur (vues Blade)
- Frais d'étude : la fenêtre de modification s'appelait « Modifier Filière d'étude » → **« Modifier Frais d'étude »**
- Frais d'abonnement : « Ajouter abonnement » → **« Ajouter Frais abonnement »** ; « Modifier Filière d'étude » → **« Modifier Frais abonnement »**
- Gestion dépense : boutons « Reglement à la caisse / banque » → **« Règlement à la caisse / banque »**
- Gestion banque : « Depot banque » → **« Dépôt banque »**
- Fiche enseignant : « Emargement » → **« Émargement »**, « Emargement de l'annee » → **« Émargement de l'année »**
- Fiche employé : « Etat de paie de l'annee » → **« État de paie de l'année »**
- Tableau de bord : légende de graphique « Depense » → **« Dépense »**

## 4. Migration : colonnes utilisées par le code mais absentes des migrations
Nouveau fichier : `database/migrations/2026_10_04_100000_ajouter_colonnes_manquantes.php`
- `remise` dans `abonnement_models`, `historique_abonnement_models`, `vente_models`
- `caisse_id`, `banque_id`, `type_abonnement_id` dans `operation_depense_models`

Chaque ajout est protégé par `hasColumn` : sans effet si la colonne existe déjà (base de production).
Sans cette migration, enregistrer un abonnement, une vente ou une dépense échoue sur une installation neuve.
**À exécuter : `php artisan migrate`.**

## 5. Filtre par année scolaire (bogue)
`ListeAbsenceApprenant.php` et `DetailEnseignant.php` filtraient avec `whereYear('date', '2026-2027')`,
c'est-à-dire en comparant l'année civile à une chaîne « 2026-2027 » : les absences / émargements de
janvier à août 2027 étaient exclus. Le filtre utilise maintenant les dates de début et de fin de l'année scolaire active.

## 6. Guide utilisateur
`Guide_utilisateur_Smart_School_complet.docx` et `.pdf` remplacés par la version mise à jour (libellés alignés sur ces corrections ; chapitre 8 complété avec les trois nouvelles fonctions de paie).

## 7. Paie du personnel : rubriques automatiques manuelles, brut imposable / non imposable, périodicité
Migration : `database/migrations/2026_10_05_100000_add_options_rubriques_paie_personnel.php` (protégée par `hasColumn`,
les lignes existantes restent « automatiques » et « tout le temps »). **À exécuter : `php artisan migrate`.**

**a) Rubriques automatiques configurables à la main (CNSS, ITS, FSSP)**
- Sur la fiche détail d'un employé, le crayon de la ligne ouvre « Calcul de la rubrique » : *Automatique* (selon les barèmes) ou *Manuel* (montant saisi, base et taux facultatifs).
- Un montant manuel n'est jamais recalculé (`synchroniserModele` l'ignore). Les autres rubriques en tiennent compte : ITS calculé avec la CNSS saisie, FSSP sur le net après CNSS et ITS saisis.
- Parts patronales (CNSS, ANPE) : toujours calculées, sans bouton de modification. Départ en cours de mois : seuls les gains sont proratisés, un montant saisi reste inchangé.
- Badge « Auto » / « Manuel » sur chaque rubrique automatique.

**b) Répartition du brut en imposable / non imposable**
- Par défaut selon le type de la rubrique (« Brute I » = imposable, « Brute NI » = non imposable).
- Nouveau champ « Dont montant imposable » pour partager un gain (le reste est non imposable ; ne peut pas dépasser le montant). Le prorata conserve la proportion.
- CNSS sur le brut total ; ITS sur la part imposable. Totaux enregistrés sur la fiche de paie (`brut_imposable`, `brut_non_imposable`) et la part imposable sur chaque ligne du bulletin calculé.
- Affichage : fiche détail (colonnes + « dont brut imposable / non imposable »), état de paie à l'écran et imprimé (2 colonnes), bulletin de paie PDF (ligne « Brut imposable / Brut non imposable »).

**c) Rubrique « tout le temps » ou « ce mois seulement »**
- À l'ajout et à la modification : *Tout le temps (chaque mois)* ou *Ce mois seulement* avec choix du mois.
- Le calcul d'un mois reprend les rubriques permanentes et celles de ce mois ; un employé sans rubrique applicable ce mois n'a pas de fiche.
- Les rubriques « ce mois seulement » sont listées sous le bulletin modèle et n'entrent pas dans ses totaux ni dans la fiche imprimée.
- Contrôles : pas de doublon (permanente + ponctuelle, ou même mois deux fois) ; le retrait vise une ligne précise.

Fichiers : `CalculPaiePersonnelService`, `EtatPaie`, `DetailEmployer` (+ vue `detail-employer`), `BulletinPaieService`, `FicheService`, vues `etat_paie`, `editer-etat-paie`, `bulletin_paie_document`, modèle `ModelBulletinModel`.
Tests : `tests/Feature/RubriquesPaieOptionsTest.php` (23 tests). Les nouveaux libellés de ces écrans sont en français dans les vues (pas de clés `en` / `ar`).

## 8. Tests
`DB_CONNECTION=sqlite DB_DATABASE=:memory: php vendor/bin/phpunit` : 55 tests ; 2 échecs hérités de la version d'origine, sans lien avec ces corrections
(`test_ajout_depuis_la_fiche_employe_calcule_le_montant` : rendu de page non testable ; `ExampleTest` : redirection vers la connexion).
Le compte de rattachement attendu dans `test_migration_de_rattachement_pour_les_employes_existants` est passé de 4 à 6 (CNSS/ANPE patronales ajoutées avant ces corrections).

## Non modifié (à décider de votre côté)
- Le compte `Admin@ad.com` est créé avec le mot de passe `000000` par les migrations : à changer avant la mise en service.
- Plusieurs migrations déclarent des clés étrangères vers des tables inexistantes (`user`, `caisses`…) : MySQL les tolère, SQLite non.
- Les fichiers de langue `en` et `ar` n'ont pas été touchés.
- Le dossier `vendor copy/` (doublon de `vendor/`) peut être supprimé.

## 8. Paie : sens « Patronal », base CNSS/ANPE, rubriques automatiques / manuelles à formule, constantes
Migration : `database/migrations/2026_10_07_100000_rubriques_formules_constantes_patronal.php`. **À exécuter : `php artisan migrate`.**

**a) « Patronal (hors net) » dans la liste déroulante du Sens** (ajout et modification d'une rubrique).
Une rubrique « Patronal » est une charge employeur : jamais dans le brut, les retenues ni le net ; elle apparaît dans « Charges patronales » de la fiche employé et du bulletin.
Les rubriques CNSS (part patronale) et ANPE (part patronale) passent en « Patronal ».

**b) Base CNSS et ANPE = brut imposable** (gains « Brute I » et part imposable des gains partagés). Les gains « Brute NI » et la part non imposable ne sont plus dans l'assiette (`CalculPaiePersonnelService::calculer`). Le plafond par branche reste appliqué.

**c) Rubriques automatiques / manuelles.** Écran Rubrique : champ « Mode » = Manuel (montant saisi) ou Automatique (formule). CNSS, ITS, FSSP, CNSS/ANPE patronales restent « Automatique (barème) ». Sur la fiche employé, une rubrique à formule se calcule d'après base / taux / nombre, ou passe en « Manuel » pour saisir le montant ; elle est recalculée à chaque calcul de paie (constantes modifiées comprises) sauf en mode manuel.

**d) Formules** : `base*nombre`, `base*taux`, `base*taux*nombre`, `base+nombre`, `min(base;PLAFOND)*taux`, `si(base>SEUIL;base*taux;0)`… Variables `base`, `taux` (en %, 5 = 5 %), `nombre`, plus les constantes par leur code. Opérateurs + - * / ( ) et comparaisons ; fonctions min, max, round, abs, floor, ceil, si. Moteur sans `eval()` (`app/Services/FormuleService.php`) ; la formule est contrôlée à l'enregistrement.

**e) Constantes** : menu Paie → « Constante paie » (`/constante/paie`), catégories tranche (borne min `CODE` / max `CODE_MAX`), calcul, test, autre.

Tests : `tests/Unit/FormuleServiceTest.php`.

**f) Base de cotisation du bulletin = base CNSS** : `BulletinPaieService` reprend la base de la ligne CNSS (brut imposable plafonné) ; à défaut, le brut imposable. Avant, elle additionnait « Brute I » + « Brute NI ».

**g) Taux modifiables** : taux CNSS / ANPE (salarié, patronal, plafond) dans Paramètres paie → « Cotisations sociales (CNSS) » ; taux FSSP dans Paramètres paie → « Taux FSSP » (table `parametre_paie_models`, migration `2026_10_08_100000`). Les nouveaux taux s'appliquent aux prochains calculs de paie.

**h) Modifier / supprimer** : taux par charge familiale, barème ITS par taux et barème ITS par montant (icônes crayon et croix de Paramètres paie, auparavant inactives). Contrôles : borne max > borne min, taux de 0 à 100, nombre de charge unique, pas de chevauchement de tranches. Nouveaux taux appliqués aux prochains calculs de paie.

**i) Constantes dans Paramètres paie** : toutes les catégories de constantes (tranche, calcul, test, autre) sont gérées dans la carte « Constantes de paie » de Paramètres paie (onglets par catégorie). Le menu séparé est retiré ; l'ancienne adresse /constante/paie redirige vers Paramètres paie.

**j) Prime d'ancienneté** (migration `2026_10_08_110000`) : rubrique automatique `si(anciennete>=ANC_SEUIL;base*min(anciennete*ANC_TAUX_AN;ANC_PLAFOND)/100;0)`. Nouvelle variable de formule `anciennete` = années complètes depuis la date d'embauche. Constantes ANC_SEUIL (test, 2), ANC_TAUX_AN (calcul, 1) et ANC_PLAFOND (calcul, 25) : valeurs de départ à ajuster dans Paramètres paie.

**k) Rubriques bâties sur Paramètres paie** : à la création d'une rubrique automatique, un catalogue cliquable propose les variables de la ligne (base, taux, nombre, anciennete, charge_familiale, taux_charge), les constantes (par catégorie) et les paramètres de paie automatiques : FSSP_TAUX, CNSS_TAUX_SALARIE, CNSS_TAUX_PATRONAL, CNSS_PLAFOND, CNSS_<BRANCHE>_TAUX_PAT / _TAUX_SAL, ANPE_TAUX_PATRONAL, ANPE_PLAFOND, TAUX_CHARGE_<n>, ITS_T<i>_MIN/MAX/TAUX, ITS_M<i>_MIN/MAX/VALEUR (lecture seule, lus en direct). Aucun nombre écrit en dur dans une formule hors 0, 1 et 100 : il faut une constante. La fiche employé affiche les constantes utilisées avec leur valeur.

**l) Bulletin modèle = bulletin calculé et imprimé** : sur la fiche employé, un « Aperçu du bulletin de paie modèle » reprend exactement le contenu du bulletin imprimé (infos employé, brut, base de cotisation, brut imposable / non imposable, colonnes Base-Taux-Nombre, gains, retenues CNSS / ITS / FSSP, totaux, net à payer, charges patronales CNSS et ANPE). `BulletinPaieService::modelePersonnel()` s'appuie sur le même moteur de calcul ; le corps du bulletin est un partial unique `print/_bulletin_corps.blade.php` utilisé par le bulletin imprimé et par l'aperçu.

**m) Format du bulletin de salaire (modèle « simulation_bulletin »)** : le bulletin du personnel (PDF, impression et aperçu du bulletin modèle) reprend la présentation du fichier Excel : en-tête société avec date d'entrée / charges familiales / ancienneté, période, coût mensuel du poste (brut + charges patronales), gains, salaire brut, cotisations sociales et fiscales (base CNSS, retenue CNSS et taux, base ITS, taux d'abattement charges de famille, ITS, parts patronales en italique, total), salaire net, retenues sur salaire, net à payer, identité (matricule, nom, prénom, fonction, N° CNSS, catégorie, grade, échelon, compte, banque) et signatures. Partial : `print/_bulletin_format.blade.php`. Le bulletin des enseignants garde son format.

**m bis)** Le tableau du bulletin au nouveau format garde les colonnes **Base / Taux / Nombre / Gain / Retenue** (comme avant), avec lignes TOTAUX, SALAIRE NET, retenues sur salaire et tableau des charges patronales (Base / Taux / Montant) sous le net.

**n) Calcul conforme à la feuille « simulation_bulletin »** (migration `2026_10_09_100000`) : (1) Paramètres paie → « Calcul de l'ITS et des charges patronales » : abattement pour frais professionnels (13 % par défaut) et base des charges patronales (total des gains plafonné, ou base CNSS) ; (2) Rubrique : listes « Base CNSS » et « Base ITS » (selon le type / soumise / exclue) pour séparer les primes exclues de la CNSS de celles exclues de l'ITS ; (3) ITS : base retenues ITS = (brut imposable − CNSS − frais pro) arrondie au millier inférieur, puis abattement charges de famille, puis barème ; la ligne ITS affiche la « Base retenues ITS ». Contrôle sur l'exemple de la feuille (brut 370 000, transport exclu de la CNSS, 5 charges) : CNSS 18 638, base ITS 305 000, ITS 38 325, charges patronales 64 380, net 313 037 (feuille : 313 037,5, écart = arrondi de la CNSS). Tests mis à jour : ITS 28 325 / FSSP 2 559 / net 253 366 pour 300 000 et 2 charges.

**o) Fiche employé : tableau du bulletin modèle au format du bulletin** : l'« Aperçu du bulletin de paie modèle » est supprimé (point l) ; le tableau modifiable lui-même reprend maintenant les colonnes Base / Taux / Nombre / Gain / Retenue et les sections du bulletin : gains, salaire brut (dont imposable / non imposable), cotisations sociales et fiscales (CNSS, frais professionnels, ITS avec sa base, taux d'abattement charges de famille, total), salaire net, retenues sur salaire (FSSP, avances...), totaux, net à payer et charges patronales (CNSS / ANPE + rubriques « Patronal »). Les boutons de modification de chaque rubrique sont conservés.

## 9. Tableau de bord (`Home.php`, `livewire/home.blade.php` + `livewire/home/`)
**Permissions** : un bloc = une permission, les blocs sont indépendants (plus de `@elseif`).
- Finance + situation scolarité : **Parametre** ou **Tresorier** ; graphiques : **Parametre** ; cartes de configuration : **Parametre** ou **Configuration** ; message de bienvenue : tous sauf Parametre.
- Le test `empty(Permission('Tresorier'))` était inversé : le Trésorier ne voyait pas la finance, les autres la voyaient.

**Corrections de cohérence**
- Total dépense : l'expression `$gestionDepense ?? (0 + $gestionPaie ...)` ignorait la paie dès que `$gestionDepense` existait ; total recette et total dépense sont maintenant calculés une seule fois (mêmes variables dans les cartes et dans l'en-tête du graphique).
- Flèches : recettes ↑, dépenses ↓ partout ; icônes en double remplacées (banque, classe, enseignant) ; années affichées « — » (et non « 0 ») quand aucune année n'est active.
- Graphiques : les données ne sont plus codées en dur (29, 48, 40… / 47, 30, 45) ; courbes par mois de l'année scolaire active (`statsMensuelles`) et camembert Homme / Femme / Enfant (`statsInscrits`) calculés dans `Home.php`. Couleurs du camembert alignées sur la légende, courbe « Recette » entièrement verte.
- JS : plus d'erreur pour les profils sans graphiques (script chargé uniquement pour Parametre, via `@assets` / `@script`) ; code `#world-map` supprimé ; `wire:ignore` sur les canvas pour que la recherche ne les efface pas.
- Structure HTML : plus de `<script>` hors du composant ; balises `page-wrapper` / `content-wrapper` correctement fermées ; code dupliqué 3 fois remplacé par des partials et une carte réutilisable (`stat-card`).
- Tableau « Situation scolarité » : textes traduits, montants formatés, relations nulles gérées, lien factice `invoice.html` retiré, pagination remise à la page 1 lors d'une recherche, Entrée dans la recherche ne recharge plus la page.

**Traductions** : 15 clés ajoutées (fr / en / ar) ; traductions fausses corrigées : en « Total recipe » → « Total revenue », « Spent » → « Expense » ; ar « وصفة كاملة » (recette de cuisine) → « إجمالي الإيرادات », « قضى » → « مصروف », « نحيف » (mince) → « النساء ».

Contrôle des fichiers de langue : `message.php` et `menu.php` ont les mêmes clés en fr / en / ar (452 et 97), sans doublon ni clé manquante ; seule `Tableau_de_bord` était non traduite en anglais (« Tableau de bord » → « Dashboard »).

**Chart.js passé de la v2 à la v4.4.9** : nouveau fichier `public/assets/vendors/chart.js/dist/chart.umd.js` (l'ancien `Chart.min.js` v2 reste en place, non utilisé). `components/layouts/app.blade.php` charge maintenant la v4. Graphiques réécrits pour l'API v4 (légende dans `plugins`, axes dans `scales`, `cutout`, `fill` / `tension` explicites pour garder le rendu) ; montants avec séparateur de milliers sur l'axe et les infobulles ; le graphique est détruit avant d'être recréé. Seul le tableau de bord utilise Chart.js ; les scripts de démo de `public/assets/js/scripts/` (v2) ne sont inclus dans aucune vue.

**Nouveaux graphiques et filtre du tableau de bord** (profil Parametre)
- Filtre : année scolaire (liste, ★ = année active) + période facultative « Du / Au », boutons « Réinitialiser » ; les graphiques se redessinent sans recharger la page (événement Livewire `stats-updated`). Les cartes du haut restent sur l'année active.
- Nouveaux graphiques : **recettes par type** (inscription, scolarité, abonnement, vente de livres, encaissements divers), **dépenses par catégorie** (charges diverses, paiement enseignants, salaires du personnel), **impayés de scolarité par niveau** (même formule que la carte « Scolarité impayée »). Le camembert des inscrits et sa liste suivent l'année choisie.
- Les montants par mois et la période utilisent maintenant la **date comptable** (`date_comptable`) au lieu de `created_at`.
- Le total recette du graphique inclut désormais les **abonnements** (il les omettait alors que la carte « Abonnement » fait partie des recettes) : total recette = inscription + scolarité + abonnement + livres + encaissements divers.
- **Carte « Statistique inscrit »** : les enfants sont séparés en **Garçons / Filles** (selon le sexe de la pré-inscription), en plus de Hommes et Femmes ; la légende affiche l'**effectif et le pourcentage** de chaque catégorie (+ total) ; nouveau graphique de **répartition des inscrits par niveau, filière ou classe** (boutons de choix). Tout suit l'année scolaire du filtre. 6 clés de traduction ajoutées (fr / en / ar).
- Sécurité : le changement de filtre revérifie la permission Parametre et la validité de l'année côté serveur.
- 7 clés de traduction ajoutées (fr / en / ar).

Aucune migration. Après copie : `php artisan view:clear` et vider le cache du navigateur (Ctrl+F5).

## 10. Graphiques qui ne s'affichaient pas (Livewire)
Erreurs de la console : « The published Livewire assets are out of date », « Alpine Expression Error: expected expression, got keyword 'var' », « missing closing tags found ».
- **Assets Livewire périmés** : `public/vendor/livewire/livewire.js` (hash 13b7c601) ne correspondait plus au paquet installé (v3.6.4, hash df3a17f2) ; l'ancienne version évaluait le bloc `@script` comme une expression Alpine, d'où l'erreur. Les assets à jour sont fournis dans `public/vendor/livewire/` (équivalent de `php artisan livewire:publish --assets`).
- **Script des graphiques** : `@script` remplacé par un script classique (écoute l'événement `stats-updated` sur `window`), qui ne dépend plus de la version des assets Livewire.
- **`@livewireScripts` mal placé** : il était dans `components/layouts/footer.blade.php`, donc *à l'intérieur* de chaque composant (cause de « missing closing tags »). Il est maintenant en bas de `components/layouts/app.blade.php`, une seule fois.
- Après copie : `php artisan view:clear` puis Ctrl+F5.

## 11. Bulletins scolaires : affichage à l'impression + reçus de scolarité
**Bulletins scolaires** (menu Programme → Bulletins scolaires)
- Le PDF n'est plus *téléchargé de force* par Livewire : « Bulletin » (un élève) et « Imprimer tous les bulletins » ouvrent maintenant le PDF **dans un nouvel onglet** (`GET /bulletin/scolaire/imprimer?classe=…&periode=…[&eleve=…]`, `BulletinScolaireController`), prêt à imprimer.
- Messages d'aide ajoutés sur la page : aucun trimestre pour l'année active, aucune note saisie. Rappel : un bulletin n'apparaît que si, pour l'année scolaire **active**, la classe a des élèves inscrits (état actif), des **trimestres** (menu Trimestre), des **coefficients** par matière (Coefficient matière) et des notes (Note apprenant par classe).
- Permission requise : `Programme` (page et impression).

**Reçus**
- Reçu de paiement de scolarité (PDF A4 / 80 mm / 85 mm **et** page du reçu à l'écran) : nouvelle ligne **« Reste à payer »** = reste de la scolarité *après ce paiement* (reste actuel de l'inscription + paiements/remises enregistrés ensuite ; les paiements annulés sont ignorés). Une réimpression ultérieure affiche donc toujours le même montant.
- Paiement **non espèces** (DEPOT, TRANSFERT, CHEQUE…) : ligne **« Banque »** avec le nom de la banque (`banque_id` du paiement), avant le numéro de chèque / référence. Pour les paiements en espèces, rien n'est ajouté. Ceci s'applique à tous les reçus PDF (inscription, scolarité, abonnement, salaire, enseignant).
- Fichiers : `app/Services/RecuService.php`, `app/Livewire/Recu/RecuPaiementScolarite.php`, `resources/views/livewire/recu/recu-paiement-scolarite.blade.php`, `resources/views/livewire/print/recu/{_a4,_thermique,document}.blade.php`.
- Après copie : `php artisan route:clear` et `php artisan view:clear`.
