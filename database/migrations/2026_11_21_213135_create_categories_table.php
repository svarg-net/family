<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#808080'); // HEX цвет
            $table->string('icon')->nullable(); // Иконка (emoji или название)

            // Тип категории: task, account, shopping
            $table->string('type');

            // Принадлежность: либо личная, либо семейная, либо системная
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('family_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Индекс для быстрого поиска
            $table->index(['type', 'user_id', 'family_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
