# Intégration du contrôle de licence (période d'expiration) — SmartSchool

Ce patch ajoute UNIQUEMENT le contrôle de la période de validité de la licence.
Le dépassement de quota (inscrits/utilisateurs) est calculé et affiché sur la
page /licence à titre indicatif, mais ne bloque rien pour l'instant.

## 1. Copier les fichiers dans votre projet

```
app/Models/LicenceModel.php                       -> app/Models/
app/Services/LicenceService.php                   -> app/Services/
app/Http/Middleware/VerifieLicence.php            -> app/Http/Middleware/
app/Http/Controllers/LicenceController.php        -> app/Http/Controllers/
config/licence.php                                -> config/
resources/views/licence/*.blade.php               -> resources/views/licence/
database/migrations/2026_09_21_000000_create_licence_table.php -> database/migrations/
```

## 2. Remplacer ces deux fichiers (déjà modifiés dans ce patch)

```
bootstrap/app.php   -> remplace le vôtre (ajoute l'alias 'verifie.licence')
routes/web.php      -> remplace le vôtre (ajoute les routes /licence + le middleware)
```

Si vous avez modifié ces fichiers depuis, comparez-les avant d'écraser :
seule l'ajout de `VerifieLicence`, l'alias `verifie.licence`, et les 3 routes
`/licence*` ont été ajoutés — rien d'autre n'a changé.

## 3. Ajouter dans le `.env`

```env
LICENCE_ID_CLIENT=ALHATAAB_SMARTSCHOOL_01
LICENCE_CODE_MAITRE=GESTARDI_SECRET_TOP_2026
```

`LICENCE_ID_CLIENT` : identifiant unique de CETTE installation. Il doit être
exactement celui que vous saisirez dans le générateur `admin_licence.php`
quand vous créerez une licence pour cette école.

`LICENCE_CODE_MAITRE` : doit être identique à `$CODE_MAITRE` dans
`admin_licence.php` (le générateur, resté chez vous).

Puis :
```
php artisan config:clear
```

## 4. Migration (si la table licence n'existe pas encore sur un autre serveur)

```
php artisan migrate
```

Sur votre serveur actuel, la table `licence` existe déjà (import SQL) —
la migration ne fera rien (elle vérifie `Schema::hasTable`) donc c'est sans
risque de la lancer quand même.

## 5. Générer une licence valide pour CE serveur

Sur votre machine, ouvrez `admin_licence.php` (fourni précédemment), et
générez une licence avec :
- ID CLIENT = exactement la valeur de `LICENCE_ID_CLIENT` ci-dessus
- Nombre d'inscrits et d'utilisateurs souhaités
- Dates de début/fin

Téléchargez le `.zip`, connectez-vous à l'application en tant qu'admin,
allez sur `/licence`, et importez le fichier.

⚠️ La licence de test actuellement dans votre base
(`CLE_ACTIVATION = 8ca747771b9d...`) a été générée par l'ancien script avec
un ID_CLIENT qui n'a jamais été fixé dans votre `.env` — elle ne passera donc
PAS la vérification de signature tant que vous n'aurez pas importé une
licence régénérée avec le bon `LICENCE_ID_CLIENT`. C'est volontaire et
attendu : générez et importez une licence fraîche via `/licence` pour activer
le contrôle proprement.

## 6. Comportement obtenu

- Licence absente, signature invalide, ou date du jour hors de
  `[DATE_DEBUT, DATE_FIN]` → toutes les pages sous `admin + verifie.licence`
  (home, scolarité, comptabilité, paie RH, etc.) redirigent vers
  `/licence/bloquee`.
- `/licence`, `/licence/importer`, `/licence/bloquee` restent accessibles à
  tout administrateur connecté, licence valide ou non, pour permettre le
  renouvellement.
- La page de connexion (`/`) n'est pas affectée.

## Pour la suite (non inclus ici)

Le blocage par quota (inscrits/utilisateurs) peut être ajouté plus tard en
appelant `LicenceService::inscritDisponible()` /
`LicenceService::utilisateurDisponible()` avant la création d'un inscrit ou
d'un utilisateur — dites-le-moi quand vous voudrez l'activer.
