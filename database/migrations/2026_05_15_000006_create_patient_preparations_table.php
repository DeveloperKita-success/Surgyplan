<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_preparations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operation_booking_id')
                ->constrained('operation_bookings')
                ->cascadeOnDelete();

            // Perawat yang menyiapkan (opsional, bisa null kalau input dari admin)
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('fasting_confirmed')->default(false);
            $table->boolean('consent_signed')->default(false);
            $table->boolean('labs_complete')->default(false);

            $table->dateTime('prepared_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('operation_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_preparations');
    }
};
