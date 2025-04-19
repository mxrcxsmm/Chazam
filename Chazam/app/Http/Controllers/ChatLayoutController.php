<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ChatLayoutController extends Controller
{
    public function show()
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        return view('layout.chat', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
        ]);
    }
}