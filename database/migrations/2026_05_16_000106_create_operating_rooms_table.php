<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operating_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('specialist_id')->nullable()->constrained('specialists')->nullOnDelete();
            $table->string('room_code');
            $table->string('room_name');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operating_rooms');
    }
};
