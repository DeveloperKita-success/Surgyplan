<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ok_validations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operation_booking_id')
                ->constrained('operation_bookings')
                ->cascadeOnDelete();

            // Perawat OK yang validasi
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();

            // pending | approved | rejected
            $table->string('status', 20)->default('pending');
            $table->dateTime('validated_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('operation_booking_id');
            $table->index(['status', 'validated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ok_validations');
    }
};
