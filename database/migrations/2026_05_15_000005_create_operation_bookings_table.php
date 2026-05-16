<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operation_room_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();

            // Siapa yang membuat booking (dokter/perawat)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('scheduled_at');

            // draft | booked | ongoing | done | cancelled
            $table->string('status', 20)->default('draft');

            $table->boolean('is_emergency')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['scheduled_at', 'status']);
            $table->index(['doctor_id', 'scheduled_at']);
            $table->index(['operation_room_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_bookings');
    }
};
