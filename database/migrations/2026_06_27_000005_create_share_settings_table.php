<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_settings', function (Blueprint $table): void {
            $table->id();
            $table->decimal('share_value', 12, 2);
            $table->decimal('share_cost', 12, 2);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->date('effective_from')->index();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_settings');
    }
};
