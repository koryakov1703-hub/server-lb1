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
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // token "id" (jti) - уникальный идентификатор токена (можно хранить открыто)
            $table->string('jti', 64)->unique();

            // хеш refresh токена (сам refresh токен в БД не храним)
            $table->string('refresh_hash', 128)->nullable()->unique();

            // метаданные
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('access_expires_at');
            $table->timestamp('refresh_expires_at')->nullable();

            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('refresh_used_at')->nullable(); // одноразовость refresh

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
