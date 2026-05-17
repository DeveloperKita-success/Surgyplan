<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->id();
            $table->string('medical_record_number')->unique();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender');
            $table->enum('origin_room', ['IGD', 'Bangsal','Poli'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
