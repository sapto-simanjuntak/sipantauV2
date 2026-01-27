<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TABEL BARU: Hospital Units (Unit/Ruangan di RS)
     */
    public function up(): void
    {
        Schema::create('hospital_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code', 20)->unique(); // IGD, ICU, RWI, POLI, dll
            $table->string('unit_name', 100); // Instalasi Gawat Darurat, ICU, dll
            $table->enum('unit_type', [
                'Critical',      // IGD, ICU, NICU, OK (prioritas tertinggi)
                'Clinical',      // Poliklinik, Rawat Inap
                'Support',       // Lab, Radiologi, Farmasi
                'Administrative' // Keuangan, HRD, IT
            ]);
            $table->string('location')->nullable(); // Lokasi fisik (Gedung A Lt.2)
            $table->unsignedBigInteger('pic_user_id')->nullable(); // PIC unit
            $table->boolean('is_24_hours')->default(false); // Operasional 24 jam?
            $table->integer('sla_response_minutes')->default(60); // Target response time (menit)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pic_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_units');
    }
};
