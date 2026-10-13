<?php

namespace App\Livewire\PaieRH;

use App\Models\PaieRH\BulletinCalculerModel;
use App\Models\PaieRH\EmployerModel;
use App\Models\PaieRH\FichePaieModel;
use App\Models\PaieRH\ModelBulletinModel;
use App\Models\PaieRH\MoisPaieModel;
use App\Models\PaieRH\RubriqueModel;
use App\Services\CalculPaiePersonnelService;
use App\Services\FormuleService;
use Livewire\Component;

class DetailEmployer extends Component
{
    public $employer, $rubriques,$rubrique_model, $model_rubrique_gain,$model_rubrique_retenu,$somme_gain, $somme_retenu;
    public $taux, $nombre, $base, $montant ;
    public $type_rubrique,$sens_rubrique;
    public $rubrique_id,$employer_id,$etatsPaies,$countEtatsPaies;
    public $date_embauche,$date_depart,$matricule,$nom, $prenom, $telephone,$adresse ,$date_naissance,$lieu_naissance,$sexe,$poste,$charge_familliale;
    /** Montant estimé de la rubrique automatique (CNSS/ITS/FSSP) sélectionnée dans le modal d'ajout. */
    public $apercu = null;

    /** Périodicité de la rubrique : 'permanente' (chaque mois) ou 'mois' (ce mois seulement). */
    public $periodicite = 'permanente';
    public $mois_application;
    public $mois_liste;
    /** Rubrique automatique (CNSS/ITS/FSSP) : 'auto' (calculée) ou 'manuel' (montant saisi). */
    public $mode_calcul = 'auto';
    public $base_manuelle, $taux_manuel;
    /** Part imposable d'un gain « Brute I / Brute NI » (vide = selon le type de la rubrique). */
    public $montant_imposable;
    public $ligne_retrait;
    /** Rubriques « ce mois seulement » et répartition du brut du bulletin modèle (rubriques permanentes). */
    public $model_rubrique_ponctuelles, $model_rubrique_patronal, $repartition = [], $bulletin_modele = [];
    /** Modification d'une rubrique du bulletin modèle. */
    public $ligne_formule = false, $ligne_formule_texte, $ligne_id, $ligne_libelle, $ligne_auto = false, $ligne_modifiable = false, $ligne_gain = false, $ligne_soumis = false;


