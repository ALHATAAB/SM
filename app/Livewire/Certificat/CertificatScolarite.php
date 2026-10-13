<?php

namespace App\Livewire\Certificat;

use App\Services\CertificatService;

class CertificatScolarite extends GestionCertificat
{
    protected function type(): string
    {
        return CertificatService::SCOLARITE;
    }
}
