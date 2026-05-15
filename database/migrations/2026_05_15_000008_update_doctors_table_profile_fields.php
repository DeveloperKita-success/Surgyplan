<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            // `degree` tetap digunakan untuk menyimpan pangkat.
            $table->text('address')->nullable()->after('degree');
            $table->text('education_history')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'education_history')) {
                $table->dropColumn('education_history');
            }

            if (Schema::hasColumn('doctors', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
