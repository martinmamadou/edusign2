<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        Log::info('Tentative de connexion avec email: ' . $request->email);

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        Log::info('Utilisateur trouvé:', ['user' => $user]);

        if (!$user) {
            Log::warning('Utilisateur non trouvé pour l\'email: ' . $request->email);
            return response()->json([
                'message' => 'Cet email n\'existe pas dans nos enregistrements.'
            ], 404);
        }

        Auth::login($user);

        return response()->json([
            'user' => $user,
            'message' => 'Connexion réussie',
            'redirect' => route('home')
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'Inscription réussie'
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
} 