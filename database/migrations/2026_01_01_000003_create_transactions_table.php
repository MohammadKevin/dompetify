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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('target_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->enum('type', ['EXPENSE', 'INCOME', 'TRANSFER'])->default('EXPENSE');
            $table->decimal('amount', 15, 2);
            $table->decimal('admin_fee', 10, 2)->default(0.00);
            $table->timestamp('date')->useCurrent();
            $table->text('description')->nullable();
            $table->string('receipt_image_path')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'date']);
            $table->index(['type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
