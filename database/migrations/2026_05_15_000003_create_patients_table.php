<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('medical_record_number')->nullable();
            $table->string('name');
            $table->string('gender', 10)->nullable(); // L/P (opsional)
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // IGD | Bangsal | Poli (sumber pasien)
            $table->string('source_unit', 20)->nullable();

            $table->timestamps();

            $table->unique('medical_record_number');
            $table->index(['name', 'source_unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