    public function mount($id)
    {
        $id=decrypt($id);
        $this->employer=EmployerModel::find($id);

        // Les rubriques automatiques (CNSS, ITS, FSSP) sont toujours affichées à jour.
        app(CalculPaiePersonnelService::class)->synchroniserModele($this->employer);

        // La CNSS et l'ANPE patronales sont générées avec la CNSS salariale : elles ne s'ajoutent pas à la main.
        $this->rubriques=RubriqueModel::where(fn ($q) => $q->whereNull('calcul_auto')->orWhereNotIn('calcul_auto', ['CNSS_PAT', 'ANPE_PAT']))->get();
        $this->mois_liste = MoisPaieModel::orderBy('id')->get();
        $this->mois_application = $this->mois_application ?: (int) now()->month;

        $toutes = ModelBulletinModel::with('rubrique')->where('employer_id',$id)->orderBy('id')->get();
        $this->rubrique_model = $toutes;

        // Le bulletin modèle montre le mois « standard » : rubriques permanentes. Celles « ce mois seulement »
        // sont listées à part : elles ne comptent que pour le mois choisi.
        $permanentes = $toutes->filter(fn ($l) => (bool) $l->permanente)->values();
        $this->model_rubrique_ponctuelles = $toutes->reject(fn ($l) => (bool) $l->permanente)->values();

        $this->model_rubrique_gain = $permanentes->filter(fn ($l) => (float) $l->montant_gain > 0)->values();
        $this->somme_gain = $permanentes->where('montant_gain','>',0)->sum('montant_gain');
        // Les rubriques automatiques restent visibles même à 0 (ex. ITS non imposable).
        $this->model_rubrique_retenu = $permanentes
            ->filter(fn ($l) => (float) $l->montant_retenu > 0 || !empty($l->rubrique?->calcul_auto))->values();
        $this->somme_retenu = $permanentes->where('montant_retenu','>',0)->sum('montant_retenu');
        // Rubriques « Patronal (hors net) » du bulletin modèle (hors CNSS / ANPE patronales, générées au calcul de la paie)
        $this->model_rubrique_patronal = $permanentes->filter(fn ($l) => (float) $l->montant_patronal > 0)->values();

        $calcul = app(CalculPaiePersonnelService::class)->calculer($this->employer, $toutes);
        $this->repartition = [
            'imposable'     => $calcul['brut_imposable'],
            'non_imposable' => $calcul['brut_non_imposable'],
        ];

        // Aperçu du bulletin modèle, au même format que le bulletin calculé et imprimé
        $this->bulletin_modele = app(\App\Services\BulletinPaieService::class)->modelePersonnel($this->employer, $toutes);

        // Évite l'erreur si aucune année scolaire n'est active.
        $anneeId = AnneeScolaire()?->id;

        $this->etatsPaies = FichePaieModel::where('employer_id',$id)->where('annee_id',$anneeId)->orderBy('mois_id','DESC')->get();
        $this->countEtatsPaies = FichePaieModel::where('employer_id',$id)->where('annee_id',$anneeId)->sum("salaire_net");
        

        
        $this->nom=$this->employer->nom;
        $this->prenom=$this->employer->prenom;
        $this->telephone=$this->employer->telephone;
        $this->adresse=$this->employer->adresse;
        $this->date_naissance=$this->employer->date_naissance;
        $this->lieu_naissance=$this->employer->lieu_naissance;
        $this->sexe=$this->employer->sexe;
        $this->poste=$this->employer->poste;
        $this->date_embauche=$this->employer->date_embauche;
        $this->date_depart=$this->employer->date_depart;
        $this->charge_familliale=$this->employer->charge_familliale;
    }

    // public function updatedTaux($TauxId){
    //    dd('taux');
    // }
    // public function updatedNombre($NombreId){
    //     dd('nombre');
    // }
    // public function updatedBase($BaseId){
    //     dd('base');
    // }

    /** Affiche le montant calculé quand on choisit CNSS / ITS / FSSP dans le modal d'ajout. */
    public function updatedRubriqueId($value)
    {
        // Nouvelle rubrique choisie : on repart d'un calcul automatique, sans reste de l'édition d'une ligne
        $this->reset(['base', 'taux', 'nombre', 'ligne_id', 'ligne_formule']);
        $this->mode_calcul = 'auto';
        $this->montant = null;

        $rubrique = $value ? RubriqueModel::find($value) : null;

        $this->apercu = $rubrique && $rubrique->estAuto()
            ? app(CalculPaiePersonnelService::class)->apercu($this->employer, $rubrique)
            : null;
    }

    /** Rubrique à formule en cours de saisie (ajout ou modification) : le montant suit base / taux / nombre. */
    public function updated($nom)
    {
        if (!in_array($nom, ['base', 'taux', 'nombre', 'mode_calcul', 'rubrique_id'], true)) {
            return;
        }

        $rubrique = $this->ligne_formule && $this->ligne_id
            ? ModelBulletinModel::with('rubrique')->find($this->ligne_id)?->rubrique
            : ($this->rubrique_id ? RubriqueModel::find($this->rubrique_id) : null);

        if ($rubrique && $rubrique->estFormule() && $this->mode_calcul !== 'manuel') {
            $this->montant = $this->montantFormule($rubrique);
        }
    }

