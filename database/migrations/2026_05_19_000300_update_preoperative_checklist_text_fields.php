<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY anesthesia_consultation_done VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY vital_sign_stable VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY fasting_more_than_6_hours VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY blood_available VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY infusion_installed VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY catheter_installed VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY surgical_area_shaved VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY jewelry_removed VARCHAR(50) NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY has_previous_surgery VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY anesthesia_consultation_done TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY vital_sign_stable TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY fasting_more_than_6_hours TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY blood_available TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY infusion_installed TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY catheter_installed TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY surgical_area_shaved TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY jewelry_removed TINYINT(1) NOT NULL");
        DB::statement("ALTER TABLE patient_preoperative_checklists MODIFY has_previous_surgery TINYINT(1) NOT NULL");
    }
};
