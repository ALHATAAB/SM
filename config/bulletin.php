<?php

/*
 * Réglages du bulletin scolaire.
 *
 * Moyenne d'une matière (par trimestre) =
 *   (note de classe × poids.classe + composition × poids.composition) / (somme des poids présents)
 * Si une seule des deux notes existe, elle sert de moyenne de la matière.
 * Moyenne générale = somme(moyenne matière × coefficient) / somme(coefficients des matières notées).
 */
return [

    // Barème des notes (affichage « / 20 »)
    'note_max' => 20,

    // Moyenne à atteindre pour la réussite
    'moyenne_passage' => 10,

    // Poids de chaque type de note dans la moyenne d'une matière
    'poids' => [
        'classe'      => 1,
        'composition' => 1,
    ],

    // Appréciation selon la moyenne (du seuil le plus haut au plus bas)
    'appreciations' => [
        [16, 'Très bien'],
        [14, 'Bien'],
        [12, 'Assez bien'],
        [10, 'Passable'],
        [8,  'Insuffisant'],
        [0,  'Très insuffisant'],
    ],

];
