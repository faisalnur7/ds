<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->string('father_name')->nullable()->after('full_name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('spouse_name')->nullable()->after('mother_name');
            $table->string('spouse_phone')->nullable()->after('spouse_name');
            $table->string('blood_group', 10)->nullable()->after('spouse_phone');
            $table->string('religion')->nullable()->after('blood_group');
            $table->string('education')->nullable()->after('religion');
            $table->string('emergency_contact_name')->nullable()->after('education');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->text('present_address')->nullable()->after('address');
            $table->text('permanent_address')->nullable()->after('present_address');
            $table->string('reference_name')->nullable()->after('permanent_address');
            $table->string('reference_phone')->nullable()->after('reference_name');
            $table->text('remarks')->nullable()->after('reference_phone');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropColumn([
                'father_name',
                'mother_name',
                'spouse_name',
                'spouse_phone',
                'blood_group',
                'religion',
                'education',
                'emergency_contact_name',
                'emergency_contact_phone',
                'present_address',
                'permanent_address',
                'reference_name',
                'reference_phone',
                'remarks',
            ]);
        });
    }
};
