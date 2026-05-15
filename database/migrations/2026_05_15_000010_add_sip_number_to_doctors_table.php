<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'sip_number')) {
                $table->string('sip_number')->after('degree');
                $table->unique('sip_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'sip_number')) {
                $table->dropUnique(['sip_number']);
                $table->dropColumn('sip_number');
            }
        });
    }
};
