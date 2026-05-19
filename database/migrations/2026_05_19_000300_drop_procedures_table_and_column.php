<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surgery_requests', function (Blueprint $table): void {
            $table->dropForeign(['procedure_id']);
            $table->dropColumn('procedure_id');
        });

        Schema::dropIfExists('procedures');
    }

    public function down(): void
    {
        Schema::create('procedures', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('surgery_requests', function (Blueprint $table): void {
            $table->foreignId('procedure_id')->nullable()->constrained('procedures')->nullOnDelete();
        });
    }
};
