<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            // Contoh: Hardware, Software, Network, PABX, CCTV, Access Control, Development
            $table->string('category_code', 20)->unique(); // HW, SW, NET, PABX, CCTV, AC, DEV
            $table->boolean('requires_validation')->default(false);
            // TRUE untuk Development/Request Fitur, FALSE untuk incident biasa
            $table->integer('default_sla_hours')->default(24); // Default SLA
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_categories');
    }
};
