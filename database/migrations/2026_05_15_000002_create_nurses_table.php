<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ok | biasa
            $table->string('type', 10);

            // IGD | Bangsal | Poli (hanya untuk type=biasa)
            $table->string('unit_asal', 20)->nullable();

            $table->timestamps();

            $table->unique('user_id');
            $table->index(['type', 'unit_asal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
