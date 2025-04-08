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
        Schema::create('reportes', function (Blueprint $table) {
            $table->bigIncrements('id_reporte');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->bigInteger('id_reportador')->unsigned();
            $table->bigInteger('id_reportado')->unsigned();
            $table->timestamps();
        
            $table->foreign('id_reportador')->references('id_usuario')->on('users');
            $table->foreign('id_reportado')->references('id_usuario')->on('users');
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
