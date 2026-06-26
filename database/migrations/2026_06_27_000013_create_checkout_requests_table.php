<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->timestamp('requested_at')->useCurrent();
            $table->string('checkout_type')->default('full')->index();
            $table->decimal('partial_percentage', 8, 2)->nullable();
            $table->decimal('refundable_amount', 12, 2)->default(0);
            $table->decimal('outstanding_loan_deducted', 12, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_requests');
    }
};
