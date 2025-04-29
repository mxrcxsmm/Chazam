<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Historia;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MomentmsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener IDs de amigos directamente de la tabla solicitudes
        $solicitudesAceptadas = Solicitud::where('estado', 'aceptada')
            ->where(function($query) use ($user) {
                $query->where('id_emisor', $user->id_usuario)
                      ->orWhere('id_receptor', $user->id_usuario);
            })
            ->get();

        // Recolectar IDs de amigos
        $amigosIds = collect();
        foreach ($solicitudesAceptadas as $solicitud) {
            if ($solicitud->id_usuario_solicitante == $user->id_usuario) {
                $amigosIds->push($solicitud->id_usuario_solicitado);
            } else {
                $amigosIds->push($solicitud->id_usuario_solicitante);
            }
        }

        // Obtener usuarios amigos
        $amigos = User::whereIn('id_usuario', $amigosIds)->get();

        // Obtener momentms del usuario y sus amigos
        $momentms = Historia::where(function($query) use ($user, $amigosIds) {
            $query->whereIn('id_usuario', $amigosIds)
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
        try {
            $request->validate([
                'contenido' => 'required|string',
            ]);

            // Obtener la imagen base64
            $image_data = $request->input('contenido');
            
            // Extraer la parte de datos de la cadena base64
            $image_parts = explode(",", $image_data);
            $image_base64 = base64_decode($image_parts[1]);

            // Crear el directorio si no existe
            $directory = public_path('img/momentms');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // Generar nombre único para el archivo
            $fileName = time() . '_' . uniqid() . '.jpg';
            $filePath = $directory . '/' . $fileName;

            // Guardar la imagen
            file_put_contents($filePath, $image_base64);

            // Crear el registro en la base de datos
            $momentm = new Historia();
            $momentm->id_usuario = auth()->id();
            $momentm->img = 'img/momentms/' . $fileName;
            $momentm->fecha_inicio = now();
            $momentm->fecha_fin = now()->addDay();
            $momentm->save();

            return response()->json([
                'success' => true,
                'message' => 'Momentm guardado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al guardar Momentm: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el Momentm: ' . $e->getMessage()
            ], 400);
        }
    }
} 