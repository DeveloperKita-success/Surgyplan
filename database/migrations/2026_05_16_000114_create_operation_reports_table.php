<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_schedule_id')->constrained('surgery_schedules');
            $table->foreignId('doctor_id')->constrained('doctors');
            $table->text('operation_result')->nullable();
            $table->text('complication')->nullable();
            $table->text('post_operation_note')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_reports');
    }
};
