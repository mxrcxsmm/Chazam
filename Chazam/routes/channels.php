Broadcast::channel('comunidad.{id}', function ($user, $id) {
    // Verificar si el usuario es miembro de la comunidad
    return \App\Models\ChatUsuario::where('id_chat', $id)
        ->where('id_usuario', $user->id_usuario)
        ->exists();
}); 