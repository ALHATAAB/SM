<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email='';
    public $password = '';


     public function mount(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    public function auth(Request $request){

       $validated =$this->validate ([

            'email' => 'required|email',            
            'password' => 'required|min:6',
    
        ]);
        
        if(Auth::Attempt( $validated))
        {
            $etaUser = User::getEtat(Auth::user()->etat);
            // dd($etaUser);
            if(!empty($etaUser)) {
                $request->session()->regenerate();
                return redirect('/home');
            }else{
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/')->with('danger','Compte inactif');;
            }
        }
        return redirect('/')->with('warning','Email ou mots de passe incorrect');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
