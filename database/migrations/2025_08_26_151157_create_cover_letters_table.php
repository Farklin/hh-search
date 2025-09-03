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
        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->string('user_hh_id'); // ID пользователя из HeadHunter API
            $table->string('title');
            $table->text('content');
            $table->timestamps();
            
            $table->index('user_hh_id'); // Индекс для быстрого поиска по пользователю
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_letters');
    }
};
