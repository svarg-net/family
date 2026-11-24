<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('type'); // income, expense, transfer
            $table->timestamp('transaction_date')->useCurrent();

            // Связи
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Кто создал

            // Для переводов между счетами
            $table->foreignId('transfer_to_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'transaction_date']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