    /** Montant de la formule d'après base / taux / nombre saisis ; null si la formule échoue (message affiché au clic sur Enregistrer). */
    protected function montantFormule(RubriqueModel $rubrique): ?float
    {
        try {
            return max(0.0, round(app(FormuleService::class)->evaluer($rubrique->formule, [
                'base'   => is_numeric($this->base) ? $this->base : 0,
                'taux'   => is_numeric($this->taux) ? $this->taux : 0,
                'nombre' => is_numeric($this->nombre) ? $this->nombre : 0,
                ...FormuleService::contexteEmploye($this->employer),
            ])));
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    /** Valeurs base / taux / nombre à enregistrer sur une ligne à formule. */
    protected function variablesLigne(): array
    {
        $nz = fn ($v) => is_numeric($v) ? $v : null;

        return ['base' => $nz($this->base), 'taux' => $nz($this->taux), 'nombre' => $nz($this->nombre)];
    }

    public function render()
    {
        return view('livewire.paie-r-h.detail-employer');
    }

    /** Rubriques automatiques dont le montant peut être saisi à la main. */
    protected const AUTO_MODIFIABLES = ['CNSS', 'ITS', 'FSSP'];

    protected function retour(): string
    {
        return '/detail/employer/'.encrypt($this->employer->id);
    }

    protected function estSoumise(RubriqueModel $rubrique): bool
    {
        return $rubrique->sens_rubrique === 'Gain' && in_array($rubrique->type_rubrique, ['Brute I', 'Brute NI'], true);
    }

    /** Règles communes : périodicité (tout le temps / ce mois seulement) et mois concerné. */
    protected function validerPeriodicite(): void
    {
        $this->validate([
            'periodicite'      => 'required|in:permanente,mois',
            'mois_application' => 'required_if:periodicite,mois|nullable|integer|exists:mois_paie_models,id',
        ]);
    }

    /** Une même rubrique ne peut pas être à la fois permanente et ponctuelle, ni répétée pour le même mois. */
    protected function conflit(int $rubriqueId, bool $ponctuelle, ?int $mois, ?int $annee, ?int $sauf = null): ?string
    {
        $autres = ModelBulletinModel::where('rubrique_id', $rubriqueId)
            ->where('employer_id', $this->employer->id)
            ->when($sauf, fn ($q) => $q->where('id', '!=', $sauf))
            ->get();

        if ($autres->isEmpty()) {
            return null;
        }

        if (!$ponctuelle) {
            return 'Rubrique existe déjà';
        }

        if ($autres->contains(fn ($l) => (bool) $l->permanente)) {
            return 'Rubrique existe déjà : elle s\'applique déjà tous les mois';
        }

        if ($autres->contains(fn ($l) => (int) $l->mois_id === $mois && (int) $l->annee_id === (int) $annee)) {
            return 'Rubrique existe déjà pour ce mois';
        }

        return null;
    }

    public function addRubriqueModelBulletin()
    {
        $validated = $this->validate([
            'rubrique_id' => 'required',
        ]);

        $rubrique = RubriqueModel::find($validated['rubrique_id']);
        $retour   = $this->retour();
        $service  = app(CalculPaiePersonnelService::class);

        if (in_array($rubrique->calcul_auto, ['CNSS_PAT', 'ANPE_PAT'], true)) {
            return redirect($retour)->with('danger', 'La part patronale (CNSS, ANPE) est calculée automatiquement avec la CNSS.');
        }

        $this->validerPeriodicite();
        $ponctuelle = $this->periodicite === 'mois';
        $anneeId    = AnneeScolaire()?->id;

        if ($ponctuelle && empty($anneeId)) {
            return redirect($retour)->with('danger', "Pas d'annee scolaire actif");
        }

        $mois = $ponctuelle ? (int) $this->mois_application : null;

        //VERIFICATION RUBRIQUE
        if ($message = $this->conflit($rubrique->id, $ponctuelle, $mois, $anneeId)) {
            return redirect($retour)->with('danger', $message);
        }

        $options = [
            'permanente' => !$ponctuelle,
            'mois_id'    => $mois,
            'annee_id'   => $ponctuelle ? $anneeId : null,
        ];

        // CNSS / ITS / FSSP : calculées d'après les informations de l'employé, ou saisies à la main
        if($rubrique->estAuto()){
            $manuel = $this->mode_calcul === 'manuel' && in_array($rubrique->calcul_auto, self::AUTO_MODIFIABLES, true);

            if ($manuel) {
                $this->validate([
                    'montant'        => 'required|numeric|min:0',
                    'base_manuelle'  => 'nullable|numeric|min:0',
                    'taux_manuel'    => 'nullable|numeric|min:0|max:100',
                ]);

                ModelBulletinModel::create([
                    'rubrique_id'    => $rubrique->id,
                    'employer_id'    => $this->employer->id,
                    'mode_calcul'    => 'manuel',
                    'montant_retenu' => $this->montant,
                    'base'           => $this->base_manuelle !== '' ? $this->base_manuelle : null,
                    'taux'           => $this->taux_manuel !== '' ? $this->taux_manuel : null,
                ] + $options);
                $service->synchroniserModele($this->employer);

                return redirect($retour)->with('success','Rubrique ajoutée : montant saisi manuellement.');
            }

            ModelBulletinModel::create([
                'rubrique_id'    => $rubrique->id,
                'employer_id'    => $this->employer->id,
                'mode_calcul'    => 'auto',
                'montant_retenu' => 0,
            ] + $options);
            $service->synchroniserModele($this->employer);

            return redirect($retour)->with('success','Rubrique ajoutée : montant calculé automatiquement.');
        }

        // Rubrique à formule : montant calculé d'après base / taux / nombre (ou saisi à la main en mode manuel)
        $formule  = $rubrique->estFormule();
        $calcule  = $formule && $this->mode_calcul !== 'manuel';
        if ($calcule) {
            $this->validate([
                'base'   => 'nullable|numeric|min:0',
                'taux'   => 'nullable|numeric|min:0|max:100',
                'nombre' => 'nullable|numeric|min:0',
            ]);
            $this->montant = $this->montantFormule($rubrique);
            if ($this->montant === null) {
                return redirect($retour)->with('danger', 'Formule de la rubrique impossible à calculer : vérifiez la formule et les constantes.');
            }
        }

        $regles = ['montant' => 'required|numeric|min:0'];
        if ($this->estSoumise($rubrique)) {
            $regles['montant_imposable'] = 'nullable|numeric|min:0|lte:montant';
        }
        $validated = $this->validate($regles);

        $donnees = [
            'rubrique_id' => $rubrique->id,
            'employer_id' => $this->employer->id,
            'mode_calcul' => $formule ? ($calcule ? 'auto' : 'manuel') : 'auto',
        ] + ($formule ? $this->variablesLigne() : []) + $options;

        if ($rubrique->sens_rubrique === 'Gain') {
            $donnees['montant_gain'] = $validated['montant'];
            $donnees['montant_imposable'] = $this->estSoumise($rubrique) && $this->montant_imposable !== null && $this->montant_imposable !== ''
                ? $this->montant_imposable : null;
        } elseif ($rubrique->sens_rubrique === 'Patronal') {
            $donnees['montant_patronal'] = $validated['montant']; // charge employeur : hors brut, retenues et net
        } else {
            $donnees['montant_retenu'] = $validated['montant'];
        }
        ModelBulletinModel::create($donnees);

        // Un nouveau gain/retenue modifie la base CNSS, l'ITS et le FSSP déjà présents
        $service->synchroniserModele($this->employer);

        return redirect($retour)->with('success','Opération effectuée avec succès');
    }

    /** Ouvre la modification d'une rubrique du bulletin modèle (mode de calcul, montant, périodicité, part imposable). */
    public function editLigne($id)
    {
        $ligne = ModelBulletinModel::with('rubrique')
            ->where('id', $id)->where('employer_id', $this->employer->id)->firstOrFail();

        $rubrique = $ligne->rubrique;

        $this->resetErrorBag();
        $this->ligne_id         = $ligne->id;
        $this->ligne_libelle    = $rubrique->rubrique;
        $this->ligne_auto       = $rubrique->estAuto();
        $this->ligne_modifiable = $rubrique->estAuto() && in_array($rubrique->calcul_auto, self::AUTO_MODIFIABLES, true);
        $this->ligne_gain       = $rubrique->sens_rubrique === 'Gain' && !$rubrique->estAuto();
        $this->ligne_soumis     = !$rubrique->estAuto() && $this->estSoumise($rubrique);
        $this->ligne_formule    = $rubrique->estFormule();
        $this->ligne_formule_texte = $rubrique->formule;
        $this->rubrique_id      = null; // l'édition ne doit pas reprendre la rubrique choisie dans le modal d'ajout
        $this->base             = $ligne->base;
        $this->taux             = $ligne->taux;
        $this->nombre           = $ligne->nombre;
        $this->mode_calcul      = $ligne->mode_calcul ?: 'auto';
        $this->periodicite      = $ligne->permanente ? 'permanente' : 'mois';
        $this->mois_application = $ligne->mois_id ?: (int) now()->month;
        $colonne = $rubrique->estAuto() ? 'montant_retenu' : match ($rubrique->sens_rubrique) {
            'Gain'     => 'montant_gain',
            'Patronal' => 'montant_patronal',
            default    => 'montant_retenu',
        };
        $this->montant          = $ligne->{$colonne} !== null ? (float) $ligne->{$colonne} : null;
        $this->montant_imposable = $ligne->montant_imposable !== null ? (float) $ligne->montant_imposable : null;
        // Base et taux ne sont repris que s'ils ont été saisis à la main : ceux d'un calcul automatique
        // deviendraient faux dès que le brut change.
        $manuel = ($ligne->mode_calcul ?? 'auto') === 'manuel';
        $this->base_manuelle     = $manuel ? $ligne->base : null;
        $this->taux_manuel       = $manuel ? $ligne->taux : null;
    }

    public function updateLigne()
    {
        $ligne = ModelBulletinModel::with('rubrique')
            ->where('id', $this->ligne_id)->where('employer_id', $this->employer->id)->firstOrFail();
        $rubrique = $ligne->rubrique;
        $retour   = $this->retour();

        $this->validerPeriodicite();
        $ponctuelle = $this->periodicite === 'mois';
        $anneeId    = AnneeScolaire()?->id;

        if ($ponctuelle && empty($anneeId)) {
            return redirect($retour)->with('danger', "Pas d'annee scolaire actif");
        }

        $mois = $ponctuelle ? (int) $this->mois_application : null;

        if ($message = $this->conflit($rubrique->id, $ponctuelle, $mois, $anneeId, $ligne->id)) {
            return redirect($retour)->with('danger', $message);
        }

        $donnees = [
            'permanente' => !$ponctuelle,
            'mois_id'    => $mois,
            'annee_id'   => $ponctuelle ? $anneeId : null,
        ];

        if ($rubrique->estAuto()) {
            $manuel = $this->mode_calcul === 'manuel' && in_array($rubrique->calcul_auto, self::AUTO_MODIFIABLES, true);

            if ($manuel) {
                $this->validate([
                    'montant'       => 'required|numeric|min:0',
                    'base_manuelle' => 'nullable|numeric|min:0',
                    'taux_manuel'   => 'nullable|numeric|min:0|max:100',
                ]);
                $donnees += [
                    'mode_calcul'    => 'manuel',
                    'montant_retenu' => $this->montant,
                    'base'           => $this->base_manuelle !== '' ? $this->base_manuelle : null,
                    'taux'           => $this->taux_manuel !== '' ? $this->taux_manuel : null,
                ];
            } else {
                // Retour au calcul automatique : le montant est recalculé juste après
                $donnees += ['mode_calcul' => 'auto'];
            }
        } else {
            $formule = $rubrique->estFormule();
            $calcule = $formule && $this->mode_calcul !== 'manuel';
            if ($calcule) {
                $this->validate([
                    'base'   => 'nullable|numeric|min:0',
                    'taux'   => 'nullable|numeric|min:0|max:100',
                    'nombre' => 'nullable|numeric|min:0',
                ]);
                $this->montant = $this->montantFormule($rubrique);
                if ($this->montant === null) {
                    return redirect($retour)->with('danger', 'Formule de la rubrique impossible à calculer : vérifiez la formule et les constantes.');
                }
            }

            $regles = ['montant' => 'required|numeric|min:0'];
            if ($this->estSoumise($rubrique)) {
                $regles['montant_imposable'] = 'nullable|numeric|min:0|lte:montant';
            }
            $this->validate($regles);

            if ($formule) {
                $donnees += ['mode_calcul' => $calcule ? 'auto' : 'manuel'] + $this->variablesLigne();
            }

            if ($rubrique->sens_rubrique === 'Gain') {
                $donnees['montant_gain'] = $this->montant;
                $donnees['montant_imposable'] = $this->estSoumise($rubrique) && $this->montant_imposable !== null && $this->montant_imposable !== ''
                    ? $this->montant_imposable : null;
            } elseif ($rubrique->sens_rubrique === 'Patronal') {
                $donnees['montant_patronal'] = $this->montant;
            } else {
                $donnees['montant_retenu'] = $this->montant;
            }
        }

        $ligne->update($donnees);
        app(CalculPaiePersonnelService::class)->synchroniserModele($this->employer->fresh());

        return redirect($retour)->with('info', 'Modification effectuée avec succès');
    }

     public function retirerRubriqueModelBulletin()
     {
        $validated = $this->validate(
            [
            'ligne_retrait' => 'required',
        ]);

        $retirer_rubrique=ModelBulletinModel::where('id',$validated['ligne_retrait'])->where('employer_id',$this->employer->id)->first();
        if($retirer_rubrique){
            $retirer_rubrique->delete();
            app(CalculPaiePersonnelService::class)->synchroniserModele($this->employer);
        }

        return redirect($this->retour())->with('danger','Rubrique retirée effectuée avec succès');
     }

public function editerEmployer()
    {
         $employer=EmployerModel::find($this->employer->id);
        $validated = $this->validate(
            [
                'nom'=>'required',
                'prenom'=>'required',
                'sexe'=>'required',
                'date_naissance'=>'required',
                'lieu_naissance'=>'required',
                'telephone'=>'required',
                'poste'=>'required',
                'date_embauche'=>'required|date',
                // vide tant que l'employé est en poste
                'date_depart'=>'nullable|date|after_or_equal:date_embauche',
                'adresse'=>'required',
                'charge_familliale'=>'nullable|integer|min:0',
            ]
        );
        
            $employer->update([
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'sexe'=>$validated['sexe'],
            'date_naissance'=>$validated['date_naissance'],
            'lieu_naissance'=>$validated['lieu_naissance'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'poste'=>$validated['poste'],
            'date_embauche'=>$validated['date_embauche'],
            'date_depart'=>$validated['date_depart'] ?: null,
            'charge_familliale'=>(int) ($validated['charge_familliale'] ?? 0),
        ]);

        // Les personnes à charge changent l'abattement, donc l'ITS et le FSSP
        app(CalculPaiePersonnelService::class)->synchroniserModele($employer->fresh());

          return redirect('detail/employer/'.encrypt($employer->id))->with('info','Modification effectuée avec succès');
       
    }

}