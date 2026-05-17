<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uk_verification_checklists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_request_id')->constrained('surgery_requests');
            $table->foreignId('verified_by')->constrained('users');
            $table->boolean('patient_wristband_installed');
            $table->boolean('doctor_present');
            $table->string('oxygen_saturation')->nullable();
            $table->boolean('operating_room_ready');
            $table->string('anesthesiologist_name')->nullable();
            $table->string('anesthesia_type')->nullable();
            $table->string('asa_status')->nullable();
            $table->boolean('anesthesia_approved');
            $table->text('anesthesia_note')->nullable();
            $table->text('verification_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uk_verification_checklists');
    }
};
