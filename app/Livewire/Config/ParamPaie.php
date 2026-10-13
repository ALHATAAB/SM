<?php

namespace App\Livewire\Config;

use App\Models\PaieRH\CotisationModel;
use App\Models\PaieRH\MontantTrancheFiscaleModel;
use App\Models\PaieRH\ParametrePaieModel;
use App\Models\PaieRH\TauxAncienneteModel;
use App\Models\PaieRH\TauxChargeFamillialeModel;
use App\Models\PaieRH\TauxTrancheFiscaleModel;
use Livewire\Component;

class ParamPaie extends Component
{

    public $config_id,$initiale,$type,$nbr_caractere;
    public $CountConfigApprenant,$CountConfigEnseignant,$CountConfigEmployer;
    public $ConfigMatriculeApprenants=[] ;
    public $ConfigMatriculeEnseignants=[] ;
    public $ConfigMatriculeEmployers=[] ;

    public $nombre_de_charge;
    public $abatement;
    public $min;
    public $max;
    public $taux_charge;
    public $tranche_min;
    public $tranche_max;
    public $valeur_fiscale;
    public $valeur_charge;
    public $taux_fiscale;
    public $taux_anciennetes=[];
    public $taux_charge_familliales=[];
    public $tranche_taux_fiscales=[];
    public $tranche_valeur_fiscales=[];
    public $taux_cotisations=[];
    public $plafond_base_cotisations=[];
    public $taux_cotisation;
    public $intitule_cotisation;

    // Cotisations sociales (CNSS)
    public $cotisations = [];
    public $cot_id, $cot_intitule, $cot_taux_salarie, $cot_taux_patronal, $cot_plafond, $cot_actif = true;
    public $cot_organisme = 'CNSS';

    // Taux FSSP (% du net avant FSSP)
    public $fssp_taux;

    // Calcul de l'ITS et des charges patronales (conforme à la feuille de calcul de l'établissement)
    public $its_frais_pro_taux, $cnss_patronal_base = 'cnss';

    // Modification des taux par charge familiale et des deux barèmes ITS (par taux / par montant)
    public $cf_id, $cf_nombre_de_charge, $cf_taux_charge;
    public $tf_id, $tf_min, $tf_max, $tf_taux_fiscale;
    public $mf_id, $mf_tranche_min, $mf_tranche_max, $mf_valeur_fiscale;
    
    public function mount()
    {
        

        $this->taux_anciennetes=TauxAncienneteModel::all();
        $this->taux_charge_familliales=TauxChargeFamillialeModel::all();
        $this->tranche_valeur_fiscales=MontantTrancheFiscaleModel::all();
        $this->tranche_taux_fiscales=TauxTrancheFiscaleModel::all();
        $this->cotisations=CotisationModel::orderBy('id')->get();
        $this->fssp_taux = ParametrePaieModel::nombre('fssp_taux', (float) config('paie.fssp_taux', 1));
        $this->its_frais_pro_taux = ParametrePaieModel::nombre('its_frais_pro_taux', 0.0);
        $this->cnss_patronal_base = ParametrePaieModel::texte('cnss_patronal_base', 'cnss');
    }

    public function updatedMax($value)
    {
        // Supprimer les séparateurs de milliers pour garder la valeur brute
        $this->max = str_replace(' ', '',$value);
    }
    public function updatedMin($value)
    {
        // Supprimer les séparateurs de milliers pour garder la valeur brute
        $this->min = str_replace(' ', '',$value);
    }
    public function updatedTrancheMax($value)
    {
        // Supprimer les séparateurs de milliers pour garder la valeur brute
        $this->tranche_max = str_replace(' ', '',$value);
    }
    public function updatedTrancheMin($value)
    {
        // Supprimer les séparateurs de milliers pour garder la valeur brute
        $this->tranche_min = str_replace(' ', '',$value);
    }

    public function updatedValeurFiscale($value)
    {
        // Supprimer les séparateurs de milliers pour garder la valeur brute
        $this->valeur_fiscale = str_replace(' ', '',$value);
    }


    public function render()
    {
        return view('livewire.config.param-paie');
    }


