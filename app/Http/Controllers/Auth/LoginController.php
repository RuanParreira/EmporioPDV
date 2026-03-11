<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(
            //Rules
            [
                'email' => 'required|email',
                'password' => 'required|min:6|max:16'
            ],
            [
                'email.required' => 'Email Obrigatório!',
                'email.email' => 'Email invalido',

                'password.required' => 'Senha Obrigatoria!',
                'password.min' => 'Deve haver no minimo :min caracters',
                'password.max' => 'Deve haver no maximo :max caracters',
            ]
        );

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard')->with('success', 'Logado com sucesso');
        }

        return back()->withErrors([
            'credentials' => 'Credenciais invalidas',
        ])->onlyInput('email');
    }
}
