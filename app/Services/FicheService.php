<?php

namespace App\Services;

use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\HistoriquePaiementSalaireModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\Programme\SeanceModel;
use App\Models\Scolarite\AbsenceApprenantModel;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Models\Scolarite\ClasseEtudeModel;
use App\Models\Scolarite\DocumentInscriptionModel;
use App\Models\Scolarite\EmargementEnseignantModel;
use App\Models\Scolarite\EnseignantModel;
use App\Models\Scolarite\EtatPaieEnseignantModel;
use App\Models\Scolarite\FiliereEtudeModel;
use App\Models\Scolarite\HistoriqueAbonnementModel;
use App\Models\Scolarite\HistoriquePaiementEnseignantModel;
use App\Models\Scolarite\HistoriquePaiementScolariteModel;
use App\Models\Scolarite\InscriptionModel;
use App\Models\Scolarite\MatiereEtudeModel;
use App\Models\Scolarite\NiveauEtudeModel;
use App\Models\Scolarite\NoteApprenantModel;
use App\Models\Scolarite\PreInscriptionModel;
use App\Models\Scolarite\SalleEtudeModel;
use App\Models\Scolarite\TrimestreModel;
use App\Models\Scolarite\TypeAbonnementModel;
use Carbon\Carbon;

/**
 * Données des fiches individuelles (élève, enseignant, employé) : toutes les informations
 * liées à la personne, prêtes à être affichées dans un PDF ou une page.
 */
