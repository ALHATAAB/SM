<?php

/*
 * Réglages de la paie du personnel.
 *
 * fssp_taux : rubrique FSSP, en % du salaire net (calculé après CNSS et ITS).
 * Modifiable ici ou via PAIE_FSSP_TAUX dans le fichier .env.
 */
return [
    'fssp_taux' => (float) env('PAIE_FSSP_TAUX', 1),
];
