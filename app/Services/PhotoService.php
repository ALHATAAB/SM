<?php

namespace App\Services;

use App\Models\PaieRH\EmployerModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\PreInscriptionModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Photos des personnes : élève (rattachée à sa pré-inscription, donc conservée d'une année à
 * l'autre), enseignant et employé. Fichiers dans storage/app/public/photos/..., servis par la
 * route « photo.personne » (aucun lien symbolique public/storage nécessaire).
 */
class PhotoService
{
    public const TYPES = [
        'apprenant'  => [PreInscriptionModel::class, 'photos/apprenants'],
        'enseignant' => [EnseignantModel::class, 'photos/enseignants'],
        'employer'   => [EmployerModel::class, 'photos/personnel'],
    ];

    public static function modele(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        return self::TYPES[$type][0]::find($id);
    }

    /** Chemin absolu de la photo (pour DomPDF), ou null s'il n'y en a pas. */
    public static function chemin(string $type, int $id): ?string
    {
        $photo = self::modele($type, $id)?->photo;

        return ($photo && Storage::disk('public')->exists($photo)) ? Storage::disk('public')->path($photo) : null;
    }

    /** URL de la photo pour les pages web (paramètre de version : pas de cache périmé), ou null. */
    public static function url(string $type, int $id): ?string
    {
        $photo = self::modele($type, $id)?->photo;

        if (!$photo || !Storage::disk('public')->exists($photo)) {
            return null;
        }

        return route('photo.personne', ['type' => $type, 'id' => $id, 'v' => Storage::disk('public')->lastModified($photo)]);
    }

    /** Enregistre la photo et supprime l'ancienne. */
    public static function enregistrer(string $type, int $id, UploadedFile $fichier): void
    {
        $modele = self::modele($type, $id);
        abort_unless($modele, 404);

        $ancienne = $modele->photo;
        $modele->update(['photo' => $fichier->store(self::TYPES[$type][1], 'public')]);

        if ($ancienne && Storage::disk('public')->exists($ancienne)) {
            Storage::disk('public')->delete($ancienne);
        }
    }

    public static function supprimer(string $type, int $id): void
    {
        $modele = self::modele($type, $id);
        abort_unless($modele, 404);

        if ($modele->photo && Storage::disk('public')->exists($modele->photo)) {
            Storage::disk('public')->delete($modele->photo);
        }

        $modele->update(['photo' => null]);
    }
}
