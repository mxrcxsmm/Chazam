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

        // Obtener momentms del usuario y sus amigos que no hayan pasado 24h desde su última modificación
        $momentms = Historia::where(function($query) use ($user, $amigosIds) {
            $query->whereIn('id_usuario', $amigosIds)
                  ->orWhere('id_usuario', $user->id_usuario);
        })
        ->where('updated_at', '>=', now()->subDay())
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

            // Obtener el usuario actual
            $user = Auth::user();

            // Crear el directorio específico para el usuario si no existe
            $userDirectory = public_path('img/momentms/' . $user->username);
            if (!file_exists($userDirectory)) {
                mkdir($userDirectory, 0777, true);
            }

            // Generar nombre único para el archivo
            $fileName = time() . '_' . uniqid() . '.jpg';
            $filePath = $userDirectory . '/' . $fileName;

            // Guardar la imagen
            file_put_contents($filePath, $image_base64);

            // Crear el registro en la base de datos
            $momentm = new Historia();
            $momentm->id_usuario = Auth::user()->id_usuario;
            $momentm->img = 'img/momentms/' . $user->username . '/' . $fileName;
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

    public function getData($id)
    {
        $momentm = Historia::with('usuario')->findOrFail($id);
        return response()->json([
            'id' => $momentm->id_historia,
            'img' => $momentm->img,
            'fecha_inicio_diff' => $momentm->fecha_inicio->diffForHumans(),
            'usuario' => [
                'username' => $momentm->usuario->username,
                'img' => 'img/profile_img/' . $momentm->usuario->img
            ]
        ]);
    }

    public function destroy($id)
    {
        $momentm = Historia::findOrFail($id);

        // Solo permitir borrar si es el dueño
        if ($momentm->id_usuario != Auth::user()->id_usuario) {
            abort(403, 'No tienes permiso para eliminar este Momentm.');
        }

        // Elimina la imagen del disco si lo deseas
        if ($momentm->img && file_exists(public_path($momentm->img))) {
            unlink(public_path($momentm->img));
        }

        $momentm->delete();

        return response()->json(['success' => true]);
    }
} 