    public function addTauxChargeFamilliale()
    {
        $validated = $this->validate(
            [
                'nombre_de_charge'=>'required|unique:taux_charge_familliale_models',
                'taux_charge'=>'required|unique:taux_charge_familliale_models',
            ]
        );

        TauxChargeFamillialeModel::create([
            'nombre_de_charge'=>$validated['nombre_de_charge'],
            'taux_charge'=>$validated['taux_charge'],
        ]);

        return redirect('/parametre/paieRH')->with('success','Opération taux charge familliale effectuée avec succès');
    }

    public function addTauxFiscale()
    {
        $validated = $this->validate(
            [
                'min'=>'required|unique:taux_tranche_fiscale_models',
                'max'=>'required|unique:taux_tranche_fiscale_models',
                'taux_fiscale'=>'required|unique:taux_tranche_fiscale_models',
            ]
        );

        TauxTrancheFiscaleModel::create([
            'min'=>$this->min,
            'max'=>$this->max,
            'taux_fiscale'=>$validated['taux_fiscale'],
        ]);
        return redirect('/parametre/paieRH')->with('success','Opération tranche taux fiscale effectuée avec succès');
    }

    public function addMontantFiscale()
    {
        $validated = $this->validate(
            [
                'tranche_min'=>'required|unique:montant_tranche_fiscale_models',
                'tranche_max'=>'required|unique:montant_tranche_fiscale_models',
                'valeur_fiscale'=>'required|unique:montant_tranche_fiscale_models',
            ]
        );

        MontantTrancheFiscaleModel::create([
            'tranche_min'=>$this->tranche_min,
            'tranche_max'=>$this->tranche_max,
            'valeur_fiscale'=>$this->valeur_fiscale,
        ]);
        return redirect('/parametre/paieRH')->with('success','Opération tranche valeur fiscale effectuée avec succès');
    }


    public function updatedCotPlafond($value)
    {
        $this->cot_plafond = str_replace(' ', '', $value);
    }

    public function resetCotisation()
    {
        $this->reset(['cot_id', 'cot_intitule', 'cot_taux_salarie', 'cot_taux_patronal', 'cot_plafond']);
        $this->cot_actif = true;
        $this->cot_organisme = 'CNSS';
        $this->resetErrorBag();
    }

    public function editCotisation($id)
    {
        $c = CotisationModel::findOrFail($id);
        $this->cot_id            = $c->id;
        $this->cot_intitule      = $c->intitule;
        $this->cot_taux_salarie  = $c->taux_salarie;
        $this->cot_taux_patronal = $c->taux_patronal;
        $this->cot_plafond       = (int) $c->plafond_mensuel;
        $this->cot_actif         = (bool) $c->actif;
        $this->cot_organisme     = $c->organisme ?: 'CNSS';
        $this->resetErrorBag();
    }

    /** Ajoute ou modifie une branche de cotisation (selon que cot_id est renseigné). */
    public function saveCotisation()
    {
        $validated = $this->validate([
            'cot_intitule'      => 'required|string|max:255',
            'cot_taux_salarie'  => 'required|numeric|min:0|max:100',
            'cot_taux_patronal' => 'required|numeric|min:0|max:100',
            'cot_plafond'       => 'nullable|numeric|min:0',
            'cot_organisme'     => 'required|in:CNSS,ANPE',
        ]);

        $data = [
            'intitule'        => $validated['cot_intitule'],
            'organisme'       => $validated['cot_organisme'],
            'taux_salarie'    => $validated['cot_taux_salarie'],
            'taux_patronal'   => $validated['cot_taux_patronal'],
            'plafond_mensuel' => $validated['cot_plafond'] ?: 0,
            'actif'           => (bool) $this->cot_actif,
        ];

        if ($this->cot_id) {
            CotisationModel::findOrFail($this->cot_id)->update($data);
            $message = 'Cotisation modifiée avec succès';
        } else {
            CotisationModel::create($data);
            $message = 'Cotisation ajoutée avec succès';
        }

        return redirect('/parametre/paieRH')->with('success', $message);
    }

