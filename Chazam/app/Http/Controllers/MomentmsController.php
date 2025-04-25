<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Historia;
use Illuminate\Support\Facades\Auth;

class MomentmsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $amigos = $user->amigos()->get();
        
        // Obtener los IDs de los amigos
        $amigoIds = $amigos->pluck('id_usuario')->toArray();
        
        $momentms = Historia::where(function($query) use ($user, $amigoIds) {
            $query->whereIn('id_usuario', $amigoIds)
                  ->orWhere('id_usuario', $user->id_usuario);
        })
        ->where('created_at', '>=', now()->subDay())
        ->with('usuario')
        ->get();

        return view('user.momentms', [
            'momentms' => $momentms,
            'user' => $user,
            'amigos' => $amigos
        ]);
    }

    public function show($id)
    {
        $momentm = Historia::with('usuario')->findOrFail($id);
        return view('user.momentm-view', [
            'momentm' => $momentm
        ]);
    }

    public function create()
    {
        return view('user.momentm-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|file|mimes:jpeg,png,jpg,gif,mp4|max:10240', // 10MB max
            'descripcion' => 'nullable|string|max:255'
        ]);

        $path = $request->file('contenido')->store('public/momentms');

        Historia::create([
            'id_usuario' => Auth::id(),
            'contenido' => basename($path),
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('user.momentms')->with('success', 'Momentm creado exitosamente');
    }
} 