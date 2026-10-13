# Emploi du temps (tableau croisé) et émargement enseignant — saisie groupée

Copier les 8 fichiers de ce paquet dans le projet SM (même arborescence, en écrasant) :

- app/Models/Programme/SeanceModel.php
- app/Livewire/Programme/EmploiDuTemps.php
- app/Livewire/Scolarite/EmargementEnseignant.php
- resources/views/livewire/programme/emploi-du-temps.blade.php
- resources/views/livewire/scolarite/emargement-enseignant.blade.php
- resources/views/livewire/print/_emploi_croise.blade.php   (NOUVEAU)
- resources/views/livewire/print/emploi_du_temps.blade.php
- resources/views/livewire/print/emploi_enseignant.blade.php

Aucune migration, aucune nouvelle route. Ensuite : `php artisan view:clear`.

## Emploi du temps (/emplois/temps) — tableau croisé
- 1re colonne = créneaux horaires (début – fin, modifiables), en-tête = jours de la semaine (dimanche en option).
- Chaque cellule = une séance : matière, salle, enseignant (vue par classe) ou classe (vue par enseignant). Cellule vide = pas de cours.
- « Ajouter un créneau horaire », « Trier par heure », « Copier » (recopie la 1re séance de la ligne sur les jours vides), « vider » sur une cellule.
- UN clic sur « Enregistrer l'emploi du temps » crée, modifie et supprime tout en une transaction. Les cellules vidées et les créneaux supprimés suppriment leurs séances.
- Conflits (enseignant / salle / classe) vérifiés dans le tableau et contre la base ; la cellule fautive est signalée.
- Messages précis, par exemple : « Salle B2 déjà occupée par la classe 6ème A (Diallo Amadou, Maths) le Lundi de 08:00 à 10:00. »
- Les PDF (par classe et par enseignant) sont aussi en tableau croisé.

## Émargement enseignant (/emargement/enseignant)
- Date + enseignant (ou classe) : séances du jour listées, statut par ligne ou « Tous présents / absents », un seul enregistrement.
- Historique filtrable (enseignant, classe, dates) avec suppression groupée.

## Corrections au passage
- seance_id est maintenant enregistré dans l'émargement.
- Le jour de la semaine est calculé en français quelle que soit la langue de l'application.
- Le filtre de l'historique ne renvoyait rien quand seules les dates étaient saisies.