    /** Enregistre l'abattement ITS pour frais professionnels et la base des charges patronales. */
    public function saveCalculIts()
    {
        $this->its_frais_pro_taux = str_replace(',', '.', trim((string) $this->its_frais_pro_taux));
        $this->validate([
            'its_frais_pro_taux' => 'required|numeric|min:0|max:100',
            'cnss_patronal_base' => 'required|in:brut,cnss',
        ], [], ['its_frais_pro_taux' => 'abattement pour frais professionnels', 'cnss_patronal_base' => 'base patronale']);

        ParametrePaieModel::definir('its_frais_pro_taux', $this->its_frais_pro_taux, 'Abattement ITS pour frais professionnels (% de brut − CNSS)');
        ParametrePaieModel::definir('cnss_patronal_base', $this->cnss_patronal_base, 'Base des charges patronales : brut (total des gains) ou cnss (base salariale)');

        return redirect('/parametre/paieRH')->with('success', 'Paramètres de calcul enregistrés : ils s\'appliquent aux prochains calculs de paie');
    }

    /** Enregistre le taux FSSP : appliqué aux prochains calculs de paie (les fiches déjà calculées ne changent pas). */
    public function saveFsspTaux()
    {
        $this->fssp_taux = str_replace(',', '.', trim((string) $this->fssp_taux));
        $this->validate(['fssp_taux' => 'required|numeric|min:0|max:100'], [], ['fssp_taux' => 'taux FSSP']);

        ParametrePaieModel::definir('fssp_taux', $this->fssp_taux, 'Taux FSSP (% du salaire net avant FSSP)');

        return redirect('/parametre/paieRH')->with('success', 'Taux FSSP enregistré : il s\'applique aux prochains calculs de paie');
    }

    // ------------------------------------------------------------------ taux par charge familiale

    public function editChargeFamiliale($id)
    {
        $c = TauxChargeFamillialeModel::findOrFail($id);
        $this->resetErrorBag();
        $this->cf_id                = $c->id;
        $this->cf_nombre_de_charge  = (int) $c->nombre_de_charge;
        $this->cf_taux_charge       = (float) $c->taux_charge;
    }

    public function updateChargeFamiliale()
    {
        $c = TauxChargeFamillialeModel::findOrFail($this->cf_id);
        $this->cf_taux_charge = $this->nombre($this->cf_taux_charge);

        $v = $this->validate([
            'cf_nombre_de_charge' => ['required', 'integer', 'min:0', \Illuminate\Validation\Rule::unique('taux_charge_familliale_models', 'nombre_de_charge')->ignore($c->id)],
            'cf_taux_charge'      => 'required|numeric|min:0|max:100',
        ], ['cf_nombre_de_charge.unique' => 'Ce nombre de charge a déjà un taux.'], [
            'cf_nombre_de_charge' => 'nombre de charge', 'cf_taux_charge' => 'taux',
        ]);

        $c->update(['nombre_de_charge' => $v['cf_nombre_de_charge'], 'taux_charge' => $v['cf_taux_charge']]);

        return redirect('/parametre/paieRH')->with('success', 'Taux de charge familiale modifié : il s\'applique aux prochains calculs de paie');
    }

    public function deleteChargeFamiliale($id)
    {
        TauxChargeFamillialeModel::findOrFail($id)->delete();

        return redirect('/parametre/paieRH')->with('danger', 'Taux de charge familiale supprimé');
    }

    // ------------------------------------------------------------------ barème ITS par taux

    public function editTauxFiscale($id)
    {
        $t = TauxTrancheFiscaleModel::findOrFail($id);
        $this->resetErrorBag();
        $this->tf_id           = $t->id;
        $this->tf_min          = (float) $t->min;
        $this->tf_max          = (float) $t->max;
        $this->tf_taux_fiscale = (float) $t->taux_fiscale;
    }

    public function updateTauxFiscale()
    {
        $t = TauxTrancheFiscaleModel::findOrFail($this->tf_id);
        foreach (['tf_min', 'tf_max', 'tf_taux_fiscale'] as $champ) {
            $this->{$champ} = $this->nombre($this->{$champ});
        }

        $v = $this->validate([
            'tf_min'          => 'required|numeric|min:0',
            'tf_max'          => 'required|numeric|gt:tf_min',
            'tf_taux_fiscale' => 'required|numeric|min:0|max:100',
        ], ['tf_max.gt' => 'La borne maximale doit être supérieure à la borne minimale.'], [
            'tf_min' => 'borne minimale', 'tf_max' => 'borne maximale', 'tf_taux_fiscale' => 'taux',
        ]);

        $this->refuserChevauchement(TauxTrancheFiscaleModel::class, 'min', 'max', $t->id, $v['tf_min'], $v['tf_max'], 'tf_min');

        $t->update(['min' => $v['tf_min'], 'max' => $v['tf_max'], 'taux_fiscale' => $v['tf_taux_fiscale']]);

        return redirect('/parametre/paieRH')->with('success', 'Tranche du barème ITS (par taux) modifiée : elle s\'applique aux prochains calculs de paie');
    }

