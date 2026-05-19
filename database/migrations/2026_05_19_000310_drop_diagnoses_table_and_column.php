<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surgery_requests', function (Blueprint $table): void {
            $table->dropForeign(['diagnosis_id']);
            $table->dropColumn('diagnosis_id');
        });

        Schema::dropIfExists('diagnoses');
    }

    public function down(): void
    {
        Schema::create('diagnoses', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('surgery_requests', function (Blueprint $table): void {
            $table->foreignId('diagnosis_id')->nullable()->constrained('diagnoses')->nullOnDelete();
        });
    }
};
