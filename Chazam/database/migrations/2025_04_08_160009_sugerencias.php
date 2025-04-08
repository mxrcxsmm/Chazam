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
        Schema::create('sugerencia', function (Blueprint $table) {
            $table->bigIncrements('id_sugerencia');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->bigInteger('id_sugerente')->unsigned();
            $table->timestamps();
        
            $table->foreign('id_sugerente')->references('id_usuario')->on('users');
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