    public function deleteTauxFiscale($id)
    {
        TauxTrancheFiscaleModel::findOrFail($id)->delete();

        return redirect('/parametre/paieRH')->with('danger', 'Tranche du barème ITS (par taux) supprimée');
    }

    // ------------------------------------------------------------------ barème ITS par montant

    public function editMontantFiscale($id)
    {
        $t = MontantTrancheFiscaleModel::findOrFail($id);
        $this->resetErrorBag();
        $this->mf_id             = $t->id;
        $this->mf_tranche_min    = (float) $t->tranche_min;
        $this->mf_tranche_max    = (float) $t->tranche_max;
        $this->mf_valeur_fiscale = (float) $t->valeur_fiscale;
    }

    public function updateMontantFiscale()
    {
        $t = MontantTrancheFiscaleModel::findOrFail($this->mf_id);
        foreach (['mf_tranche_min', 'mf_tranche_max', 'mf_valeur_fiscale'] as $champ) {
            $this->{$champ} = $this->nombre($this->{$champ});
        }

        $v = $this->validate([
            'mf_tranche_min'    => 'required|numeric|min:0',
            'mf_tranche_max'    => 'required|numeric|gt:mf_tranche_min',
            'mf_valeur_fiscale' => 'required|numeric|min:0',
        ], ['mf_tranche_max.gt' => 'La borne maximale doit être supérieure à la borne minimale.'], [
            'mf_tranche_min' => 'borne minimale', 'mf_tranche_max' => 'borne maximale', 'mf_valeur_fiscale' => 'montant',
        ]);

        $this->refuserChevauchement(MontantTrancheFiscaleModel::class, 'tranche_min', 'tranche_max', $t->id, $v['mf_tranche_min'], $v['mf_tranche_max'], 'mf_tranche_min');

        $t->update(['tranche_min' => $v['mf_tranche_min'], 'tranche_max' => $v['mf_tranche_max'], 'valeur_fiscale' => $v['mf_valeur_fiscale']]);

        return redirect('/parametre/paieRH')->with('success', 'Tranche du barème ITS (par montant) modifiée : elle s\'applique aux prochains calculs de paie');
    }

    public function deleteMontantFiscale($id)
    {
        MontantTrancheFiscaleModel::findOrFail($id)->delete();

        return redirect('/parametre/paieRH')->with('danger', 'Tranche du barème ITS (par montant) supprimée');
    }

    // ------------------------------------------------------------------ outils

    /** Retire les espaces (séparateurs de milliers) et accepte la virgule décimale. */
    protected function nombre($valeur): string
    {
        return str_replace([' ', "\u{00A0}", ','], ['', '', '.'], trim((string) $valeur));
    }

    /** Une tranche ne doit pas empiéter sur une autre (bornes qui se touchent autorisées). */
    protected function refuserChevauchement(string $modele, string $colMin, string $colMax, int $id, $min, $max, string $champErreur): void
    {
        $autre = $modele::where('id', '!=', $id)->where($colMin, '<', $max)->where($colMax, '>', $min)->first();

        if ($autre) {
            $message = 'Cette tranche chevauche la tranche '.number_format((float) $autre->{$colMin}, 0, ',', ' ')
                .' – '.number_format((float) $autre->{$colMax}, 0, ',', ' ').'.';
            $this->addError($champErreur, $message);
            throw \Illuminate\Validation\ValidationException::withMessages([$champErreur => $message]);
        }
    }

    public function deleteCotisation($id)
    {
        CotisationModel::findOrFail($id)->delete();

        return redirect('/parametre/paieRH')->with('danger', 'Cotisation supprimée');
    }

}
