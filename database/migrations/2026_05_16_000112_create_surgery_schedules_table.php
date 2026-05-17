<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_request_id')->constrained('surgery_requests');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->foreignId('operating_room_id')->constrained('operating_rooms');
            $table->foreignId('approved_by')->constrained('users');
            $table->date('surgery_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('schedule_status');
            $table->text('rejection_reason')->nullable();
            $table->text('delay_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_schedules');
    }
};
