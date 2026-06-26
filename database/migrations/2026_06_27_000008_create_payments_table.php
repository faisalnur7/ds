<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->date('payment_month')->index();
            $table->date('due_date')->index();
            $table->decimal('share_value', 12, 2);
            $table->decimal('share_cost', 12, 2);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->boolean('is_late')->default(false);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('payment_status_detail')->default('partial');
            $table->string('payment_method')->index();
            $table->string('transaction_no')->nullable();
            $table->string('status')->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('receipt_no')->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['member_id', 'payment_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
