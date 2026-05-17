<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('surgery_request_id')->constrained('surgery_requests');
            $table->foreignId('changed_by')->constrained('users');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_histories');
    }
};
