<?php

namespace App\Livewire\Scolarite;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Request;
use App\Models\Scolarite\AnneeScolaireModel;
use App\Services\LicenceService;

class AnneeScolaire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $recherche;
    public $confirmDesactiver,$confirmActiver, $confirmInitialisationMdp;

    public $annee_id;
    public $annee;
    public $date_debut;
    public $date_fin;


    public function render()
    {
         //condition de recherche utilisateur
         if($this->recherche){
            $annee_scolaires = AnneeScolaireModel::where("annee",'like','%'.$this->recherche.'%')->orderBy('annee','DESC')
            ->paginate(5);
            return view('livewire.scolarite.annee-scolaire',['annee_scolaires'=>$annee_scolaires]);
        }else{
            $annee_scolaires = AnneeScolaireModel::orderBy('annee','DESC')->paginate(5);
            return view('livewire.scolarite.annee-scolaire',['annee_scolaires'=>$annee_scolaires]);
        }
    }

    public function addAnnee(Request  $request)
    {
        $validated = $this->validate(
            [
                'annee' => 'required|unique:annee_scolaire_models|digits:4|integer|min:' . date('Y').'|max:2050',
                'date_debut'=>'required',
                'date_fin'=>'required',
            ]
        );

        AnneeScolaireModel::create([
            'annee'=>$validated['annee'],
            'date_debut'=>$validated['date_debut'],
            'date_fin'=>$validated['date_fin'],
        ]);

        return redirect('/annee/scolaire')->with('success','Opération effectuée avec succès');
    }

    //avtiver et desactiver un compte
    public function activer($id){
        $this->confirmActiver= $id;
    }
    public function confirmActive(){
        $activier = AnneeScolaireModel::find($this->confirmActiver);
        $desactiver = AnneeScolaireModel::Where('id','!=',$this->confirmActiver);

        $activier->update([
            'etat' => 'actif',
        ]);

        $desactiver->update([
            'etat' => 'inactif',
        ]);

        // Une licence importée avant la création de l'année est rattachée à cette année
        app(LicenceService::class)->rattacherLicenceSansAnnee((int) $activier->id);

        return redirect('/annee/scolaire')->with('success','Annee activer');

        $this->dispatchBrowserEvent('close-modal');
    }
    public function desactiver($id){
        $this->confirmDesactiver = $id;
    }
    public function confirmDesactive(){
        $activier = AnneeScolaireModel::find($this->confirmDesactiver);
        $activier->update([
            'etat' => 'inactif',
        ]);
        return redirect('/annee/scolaire')->with('danger','Annee desactiver');
    }

      //editer un utilisateur
      public function editAnnee($id){

        $annee_scolaire = AnneeScolaireModel::find($id);
        $this->annee_id= $id;
        $this->annee= $annee_scolaire->annee;
        $this->date_debut= $annee_scolaire->date_debut;
        $this->date_fin= $annee_scolaire->date_fin;
    }
    public function updateAnnee()
    {
        $annee_scolaire = AnneeScolaireModel::find( $this->annee_id);
        $validated = $this->validate([
                'annee'=>'required',
                'date_debut'=>'required',
                'date_fin'=>'required',
            ]);

        $annee_scolaire->update([
            'annee'=>$validated['annee'],
            'date_debut'=>$validated['date_debut'],
            'date_fin'=>$validated['date_fin'],
        ]);
        return redirect('/annee/scolaire')->with('info','Modification effectuée avec succès');
    }
}
