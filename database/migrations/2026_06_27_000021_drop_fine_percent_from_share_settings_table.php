<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('share_settings', function (Blueprint $table): void {
            if (Schema::hasColumn('share_settings', 'fine_percent')) {
                $table->dropColumn('fine_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('share_settings', function (Blueprint $table): void {
            $table->decimal('fine_percent', 8, 2)->default(0)->after('fine_amount');
        });
    }
};
