<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historias', function (Blueprint $table) {
            $table->bigIncrements('id_historia');
            $table->bigInteger('id_usuario')->unsigned();
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
            $table->string('img');
            $table->timestamps();
        
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
