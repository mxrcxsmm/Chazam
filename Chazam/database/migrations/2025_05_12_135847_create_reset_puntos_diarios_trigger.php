<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateResetPuntosDiariosTrigger extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero eliminamos el evento si existe
        DB::unprepared('DROP EVENT IF EXISTS reset_puntos_diarios_event');

        // Creamos el evento para que se ejecute a las 00:00 de cada día
        DB::unprepared('
            CREATE EVENT reset_puntos_diarios_event
            ON SCHEDULE EVERY 1 DAY
            STARTS (CURRENT_DATE + INTERVAL 1 DAY)
            ON COMPLETION PRESERVE
            DO
                UPDATE users SET puntos_diarios = 0;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP EVENT IF EXISTS reset_puntos_diarios_event');
    }
}
