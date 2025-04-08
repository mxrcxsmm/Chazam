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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->bigIncrements('id_mensaje');
            $table->bigInteger('id_chat_usuario')->unsigned();
            $table->text('contenido');
            $table->datetime('fecha_envio');
            $table->timestamps();
        
            $table->foreign('id_chat_usuario')->references('id_chat_usuario')->on('chats_usuarios');
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
