<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_id');
            $table->unsignedBigInteger('technician_id');

            // ========== STATUS ==========
            $table->enum('status', [
                'Assigned',       // Baru dapat tugas
                'On The Way',     // Teknisi dalam perjalanan (penting untuk RS!)
                'In Progress',    // Sedang dikerjakan
                'Waiting Parts',  // Menunggu spare part
                'Escalated',      // Di-eskalasi ke vendor/level lebih tinggi
                'Resolved',       // Selesai
                'Closed'          // User sudah konfirmasi
            ])->default('Assigned');

            // ========== DIAGNOSIS & TINDAKAN ==========
            $table->text('diagnosis')->nullable(); // Apa penyebab masalahnya
            $table->text('action_taken')->nullable(); // Tindakan yang dilakukan
            $table->text('technician_notes')->nullable(); // Catatan teknisi

            // ========== UNTUK ESCALATION ==========
            $table->boolean('is_escalated')->default(false);
            $table->text('escalation_reason')->nullable(); // Kenapa di-eskalasi
            $table->string('escalated_to')->nullable(); // Vendor/pihak ketiga

            // ========== SPARE PARTS (Opsional, bisa disederhanakan) ==========
            $table->text('parts_used')->nullable(); // List spare part yang dipakai

            // ========== TIMELINE ==========
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable(); // Mulai handle
            $table->timestamp('arrived_at')->nullable(); // Tiba di lokasi (penting!)
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // ========== FOREIGN KEYS ==========
            $table->foreign('service_request_id')->references('id')->on('service_requests')->onDelete('cascade');
            $table->foreign('technician_id')->references('id')->on('users')->onDelete('cascade');

            // ========== INDEXES ==========
            $table->index('service_request_id');
            $table->index('technician_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_responses');
    }
};
