<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Auth\User;
use Livewire\WithPagination;
use App\Models\Auth\RoleModel;
use App\Services\LicenceService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;


class Users extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $rechercheNom;
    public $recherchePrenom;
    public $rechercheTelephone;
    // public $users;
    public $roles = [];
    public $user_id,$nom, $prenom, $telephone,$adresse ,$email,$password,$role_id;
    public $Permission;

    public $confirmDesactiver,$confirmActiver, $confirmInitialisationMdp;

    public function mount()
    {

        //liste des roles
        $this->roles = RoleModel::all();

    }

    public function render()
    {
          //condition de recherche utilisateur
        if($this->rechercheNom || $this->recherchePrenom || $this->rechercheTelephone){
            $users = User::where("nom",'like','%'.$this->rechercheNom.'%')->where("prenom",'like','%'.$this->recherchePrenom.'%')
            ->where("telephone",'like','%'.$this->rechercheTelephone.'%')
            ->paginate(5);
            return view('livewire.auth.users',['users'=>$users]);
        }else{
            $users = User::paginate(5);
            return view('livewire.auth.users',['users'=>$users]);
        }

    }

    public function addUser(Request  $request)
    {
        $validated = $this->validate(
            [
                'nom'=>'required',
                'prenom'=>'required',
                'email'=>'required|email|unique:users',
                'telephone'=>'required',
                'adresse'=>'required',
                'password'=>'required|min:6',
                'role_id'=>'required',
            ]
        );

        $licenceService = app(LicenceService::class);
        if (!$licenceService->utilisateurDisponible()) {
            return redirect('/users')->with('danger',
                "Nombre maximum d'utilisateurs atteint pour votre licence (" .
                $licenceService->nombreUtilisateursActuel() . " / " . $licenceService->limiteUtilisateurs() .
                "). Contactez votre fournisseur pour augmenter votre quota."
            );
        }

        User::create([
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'email'=>$validated['email'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'password'=>Hash::make($validated['password']),
            'role_id'=>$validated['role_id'],
        ]);

        return redirect('/users')->with('success','Opération effectuée avec succès');
    }

    //avtiver et desactiver un compte
    public function activer($id){
        $this->confirmActiver= $id;
    }
    public function confirmActive(){
        $activier = User::find($this->confirmActiver);

        if ((int) $activier->etat !== 1) {
            $licenceService = app(LicenceService::class);
            if (!$licenceService->utilisateurDisponible()) {
                return redirect('/users')->with('danger',
                    "Impossible d'activer ce compte : nombre maximum d'utilisateurs atteint pour votre licence (" .
                    $licenceService->nombreUtilisateursActuel() . " / " . $licenceService->limiteUtilisateurs() .
                    "). Désactivez un autre compte ou contactez votre fournisseur."
                );
            }
        }

        $activier->update([
            'etat' => 1,
        ]);

        return redirect('/users')->with('success','Compte activer');
    }
    public function desactiver($id){
        $this->confirmDesactiver = $id;
    }
    public function confirmDesactive(){
        $activier = User::find($this->confirmDesactiver);
        $activier->update([
            'etat' => 0,
        ]);
        return redirect('/users')->with('danger','Compte desactiver');
    }

    //editer un utilisateur
    public function editUser($id){

        $user = User::find($id);
        $this->user_id= $id;
        $this->nom= $user->nom;
        $this->prenom= $user->prenom;
        $this->telephone= $user->telephone;
        $this->adresse= $user->adresse;
        $this->email= $user->email;
        $this->role_id= $user->role_id;
    }
    public function updateUser()
    {
        $user = User::find( $this->user_id);
        $validated = $this->validate(
            [
                'nom'=>'required',
                'prenom'=>'required',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'telephone'=>'required',
                'adresse'=>'required',
                'role_id'=>'required',
            ]
        );

        $user->update([
            'nom'=>$validated['nom'],
            'prenom'=>$validated['prenom'],
            'email'=>$validated['email'],
            'telephone'=>$validated['telephone'],
            'adresse'=>$validated['adresse'],
            'role_id'=>$validated['role_id'],
        ]);
        return redirect('/users')->with('info','Modification effectuée avec succès');
    }

    //Initialisation du mot de passe oublier
    public function initialisationMdp($id)
    {
        $this->confirmInitialisationMdp = $id;
    }
    public function confirmMdp()
    {
        $mdp = User::find( $this->confirmInitialisationMdp);
        $mdp->update([
            'password'=>Hash::make('000000'),
        ]);
        return redirect('/users')->with('warning','Mots de passe initialisé avec succès');

    }

        //show un utilisateur
        public function showUser($id){

            $user = User::find($id);
            $this->user_id= $id;
            $this->nom= $user->nom;
            $this->prenom= $user->prenom;
            $this->telephone= $user->telephone;
            $this->adresse= $user->adresse;
            $this->email= $user->email;
            $this->role_id= $user->role_id;
        }
//Fermer le modal
    public function fermerModal(){
        return redirect('/users');
    }
}