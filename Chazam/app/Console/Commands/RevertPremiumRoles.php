<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class RevertPremiumRoles extends Command
{
    protected $signature = 'roles:revert-premium';
    protected $description = 'Revertir roles Premium cuando expire la suscripción';

    public function handle()
    {
        // ID del rol "Premium" y "Usuario"
        $rolPremiumId = 3; // ID del rol Premium
        $rolUsuarioId = 2; // ID del rol Usuario

        // Buscar usuarios con rol Premium cuya suscripción haya expirado
        $users = User::where('id_rol', $rolPremiumId)
            ->whereHas('pagos', function ($query) {
                $query->where('fecha_pago', '<', Carbon::now()->subMonth());
            })
            ->get();

        foreach ($users as $user) {
            $user->id_rol = $rolUsuarioId; // Cambiar al rol "Usuario"
            $user->save();
        }

        $this->info('Roles Premium revertidos correctamente.');
    }
}