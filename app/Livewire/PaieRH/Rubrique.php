<?php

namespace App\Livewire\PaieRH;

use App\Models\PaieRH\RubriqueModel;
use App\Services\FormuleService;
use Livewire\Component;
use Livewire\WithPagination;

class Rubrique extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;

    public $rubrique_id;
    public $code_rubrique ;
    public $rubrique;
    public $sens_rubrique;
    public $type_rubrique;
    public $categorie_rubrique;
    /** 'manuel' (montant saisi) ou 'automatique' (calculé par la formule). */
    public $mode_rubrique = 'manuel';
    /** Gains « Brute I / Brute NI » : '' = selon le type, '1' = soumis, '0' = exclu. */
    public $soumis_cnss = '';
    public $soumis_its = '';
    public $formule;

    

    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $rubriques = RubriqueModel::where("rubrique",'like','%'.$this->recherche.'%')->orderBy('code_rubrique','ASC')
            ->paginate(10);
            return view('livewire.paie-r-h.rubrique',['rubriques'=>$rubriques, 'catalogue'=>$this->catalogue()]);
        }else{
            $rubriques = RubriqueModel::orderBy('code_rubrique','ASC')->paginate(10);
            return view('livewire.paie-r-h.rubrique',['rubriques'=>$rubriques, 'catalogue'=>$this->catalogue()]);
        }
    }

    public function addRubrique()
    {
        $validated = $this->validate(
            [
                'code_rubrique' =>'required|string|unique:rubrique_models',
                'rubrique' => 'required',
                'type_rubrique'=>'required|in:Brute I,Brute NI,Cotisation,Non soumise',
                'sens_rubrique'=>'required|in:Gain,Retenu,Patronal',
                'categorie_rubrique'=>'required|in:Commun,Personnel,Enseignant',
                'mode_rubrique'=>'required|in:manuel,automatique',
                'soumis_cnss'=>'nullable|in:0,1',
                'soumis_its'=>'nullable|in:0,1',
            ]
        );
        $formule = $this->formuleValidee();

        RubriqueModel::create([
            'rubrique'=>$validated['rubrique'],
            'code_rubrique'=>$validated['code_rubrique'],
            'sens_rubrique'=>$validated['sens_rubrique'],
            'type_rubrique'=>$validated['type_rubrique'],
            'categorie_rubrique'=>$validated['categorie_rubrique'],
            'mode_rubrique'=>$validated['mode_rubrique'],
            'formule'=>$formule,
            'soumis_cnss'=>$this->drapeau($this->soumis_cnss),
            'soumis_its'=>$this->drapeau($this->soumis_its),
        ]);

        $this->resetInput();

        return redirect('/rubrique')->with('success','Opération effectuée avec succès');
    }

    public function editRubrique($id){

        $rubrique= RubriqueModel::findOrFail($id);
        $this->rubrique_id= $id;
        $this->rubrique= $rubrique->rubrique;
        $this->code_rubrique= $rubrique->code_rubrique;
        $this->type_rubrique= $rubrique->type_rubrique;
        $this->sens_rubrique= $rubrique->sens_rubrique;
        $this->categorie_rubrique= $rubrique->categorie_rubrique;
        $this->mode_rubrique= $rubrique->mode_rubrique ?: 'manuel';
        $this->formule= $rubrique->formule;
        $this->soumis_cnss= $rubrique->soumis_cnss === null ? '' : (string) (int) $rubrique->soumis_cnss;
        $this->soumis_its= $rubrique->soumis_its === null ? '' : (string) (int) $rubrique->soumis_its;
    }

    public function updateRubrique()
    {

        $rubrique = RubriqueModel::findOrFail($this->rubrique_id);
        $validated = $this->validate([
            'rubrique' => 'required',
            'type_rubrique'=>'required|in:Brute I,Brute NI,Cotisation,Non soumise',
            'sens_rubrique'=>'required|in:Gain,Retenu,Patronal',
            'categorie_rubrique'=>'required|in:Commun,Personnel,Enseignant',
            'mode_rubrique'=>'required|in:manuel,automatique',
                'soumis_cnss'=>'nullable|in:0,1',
                'soumis_its'=>'nullable|in:0,1',
        ]);

        // CNSS / ITS / FSSP : le calcul repose sur leur type, sens et catégorie, seul le libellé est modifiable
        if($rubrique->estAuto()){
            $rubrique->update(['rubrique'=>$validated['rubrique']]);

            return redirect('/rubrique')->with('info','Rubrique automatique : seul le libellé a été modifié.');
        }

        $formule = $this->formuleValidee();

        $rubrique->update([
            'rubrique'=>$validated['rubrique'],
            'mode_rubrique'=>$validated['mode_rubrique'],
            'formule'=>$formule,
            'soumis_cnss'=>$this->drapeau($this->soumis_cnss),
            'soumis_its'=>$this->drapeau($this->soumis_its),
            'type_rubrique'=>$validated['type_rubrique'],
            'sens_rubrique'=>$validated['sens_rubrique'],
            'categorie_rubrique'=>$validated['categorie_rubrique'],
        ]);

        return redirect('/rubrique')->with('info','Modification effectuée avec succès');
    }

    public function resetInput()
    {
        $this->reset([
            'rubrique_id', 'code_rubrique', 'rubrique',
            'sens_rubrique', 'type_rubrique', 'categorie_rubrique', 'formule',
        ]);
        $this->mode_rubrique = 'manuel';
        $this->soumis_cnss = '';
        $this->soumis_its = '';
        $this->resetErrorBag();
    }

    /**
     * Rubrique « automatique » : la formule est obligatoire et doit être valide
     * (variables base, taux, nombre et constantes existantes). Rubrique « manuel » : pas de formule.
     */
    protected function formuleValidee(): ?string
    {
        if ($this->mode_rubrique !== 'automatique') {
            return null;
        }

        $this->validate(['formule' => 'required|string|max:255']);

        if ($erreur = app(FormuleService::class)->valider($this->formule, null, true)) {
            $this->addError('formule', $erreur);
            throw \Illuminate\Validation\ValidationException::withMessages(['formule' => $erreur]);
        }

        return trim($this->formule);
    }

    /** Variables, constantes et paramètres de paie proposés pour écrire la formule (uniquement en mode automatique). */
    protected function catalogue(): array
    {
        return $this->mode_rubrique === 'automatique' ? app(FormuleService::class)->catalogue() : [];
    }

    /** Ajoute un élément du catalogue (variable, constante ou paramètre) à la fin de la formule. */
    public function insererDansFormule(string $code)
    {
        $f = rtrim((string) $this->formule);
        $operateurs = ['+', '-', '*', '/', '(', ';', '>', '<', '='];
        // entre deux éléments sans opérateur, on met « * » par défaut
        $this->formule = $f === '' ? $code : (in_array(substr($f, -1), $operateurs, true) ? $f.$code : $f.'*'.$code);
    }

    /** '' => null (selon le type de rubrique) ; '0' / '1' => faux / vrai. */
    protected function drapeau($valeur): ?bool
    {
        return $valeur === '' || $valeur === null ? null : (bool) (int) $valeur;
    }
}
