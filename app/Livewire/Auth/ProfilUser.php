<?php

namespace App\Livewire\Auth;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Livewire\Component;

class ProfilUser extends Component
{
    public $user;
    public $password;
    public $new_password;
    public $confirm_password;

    public function mount($id)
    {
        $id=decrypt($id);
        $this->user=User::find($id);
    }
    public function render()
    {
        return view('livewire.auth.profil-user');
    }

    public function changePassword(Request $request)
    {
        $validated= $this->validate([
        'password' => ['required', 'current_password'],
        'new_password' => [
            'required',
            'min:6',
            'different:password',
            'same:confirm_password',
        ],
        'confirm_password' => 'required',
    ]);
    
    $users=$this->user;
    $users->update([
        'password' => Hash::make($validated['new_password']),
    ]);

    Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
