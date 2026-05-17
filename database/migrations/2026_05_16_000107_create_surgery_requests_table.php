<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('diagnosis_id')->nullable()->constrained('diagnoses')->nullOnDelete();
            $table->foreignId('procedure_id')->nullable()->constrained('procedures')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('requested_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->date('requested_date');
            $table->time('requested_start_time')->nullable();
            $table->time('requested_end_time')->nullable();
            $table->string('patient_priority');
            $table->string('request_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_requests');
    }
};
