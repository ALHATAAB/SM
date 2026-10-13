<?php

/*
 * Réglages des documents imprimés : reçus (A4 / thermique) et certificats.
 * Chaque valeur peut être surchargée dans le .env (clés DOC_*).
 */
return [

    // En-tête officiel des certificats
    'entete_pays' => env('DOC_ENTETE_PAYS', 'RÉPUBLIQUE DU NIGER'),
    'devise_pays' => env('DOC_DEVISE_PAYS', 'Fraternité - Travail - Progrès'),

    // Ville inscrite dans « Fait à … le … »
    'ville' => env('DOC_VILLE', 'Niamey'),

    // Signataire proposé par défaut dans les certificats (modifiable à chaque émission)
    'signataire' => env('DOC_SIGNATAIRE', ''),
    'qualite'    => env('DOC_QUALITE', 'Le Directeur'),

    // Monnaie
    'monnaie'         => env('DOC_MONNAIE', 'FCFA'),
    'monnaie_lettres' => env('DOC_MONNAIE_LETTRES', 'francs CFA'),

    // Taux de la retenue ITS affichée sur le reçu de paiement d'enseignant (en %)
    'its_enseignant'  => env('DOC_ITS_ENSEIGNANT', 5),

    'recu' => [
        // Format proposé au premier affichage (ensuite le choix de l'utilisateur est mémorisé en session)
        'format_defaut' => 'a4',

        // a4 : 2 exemplaires sur la même feuille. t80 / t85 : papier thermique, 1 ticket par exemplaire.
        'formats' => [
            'a4'  => ['libelle' => 'A4',              'largeur_mm' => null],
            't80' => ['libelle' => 'Thermique 80 mm', 'largeur_mm' => 80],
            't85' => ['libelle' => 'Thermique 85 mm', 'largeur_mm' => 85],
        ],

        'exemplaires' => ['Exemplaire client', 'Exemplaire école'],
    ],
];
