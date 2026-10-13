# Reçus (A4 / thermique, 2 exemplaires) et certificats — notice d'installation

## 1. Copier les fichiers
Décompressez `SM_modifications.zip` **par-dessus** votre dossier `SM` (mêmes chemins, aucun fichier de `vendor` touché).

Fichiers **supprimés** (remplacés par le nouveau gabarit, à effacer s'ils existent encore) :
`resources/views/livewire/print/recu_inscription.blade.php`, `recu_preinscription.blade.php`,
`recu_paiement_scolarite.blade.php`, `recu_paiement_salaire.blade.php`,
`recu_paiement_enseignant.blade.php`, `recu_abonnement.blade.php`.

## 2. Migration + caches
```
php artisan migrate
php artisan config:clear
php artisan view:clear
php artisan route:clear
```
La migration crée la table `certificat_models` (elle ne fait rien si la table existe déjà).

## 3. Reçus
- Concernés : pré-inscription, inscription, paiement de scolarité, abonnement, salaire, paiement enseignant.
- Sur chaque page de reçu : menu **Format** (A4 / Thermique 80 mm / Thermique 85 mm) + bouton **Imprimer**.
  Le PDF s'ouvre dans un nouvel onglet ; le choix est mémorisé pour l'utilisateur (session).
- **Deux exemplaires** à chaque impression : « Exemplaire client » et « Exemplaire école ».
  - A4 : les deux sur la même feuille, avec un trait « Couper ici » (un exemplaire par feuille si le reçu dépasse 13 lignes).
  - Thermique : un ticket par exemplaire ; la hauteur du ticket s'adapte au contenu.
- Les reçus de paiement affichent le montant en chiffres et **en lettres**, la date de l'opération, le caissier et les zones de signature.
- Adresse directe : `/recu/imprimer/{type}/{id chiffré}?format=a4|t80|t85`
  (types : preinscription, inscription, scolarite, abonnement, salaire, enseignant).

## 4. Certificats
- Menu **Scolarité → Certificats de scolarité** (élèves inscrits sur l'année active).
- Menu **Enseignants → Certificats de travail** et **Personnel administratif → Certificats de travail**.
- Actions : Générer (formulaire), Imprimer (PDF A4), Duplicata / nouvelle édition (remplace l'ancien, mention « DUPLICATA »), Annuler.
- Numérotation automatique : `CS-<année scolaire>-00001` et `CT-<année>-00001`.
- Certificat de travail : date de prise de fonction, date de fin (vide = toujours en poste), fonction, mention « libre de tout engagement ».
  Valeurs proposées : date d'embauche et poste pour le personnel ; date de création de la fiche et spécialité pour les enseignants (modifiables).
- Permissions : mêmes que les cartes (`Scolarite` pour élèves/enseignants, `Employer` pour le personnel ; `Ajouter` pour émettre, `Modifier` pour annuler).

## 5. Réglages (facultatif, dans `.env`)
```
DOC_ENTETE_PAYS="RÉPUBLIQUE DU NIGER"
DOC_DEVISE_PAYS="Fraternité - Travail - Progrès"
DOC_VILLE="Niamey"
DOC_SIGNATAIRE=""            # nom proposé du signataire
DOC_QUALITE="Le Directeur"
DOC_MONNAIE="FCFA"
```
Les formats proposés et les libellés des exemplaires se modifient dans `config/documents.php`.

## 6. À vérifier après installation
Ce code a été écrit sans pouvoir exécuter PHP/DomPDF dans l'environnement de travail : faites un essai d'impression de chaque reçu (A4, 80 mm, 85 mm)
et de chaque certificat. Un test unitaire est fourni pour le montant en lettres : `php artisan test --filter=MontantEnLettresTest`.
