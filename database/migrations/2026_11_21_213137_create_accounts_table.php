<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // cash, card, savings, crypto, etc.
            $table->string('currency', 3)->default('RUB');
            $table->decimal('balance', 15, 2)->default(0);

            // Видимость
            $table->string('visibility')->default('private');

            // Связи
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('family_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'visibility']);
            $table->index(['family_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
