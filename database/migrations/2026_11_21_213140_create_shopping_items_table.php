<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->default('шт'); // шт, кг, л, упак
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('actual_price', 10, 2)->nullable();
            $table->boolean('is_bought')->default(false);
            $table->integer('priority')->default(0); // Для сортировки

            // Связи
            $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bought_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['shopping_list_id', 'is_bought']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_items');
    }
};
