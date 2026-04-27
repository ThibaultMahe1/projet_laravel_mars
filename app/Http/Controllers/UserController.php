<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $tempPassword = Str::random(10); // Génère un mot de passe temporaire aléatoire
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'needs_password_change' => true,
        ]);

        Bouncer::assign('colonist')->to($user); // Par défaut, rôle "colon"

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès !')
            ->with('temp_password_user', $user->name)
            ->with('temp_password_value', $tempPassword);
    }

    public function resetPassword(Request $request, User $user)
    {
        $tempPassword = Str::random(10); 
        
        $user->update([
            'password' => Hash::make($tempPassword),
            'needs_password_change' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Mot de passe réinitialisé !')
            ->with('temp_password_user', $user->name)
            ->with('temp_password_value', $tempPassword);
    }
}
