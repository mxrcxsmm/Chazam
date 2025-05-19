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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('username', 50)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['hombre', 'mujer']);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('puntos')->default(500);
            $table->integer('racha')->default(0);
            $table->bigInteger('id_nacionalidad')->unsigned();
            $table->bigInteger('id_rol')->unsigned();
            $table->bigInteger('id_estado')->unsigned();
            $table->string('img')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('strikes')->nullable()->default('0');
            $table->dateTime('inicio_ban')->nullable();
            $table->dateTime('fin_ban')->nullable();
            $table->dateTime('ultimo_login')->nullable();
            $table->integer('puntos_diarios')->default(0);
            $table->time('skip_time')->default('00:00:00');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_rol')->references('id_rol')->on('roles');
            $table->foreign('id_estado')->references('id_estado')->on('estados');
            $table->foreign('id_nacionalidad')->references('id_nacionalidad')->on('nacionalidad');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
