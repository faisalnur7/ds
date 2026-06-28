<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_share_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('previous_share_number')->nullable();
            $table->unsignedSmallInteger('share_number');
            $table->decimal('share_value_per_share', 12, 2)->nullable();
            $table->decimal('share_cost_per_share', 12, 2)->nullable();
            $table->decimal('monthly_amount', 12, 2)->nullable();
            $table->timestamp('changed_at')->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_share_histories');
    }
};
