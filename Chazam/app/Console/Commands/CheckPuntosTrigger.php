<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPuntosTrigger extends Command
{
    protected $signature = 'puntos:check-trigger';
    protected $description = 'Verifica el estado del trigger de puntos diarios';

    public function handle()
    {
        $this->info('Verificando estado del trigger de puntos diarios...');

        // 1. Verificar si el event scheduler está activo
        $schedulerStatus = DB::select("SHOW VARIABLES LIKE 'event_scheduler'")[0]->Value;
        $this->info("Event Scheduler está: " . ($schedulerStatus === 'ON' ? 'ACTIVO' : 'INACTIVO'));

        // 2. Verificar si el evento existe y su estado
        $event = DB::select("
            SELECT 
                EVENT_NAME,
                STATUS,
                LAST_EXECUTED,
                STARTS,
                ENDS,
                INTERVAL_VALUE,
                INTERVAL_FIELD,
                EXECUTE_AT
            FROM information_schema.events 
            WHERE event_name = 'reset_puntos_diarios_event'
        ");
        
        if (empty($event)) {
            $this->error('El evento reset_puntos_diarios_event no existe!');
            return;
        }

        $event = $event[0];
        $this->info("\nInformación del evento:");
        $this->info("Nombre: " . $event->EVENT_NAME);
        $this->info("Estado: " . $event->STATUS);
        $this->info("Última ejecución: " . ($event->LAST_EXECUTED ?? 'Nunca'));
        $this->info("Próxima ejecución: " . ($event->EXECUTE_AT ?? 'No programada'));
        $this->info("Inicio: " . $event->STARTS);
        $this->info("Intervalo: " . $event->INTERVAL_VALUE . " " . $event->INTERVAL_FIELD);
        
        // 3. Verificar la zona horaria
        $timezone = DB::select("SELECT @@global.time_zone, @@session.time_zone")[0];
        $this->info("\nZona horaria del servidor:");
        $this->info("Global: " . $timezone->{'@@global.time_zone'});
        $this->info("Sesión: " . $timezone->{'@@session.time_zone'});

        // 4. Verificar usuarios con puntos
        $usersWithPoints = DB::table('users')
            ->where('puntos_diarios', '>', 0)
            ->count();
        
        $this->info("\nUsuarios con puntos diarios > 0: " . $usersWithPoints);
    }
} 