<?php

namespace App\Http\Controllers;

use App\Services\FicheService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

/**
 * Fiches individuelles (PDF) : élève, enseignant, employé.
 * Les identifiants viennent de l'URL chiffrée (comme les pages « détail »).
 */
class FicheController extends Controller
{
    public function apprenant(string $id, FicheService $service)
    {
        $this->exiger('Scolarite');

        return $this->pdf('livewire.print.fiche_apprenant', $service->apprenant($this->id($id)), 'fiche-apprenant.pdf');
    }

    public function enseignant(string $id, FicheService $service)
    {
        $this->exiger('Consulter');

        return $this->pdf('livewire.print.fiche_enseignant', $service->enseignant($this->id($id)), 'fiche-enseignant.pdf');
    }

    public function employer(string $id, FicheService $service)
    {
        $this->exiger('Employer');

        return $this->pdf('livewire.print.fiche_employer', $service->employe($this->id($id)), 'fiche-personnel.pdf');
    }

    protected function pdf(string $vue, array $fiche, string $fichier)
    {
        ini_set('max_execution_time', 300);

        $pdf = Pdf::loadView($vue, ['f' => $fiche, 'ecole' => $fiche['ecole']])->setPaper('a4', 'portrait');

        // Ouvre le PDF dans le navigateur (impression / téléchargement possibles).
        return $pdf->stream(str_replace('.pdf', '-' . \Illuminate\Support\Str::slug($fiche['nom_complet']) . '.pdf', $fichier));
    }

    protected function exiger(string $slug): void
    {
        abort_unless(Auth::check(), 401);
        abort_if(empty(Permission($slug)), 403, 'Accès refusé.');
    }

    protected function id(string $chiffre): int
    {
        try {
            $id = decrypt($chiffre);
        } catch (DecryptException $e) {
            abort(404);
        }

        abort_unless(is_numeric($id) && (int) $id > 0, 404);

        return (int) $id;
    }
}
