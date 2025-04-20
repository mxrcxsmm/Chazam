<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y actualiza el estado de usuarios inactivos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        Log::info('Iniciando verificación de usuarios inactivos. Total usuarios: ' . $users->count());
        
        foreach ($users as $user) {
            $isOnline = Cache::has('user-is-online-' . $user->id_usuario);
            Log::info('Usuario ' . $user->id_usuario . ' - Online: ' . ($isOnline ? 'Sí' : 'No') . ' - Estado actual: ' . $user->id_estado);
            
            if (!$isOnline && $user->id_estado == 1) {
                $updated = User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 2]);
                Log::info('Usuario ' . $user->id_usuario . ' marcado como inactivo. Actualización: ' . ($updated ? 'éxito' : 'fallo'));
            }
        }
        
        Log::info('Verificación de usuarios inactivos completada');
        $this->info('Verificación de usuarios inactivos completada');
    }
}
