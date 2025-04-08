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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->integer('puntos')->default(500);
            $table->dateTime('inicio_ban')->nullable();
            $table->dateTime('fin_ban')->nullable();
            $table->bigInteger('id_nacionalidad')->unsigned();
            $table->bigInteger('id_rol')->unsigned();
            $table->bigInteger('id_estado')->unsigned();
            $table->bigInteger('id_reto')->unsigned();
            $table->string('img')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('username', 50)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->text('descripcion')->nullable();
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamps();
        
            $table->foreign('id_rol')->references('id_rol')->on('roles');
            $table->foreign('id_estado')->references('id_estado')->on('estados');
            $table->foreign('id_reto')->references('id_reto')->on('retos');
            $table->foreign('id_nacionalidad')->references('id_nacionalidad')->on('nacionalidad');
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
