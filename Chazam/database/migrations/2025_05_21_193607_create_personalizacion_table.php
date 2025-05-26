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
        Schema::create('personalizacion', function (Blueprint $table) {
            $table->bigIncrements('id_personalizacion');

            // Relación con el usuario
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');

            // Personalización
            $table->string('marco')->nullable();
            $table->boolean('rotacion')->default(false);
            $table->string('brillo')->nullable();
            $table->string('sidebar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalizacion');
    }
};