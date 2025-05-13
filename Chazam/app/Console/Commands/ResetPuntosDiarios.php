<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPuntosDiarios extends Command
{
    protected $signature = 'puntos:reset';
    protected $description = 'Resetea los puntos diarios de todos los usuarios';

    public function handle()
    {
        $this->info('Reseteando puntos diarios...');
        
        $affected = DB::table('users')->update(['puntos_diarios' => 0]);
        
        $this->info("Puntos diarios reseteados para {$affected} usuarios.");
    }
} 