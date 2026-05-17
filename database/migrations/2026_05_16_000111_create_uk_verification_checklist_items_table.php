<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uk_verification_checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_request_id')->constrained('surgery_requests');
            $table->string('name');
            $table->boolean('value');
            $table->text('note')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uk_verification_checklist_items');
    }
};
