<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Видимость: private (только создатель) или family (все члены семьи)
            $table->string('visibility')->default('private');

            // Статус выполнения
            $table->string('status')->default('todo');

            // Приоритет: low, medium, high
            $table->string('priority')->default('medium');

            // Дедлайн
            $table->timestamp('due_date')->nullable();

            // Связи
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Создатель
            $table->foreignId('family_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Исполнитель

            $table->timestamps();
            $table->softDeletes();

            // Индексы для быстрых выборок
            $table->index(['user_id', 'visibility']);
            $table->index(['family_id', 'visibility']);
            $table->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
