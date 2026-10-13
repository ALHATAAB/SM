<?php

return [

    /*
     * Identifiant unique de CETTE installation cliente.
     * Doit être IDENTIQUE à l'ID_CLIENT saisi dans le générateur (admin_licence.php)
     * lors de la création de la licence pour cet établissement précis.
     */
    'id_client' => env('LICENCE_ID_CLIENT'),

    /*
     * Secret partagé avec le générateur de licence (admin_licence.php).
     * Ne JAMAIS versionner ce fichier avec la vraie valeur — utilisez le .env.
     */
    'code_maitre' => env('LICENCE_CODE_MAITRE'),

];
