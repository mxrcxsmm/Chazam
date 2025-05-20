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
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id_chat');
            $table->datetime('fecha_creacion');
            $table->string('img')->nullable();
            $table->string('nombre', 100);
            $table->string('tipocomunidad')->nullable();
            $table->string('codigo', 10)->nullable();
            $table->text('descripcion')->nullable();
            $table->bigInteger('id_reto')->nullable()->unsigned();
            
            $table->timestamps();

            $table->foreign('id_reto')->references('id_reto')->on('retos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
