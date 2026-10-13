# SmartSchool — enchaînement inscription, carte professionnelle du personnel, QR code

Copier les dossiers `app/`, `resources/` et `routes/` à la racine du projet (en écrasant).
Aucune migration, aucune dépendance Composer à installer. Ensuite :

```
php artisan view:clear
php artisan route:clear
```

## Ce qui change
1. **Pré-inscription → inscription** : le reçu de pré-inscription a un bouton « Enchaîner l'inscription »
   (visible avec la permission Comptabilité). Il ouvre `/inscription?matricule=XXX` avec le formulaire déjà rempli.
   Même raccourci « Inscrire » dans la recherche des pré-inscrits.
2. **Carte professionnelle du personnel** : dans les formulaires Employé et Enseignant, case
   « Générer la carte professionnelle » (cochée par défaut). Dans les deux listes, bouton carte :
   imprimer si elle existe, sinon la générer.
3. **QR code** au verso des cartes (scolaires et professionnelles). Il contient l'adresse
   `/carte/verifier/{numéro}` : page qui indique si la carte est valable (réservée aux utilisateurs connectés).
   Générateur en PHP pur (`app/Services/QrCode.php`), sans bibliothèque.
4. Adresse et téléphone de l'école passent sur une ligne sous le QR au verso de la carte.
