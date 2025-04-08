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
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->bigInteger('valor');
            $table->bigInteger('id_tipo_producto')->unsigned();


            $table->timestamps();

            $table->foreign('id_tipo_producto')->references('id_tipo_producto')->on('tipo_producto');
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
