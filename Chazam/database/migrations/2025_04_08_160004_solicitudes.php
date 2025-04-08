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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id_solicitud');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada','solicitando','blockeada']);
            $table->bigInteger('id_emisor')->unsigned();
            $table->bigInteger('id_receptor')->unsigned();
            $table->timestamps();
        
            $table->foreign('id_emisor')->references('id_usuario')->on('users');
            $table->foreign('id_receptor')->references('id_usuario')->on('users');
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
