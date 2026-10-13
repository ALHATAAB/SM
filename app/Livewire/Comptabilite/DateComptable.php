<?php

namespace App\Livewire\Comptabilite;

use App\Models\Comptabilite\DateComptableModel;
use App\Models\Config\ConfigDateComptableModel;
use Livewire\WithPagination;
use Livewire\Component;

class DateComptable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    public $date_comptable;
    public $date_comptable_id;
    public $mode = 'MANUELLE';

    public function mount()
    {
        $this->mode = ModeDateComptable();
    }

    public function changerMode($nouveauMode)
    {
        if (!in_array($nouveauMode, ['MANUELLE', 'AUTOMATIQUE'], true)) {
            return redirect('/date/comptable')->with('danger', __('message.Mode_invalide'));
        }

        ConfigDateComptableModel::updateOrCreate(['id' => 1], ['mode' => $nouveauMode]);

        // En automatique : on crée/ouvre tout de suite la journée du jour
        JourneeOuverte(true);

        return redirect('/date/comptable')->with('success', __('message.Mode_date_modifie'));
    }

    public function render()
    {
        $anneeScolaire = AnneeScolaire();

        // Mode automatique : s'assure que la journée du jour existe et est ouverte avant d'afficher la liste
        if (ModeDateComptable() === 'AUTOMATIQUE') {
            JourneeOuverte();
        }

        // Sécurité si aucune année scolaire active n'est trouvée pour éviter l'erreur sur "id"
        if (!$anneeScolaire) {
            session()->flash('danger', __('message.Aucune_annee_active_config'));
            return view('livewire.comptabilite.date-comptable', [
                'date_comptables' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5)
            ]);
        }

        $anneeId = $anneeScolaire->id;

        // Condition de recherche
        if ($this->recherche) {
            $date_comptables = DateComptableModel::where("date_comptable", 'like', '%' . $this->recherche . '%')
                ->where('annee_scolaire_id', $anneeId)
                ->orderBy('date_comptable', 'DESC')
                ->paginate(5);
        } else {
            $date_comptables = DateComptableModel::where('annee_scolaire_id', $anneeId)
                ->orderBy('date_comptable', 'DESC')
                ->paginate(5);
        }

        return view('livewire.comptabilite.date-comptable', [
            'date_comptables' => $date_comptables
        ]);
    }

    public function addDateComptable()
    {
        if (ModeDateComptable() === 'AUTOMATIQUE') {
            return redirect('/date/comptable')->with('danger', __('message.Date_auto_ajout_interdit'));
        }

        $anneeScolaire = AnneeScolaire();

        if (!$anneeScolaire) {
            return redirect('/date/comptable')->with('danger', __('message.Impossible_ajouter_aucune_annee'));
        }

        $validated = $this->validate([
            'date_comptable' => 'required',
        ]);

        $journee = DateComptableModel::where('date_comptable', $validated['date_comptable'])->first();
        
        if (!empty($journee)) {
            return redirect('/date/comptable')->with('danger', __('message.Date_existe_deja')); 
        } else {
            // AJOUT JOURNÉE COMPTABLE
            DateComptableModel::create([
                'date_comptable' => $validated['date_comptable'],
                'annee_scolaire_id' => $anneeScolaire->id,
                'statuts' => "OUVERTE",
            ]);

            DateComptableModel::where('date_comptable', '!=', $validated['date_comptable'])
                ->update(['statuts' => 'FERMEE']);

            return redirect('/date/comptable')->with('success', __('message.Date_operation_reussie'));
        }
    }

    public function ouvertureDate($id)
    {
        if (ModeDateComptable() === 'AUTOMATIQUE') {
            return redirect('/date/comptable')->with('danger', __('message.Date_auto_ouverture_interdit'));
        }

        $anneeScolaire = AnneeScolaire();
        $journee = DateComptableModel::find($id);

        if (!$journee) {
            return redirect('/date/comptable')->with('danger', __('message.Date_introuvable'));
        }

        if (!$anneeScolaire || $journee->annee_scolaire_id != $anneeScolaire->id) {
            return redirect('/date/comptable')->with('danger', __('message.Date_hors_annee_active'));
        }

        // Une seule journée ouverte à la fois : on ferme toutes les autres
        DateComptableModel::where('id', '!=', $journee->id)->update(['statuts' => 'FERMEE']);
        $journee->update(['statuts' => 'OUVERTE']);

        JourneeOuverte(true);

        return redirect('/date/comptable')->with('success', __('message.Date_ouverte_succes'));
    }

    public function clotureDate($id)
    {
        if (ModeDateComptable() === 'AUTOMATIQUE') {
            return redirect('/date/comptable')->with('danger', __('message.Date_auto_fermeture_interdit'));
        }

        $journee = DateComptableModel::find($id);

        if (!$journee) {
            return redirect('/date/comptable')->with('danger', __('message.Date_introuvable'));
        }

        $journee->update(['statuts' => 'FERMEE']);

        JourneeOuverte(true);

        return redirect('/date/comptable')->with('success', __('message.Date_fermee_succes'));
    }
}
