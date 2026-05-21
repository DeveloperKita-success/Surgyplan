<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_preoperative_checklists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_request_id')->constrained('surgery_requests');
            $table->boolean('surgical_consent');
            $table->string('surgical_consent_file')->nullable();
            $table->boolean('surgical_consent_signed')->default(false);
            $table->boolean('anesthesia_consent');
            $table->string('anesthesia_consent_file')->nullable();
            $table->boolean('anesthesia_consent_signed')->default(false);
            $table->boolean('lab_result_complete');
            $table->string('lab_result_file')->nullable();
            $table->boolean('lab_result_signed')->default(false);
            $table->boolean('radiology_available');
            $table->string('radiology_file')->nullable();
            $table->boolean('radiology_signed')->default(false);
            $table->boolean('anesthesia_consultation_done');
            $table->text('anesthesia_risk_estimation')->nullable();
            $table->boolean('vital_sign_stable');
            $table->text('vital_sign_note')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->text('allergy')->nullable();
            $table->boolean('fasting_more_than_6_hours');
            $table->string('blood_type')->nullable();
            $table->boolean('blood_available');
            $table->boolean('infusion_installed');
            $table->boolean('catheter_installed');
            $table->boolean('surgical_area_shaved');
            $table->boolean('jewelry_removed');
            $table->text('disease_history')->nullable();
            $table->text('current_medications')->nullable();
            $table->boolean('has_previous_surgery');
            $table->text('previous_surgery_note')->nullable();
            $table->date('previous_surgery_date')->nullable();
            $table->text('final_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_preoperative_checklists');
    }
};
