<?php

namespace App\Livewire\Certificat;

use App\Services\CertificatService;

class CertificatTravail extends GestionCertificat
{
    protected function type(): string
    {
        return CertificatService::TRAVAIL;
    }
}
