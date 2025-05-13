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
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id_pago');
            $table->bigInteger('id_comprador')->unsigned()->nullable();
            $table->datetime('fecha_pago');
            $table->bigInteger('id_producto')->unsigned()->nullable();
            $table->timestamps();
        
            $table->foreign('id_comprador')->references('id_usuario')->on('users')->onDelete('set null');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('set null');
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
