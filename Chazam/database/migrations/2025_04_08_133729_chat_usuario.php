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
        Schema::create('chat_usuario', function (Blueprint $table) {
            $table->bigIncrements('id_chat_usuario');
            $table->bigInteger('id_chat')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();
            $table->timestamps();
        
            $table->foreign('id_chat')->references('id_chat')->on('chats');
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