class FicheService
{
    /** Fiche d'un élève à partir de l'une de ses inscriptions ; l'historique couvre toutes ses années. */
    public function apprenant(int $inscriptionId): array
    {
        $inscription = InscriptionModel::where('etat', 1)->findOrFail($inscriptionId);
        $pre = PreInscriptionModel::findOrFail($inscription->pre_inscription_id);
        $doc = DocumentInscriptionModel::where('pre_inscription_id', $pre->id)->first();

        $niveaux  = NiveauEtudeModel::pluck('niveau', 'id');
        $filieres = FiliereEtudeModel::pluck('filiere', 'id');
        $classes  = ClasseEtudeModel::pluck('classe', 'id');
        $annees   = AnneeScolaireModel::pluck('annee', 'id');
        $matieres = MatiereEtudeModel::pluck('matiere_etude', 'id');
        $trims    = TrimestreModel::pluck('trimestre', 'id');
        $enseign  = EnseignantModel::get()->mapWithKeys(fn ($e) => [$e->id => trim($e->nom . ' ' . $e->prenom)]);
        $types    = TypeAbonnementModel::pluck('type_abonnement', 'id');

        $insc = InscriptionModel::where('pre_inscription_id', $pre->id)->where('etat', 1)->orderBy('annee_scolaire_id')->get();
        $ids  = $insc->pluck('id');

        $inscriptions = $insc->map(fn ($i) => [
            'annee'    => $annees[$i->annee_scolaire_id] ?? '',
            'matricule' => $i->matricule,
            'niveau'   => $niveaux[$i->niveau_etude_id] ?? '',
            'filiere'  => $filieres[$i->filiere_etude_id] ?? '',
            'classe'   => $classes[$i->classe_etude_id] ?? '',
            'inscription' => (float) $i->frais_inscription,
            'scolarite'   => (float) $i->frais_scolarite,
            'remise'      => (float) $i->remise,
        ])->all();

        $paiements = HistoriquePaiementScolariteModel::whereIn('inscription_id', $ids)
            ->where('etat', '!=', 0)->orderBy('date_comptable')->get()
            ->map(fn ($p) => [
                'date'   => $this->date($p->date_comptable),
                'annee'  => $annees[$p->annee_scolaire_id] ?? '',
                'libelle' => $p->description,
                'mode'   => $p->mode_paiement,
                'montant' => (float) $p->frais_payer,
                'remise' => (float) $p->remise,
            ])->all();

        $abonnements = HistoriqueAbonnementModel::where('pre_inscription_id', $pre->id)
            ->where('etat', '!=', 0)->orderBy('date_debut')->get()
            ->map(fn ($a) => [
                'annee'   => $annees[$a->annee_scolaire_id] ?? '',
                'type'    => $types[$a->type_abonnement_id] ?? '',
                'periode' => trim($a->nombre . ' ' . $a->periode),
                'debut'   => $this->date($a->date_debut),
                'fin'     => $this->date($a->date_fin),
                'mode'    => $a->mode_paiement,
                'montant' => (float) $a->frais_abonnement,
            ])->all();

        $absences = AbsenceApprenantModel::whereIn('inscription_id', $ids)->orderBy('date')->get()
            ->map(fn ($a) => [
                'date'    => $this->date($a->date),
                'horaire' => substr((string) $a->heure_debut, 0, 5) . ' - ' . substr((string) $a->heure_fin, 0, 5),
                'matiere' => $matieres[$a->matiere_etude_id] ?? '',
                'enseignant' => $enseign[$a->enseignant_id] ?? '',
                'motif'   => $a->motif,
                'justifiee' => (bool) $a->justifiee,
            ])->all();

        $notes = NoteApprenantModel::whereIn('apprenant_id', $ids)->orderBy('trimestre_id')->orderBy('matiere_etude_id')->get()
            ->map(fn ($n) => [
                'trimestre' => $trims[$n->trimestre_id] ?? '',
                'matiere'   => $matieres[$n->matiere_etude_id] ?? '',
                'classe'    => $classes[$n->classe_etude_id] ?? '',
                'type'      => $n->type === 'composition' ? 'Composition' : 'Classe',
                'note'      => (float) $n->note,
            ])->all();

        $totalDu   = collect($inscriptions)->sum(fn ($i) => $i['inscription'] + $i['scolarite']);
        $totalPaye = collect($paiements)->sum('montant');

        return [
            'type'      => 'apprenant',
            'photo'     => PhotoService::chemin('apprenant', $pre->id),
            'ecole'     => SocieteService::info(),
            'identite'  => array_values(array_filter([
                ['Matricule', $inscription->matricule],
                ['Nom', $pre->nom],
                ['Prénom', $pre->prenom],
                ['Sexe', $pre->sexe],
                ['Groupe', $pre->groupe],
                ['Date de naissance', $this->date($pre->date_naissance)],
                ['Lieu de naissance', $pre->lieu_naissance],
                ['Nationalité', $pre->nationalite ?: ($doc->nationnalite ?? null)],
                ['Pays', $pre->pays],
                ['Téléphone', $pre->telephone],
                ['Adresse', $pre->adresse],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'parent'    => array_values(array_filter([
                ['Nom et prénom', $pre->prenom_parent],
                ['Téléphone', $pre->telephone_parent],
                ['Adresse', $pre->adresse_parent],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'documents' => array_values(array_filter([
                ['Photo d\'identité', $doc?->photo_identite ? 'Fournie' : null],
                ['Acte de naissance', $doc?->acte_naissance ? 'Fourni' : null],
                ['Dernier diplôme', $doc?->dernier_diplome],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'inscriptions' => $inscriptions,
            'paiements'    => $paiements,
            'abonnements'  => $abonnements,
            'absences'     => $absences,
            'notes'        => $notes,
            'resume' => [
                'du'     => (float) $totalDu,
                'remise' => (float) collect($inscriptions)->sum('remise'),
                'paye'   => (float) $totalPaye,
                'reste'  => (float) ($totalDu - collect($inscriptions)->sum('remise') - $totalPaye),
                'absences' => count($absences),
                'absences_non_justifiees' => collect($absences)->where('justifiee', false)->count(),
            ],
            'nom_complet' => trim($pre->nom . ' ' . $pre->prenom),
        ];
    }

    public function enseignant(int $id): array
    {
        $e = EnseignantModel::findOrFail($id);

        $matieres = MatiereEtudeModel::pluck('matiere_etude', 'id');
        $classes  = ClasseEtudeModel::pluck('classe', 'id');
        $salles   = SalleEtudeModel::pluck('salle', 'id');
        $annees   = AnneeScolaireModel::pluck('annee', 'id');
        $mois     = MoisPaieModel::pluck('mois', 'id');

        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $seances = SeanceModel::where('enseignant_id', $e->id)->orderBy('heure_debut')->get()
            ->sortBy(fn ($s) => (array_search($s->jour, $jours) === false ? 9 : array_search($s->jour, $jours)) . $s->heure_debut)
            ->map(fn ($s) => [
                'jour'    => $s->jour,
                'horaire' => substr((string) $s->heure_debut, 0, 5) . ' - ' . substr((string) $s->heure_fin, 0, 5),
                'matiere' => $matieres[$s->matiere_id] ?? '',
                'classe'  => $classes[$s->classe_id] ?? '',
                'salle'   => $salles[$s->salle_id] ?? '',
            ])->values()->all();

        $anneeActive = AnneeScolaire();
        $emargements = EmargementEnseignantModel::where('enseignant_id', $e->id)
            ->when($anneeActive, fn ($q) => $q->where('annee_scolaire_id', $anneeActive->id))
            ->orderBy('date')->get();

        $presences = $emargements->where('status', 'present');

        // Heures effectuées, regroupées par matière et classe (année scolaire active)
        $parMatiere = $presences->groupBy(fn ($m) => $m->matiere_etude_id . '-' . $m->classe_etude_id)
            ->map(fn ($g) => [
                'matiere' => $matieres[$g->first()->matiere_etude_id] ?? '',
                'classe'  => $classes[$g->first()->classe_etude_id] ?? '',
                'seances' => $g->count(),
                'heures'  => (float) $g->sum('nombre_heure'),
            ])->values()->all();

        $paiements = HistoriquePaiementEnseignantModel::where('enseignant_id', $e->id)
            ->where('etat', '!=', 0)->orderBy('date_comptable')->get()
            ->map(fn ($p) => [
                'date'    => $this->date($p->date_comptable),
                'periode' => trim(($mois[$p->mois_id] ?? '') . ' ' . ($annees[$p->annee_id] ?? '')),
                'mode'    => $p->mode_paiement,
                'montant' => (float) $p->salaire_payer,
            ])->all();

        $etats = EtatPaieEnseignantModel::where('enseignant_id', $e->id)->orderBy('id')->get()
            ->map(fn ($p) => [
                'periode' => trim(($mois[$p->mois_id] ?? '') . ' ' . $p->annee),
                'base'    => (float) $p->montant_base,
                'hsup'    => (float) $p->heures_sup,
                'montant_hsup' => (float) $p->montant_heures_sup,
                'total'   => (float) $p->total_paye,
                'statut'  => $p->statut === 'payé' ? 'Payé' : 'En attente',
            ])->all();

        $contrats = ['fixe' => 'Fixe', 'horaire' => 'Horaire', 'mixte' => 'Mixte'];

        return [
            'type'  => 'enseignant',
            'photo' => PhotoService::chemin('enseignant', $e->id),
            'ecole' => SocieteService::info(),
            'identite' => array_values(array_filter([
                ['Matricule', $e->matricule],
                ['Nom', $e->nom],
                ['Prénom', $e->prenom],
                ['Sexe', $e->sexe],
                ['Date de naissance', $this->date($e->date_naissance)],
                ['Lieu de naissance', $e->lieu_naissance],
                ['Téléphone', $e->telephone],
                ['Email', $e->email],
                ['Adresse', $e->adresse],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'professionnel' => array_values(array_filter([
                ['Date d\'embauche', $e->date_embauche ? $this->date($e->date_embauche) : null],
                ['Date de départ', $e->date_depart ? $this->date($e->date_depart) : null],
                ['Spécialité', $e->specialite],
                ['Département', $e->departement],
                ['Matières enseignées', $e->matieres],
                ['Grade', $e->grade],
                ['Statut', $e->statut],
                ['Ancienneté', $e->anciennete !== null ? $e->anciennete . ' an(s)' : null],
                ['Type de contrat', $contrats[$e->type_contrat] ?? $e->type_contrat],
                ['Salaire de base', (float) $e->salaire_base > 0 ? $this->montant($e->salaire_base) : null],
                ['Salaire horaire', (float) $e->salaire_horaire > 0 ? $this->montant($e->salaire_horaire) : null],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'seances'     => $seances,
            'annee'       => $anneeActive?->annee,
            'presence'    => [
                'presents' => $presences->count(),
                'absents'  => $emargements->where('status', 'absent')->count(),
                'heures'   => (float) $presences->sum('nombre_heure'),
            ],
            'heures_par_matiere' => $parMatiere,
            'etats_paie'  => $etats,
            'paiements'   => $paiements,
            'total_paye'  => (float) collect($paiements)->sum('montant'),
            'nom_complet' => trim($e->nom . ' ' . $e->prenom),
        ];
    }

    public function employe(int $id): array
    {
        $e = EmployerModel::findOrFail($id);

        $annees = AnneeScolaireModel::pluck('annee', 'id');
        $mois   = MoisPaieModel::pluck('mois', 'id');

        // Bulletin modèle « standard » : rubriques permanentes (celles « ce mois seulement » ne comptent que pour leur mois).
        $lignes = ModelBulletinModel::with('rubrique')->where('employer_id', $e->id)->where('permanente', true)->get()
            ->sortBy(fn ($l) => $l->rubrique->code_rubrique ?? PHP_INT_MAX);

        $gains = $lignes->filter(fn ($l) => (float) $l->montant_gain != 0)
            ->map(fn ($l) => ['libelle' => $l->rubrique->rubrique ?? 'Rubrique', 'montant' => (float) $l->montant_gain])->values()->all();

        $retenues = $lignes->filter(fn ($l) => (float) $l->montant_retenu != 0)
            ->map(fn ($l) => ['libelle' => $l->rubrique->rubrique ?? 'Rubrique', 'montant' => (float) $l->montant_retenu])->values()->all();

        $fiches = FichePaieModel::where('employer_id', $e->id)->orderBy('annee_id')->orderBy('mois_id')->get()
            ->map(fn ($f) => [
                'periode' => trim(($mois[$f->mois_id] ?? '') . ' ' . ($annees[$f->annee_id] ?? '')),
                'gain'    => (float) $f->total_gain,
                'retenu'  => (float) $f->total_retenu,
                'net'     => (float) $f->salaire_net,
                'statut'  => strtoupper((string) $f->statuts) === 'PAIE' ? 'Payé' : 'En cours',
            ])->all();

        $paiements = HistoriquePaiementSalaireModel::where('employer_id', $e->id)->orderBy('date_comptable')->get()
            ->map(fn ($p) => [
                'date'    => $this->date($p->date_comptable),
                'periode' => trim(($mois[$p->mois_id] ?? '') . ' ' . ($annees[$p->annee_id] ?? '')),
                'mode'    => $p->mode_paiement,
                'montant' => (float) $p->salaire_payer,
            ])->all();

        return [
            'type'  => 'employer',
            'photo' => PhotoService::chemin('employer', $e->id),
            'ecole' => SocieteService::info(),
            'identite' => array_values(array_filter([
                ['Matricule', $e->matricule],
                ['Nom', $e->nom],
                ['Prénom', $e->prenom],
                ['Sexe', $e->sexe],
                ['Date de naissance', $this->date($e->date_naissance)],
                ['Lieu de naissance', $e->lieu_naissance],
                ['Téléphone', $e->telephone],
                ['Email', $e->email],
                ['Adresse', $e->adresse],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'professionnel' => array_values(array_filter([
                ['Poste', $e->poste],
                ['Date d\'embauche', $this->date($e->date_embauche)],
                ['Date de départ', $e->date_depart ? $this->date($e->date_depart) : null],
                ['Charge familiale', $e->charge_familliale],
                ['N° CNSS', $e->numero_cnss],
                ['Salaire', (float) $e->salaire > 0 ? $this->montant($e->salaire) : null],
            ], fn ($l) => $l[1] !== null && $l[1] !== '')),
            'gains'      => $gains,
            'retenues'   => $retenues,
            'fiches'     => $fiches,
            'paiements'  => $paiements,
            'total_net_paye' => (float) collect($paiements)->sum('montant'),
            'nom_complet' => trim($e->nom . ' ' . $e->prenom),
        ];
    }

    protected function date($valeur): ?string
    {
        return $valeur ? Carbon::parse($valeur)->format('d/m/Y') : null;
    }

    protected function montant($valeur): string
    {
        return number_format((float) $valeur, 0, ',', ' ') . ' F CFA';
    }
}
