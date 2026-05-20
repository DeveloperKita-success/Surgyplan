<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_preoperative_checklists', function (Blueprint $table): void {
            $table->boolean('surgical_consent_signed')->default(false)->after('surgical_consent_file');
            $table->boolean('anesthesia_consent_signed')->default(false)->after('anesthesia_consent_file');
            $table->boolean('lab_result_signed')->default(false)->after('lab_result_file');
            $table->boolean('radiology_signed')->default(false)->after('radiology_file');
        });
    }

    public function down(): void
    {
        Schema::table('patient_preoperative_checklists', function (Blueprint $table): void {
            $table->dropColumn([
                'surgical_consent_signed',
                'anesthesia_consent_signed',
                'lab_result_signed',
                'radiology_signed',
            ]);
        });
    }
};
