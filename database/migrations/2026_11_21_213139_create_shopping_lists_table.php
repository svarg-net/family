<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // Видимость
            $table->string('visibility')->default('private');

            // Статус: active, completed, archived
            $table->string('status')->default('active');

            // Связи
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Создатель
            $table->foreignId('family_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'visibility']);
            $table->index(['family_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_lists');
    }
};
