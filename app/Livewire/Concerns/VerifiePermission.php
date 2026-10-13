<?php

namespace App\Livewire\Concerns;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôles d'accès côté SERVEUR pour les composants Livewire.
 *
 * Masquer un bouton dans la vue (@if Permission(...)) ne protège rien :
 * une méthode publique Livewire peut être appelée directement par le client.
 * Chaque page ET chaque action sensible doit donc vérifier la permission ici.
 */
trait VerifiePermission
{
    /** Exige TOUTES les permissions listées, sinon 403. */
    protected function exigerPermissions(string ...$slugs): void
    {
        abort_unless(Auth::check(), 401);

        foreach ($slugs as $slug) {
            abort_if(empty(Permission($slug)), 403, 'Accès refusé.');
        }
    }

    /** Déchiffre un id venu de l'URL ; 404 si falsifié / invalide (au lieu d'une erreur 500). */
    protected function idDepuisUrl(string $chiffre): int
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
