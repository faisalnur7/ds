<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('member_code')->unique();
            $table->string('full_name');
            $table->text('phone');
            $table->text('nid_number');
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('nominee_phone')->nullable();
            $table->date('join_date')->index();
            $table->string('membership_status')->default('active')->index();
            $table->unsignedSmallInteger('checkout_eligible_after_months')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
