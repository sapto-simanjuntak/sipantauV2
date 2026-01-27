<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->index('ticket_number');

            // ========== INFORMASI PELAPOR ==========
            $table->unsignedBigInteger('user_id'); //
            $table->string('requester_name', 100);
            $table->string('requester_phone', 20)->nullable();
            $table->unsignedBigInteger('unit_id');

            // ========== DETAIL MASALAH ==========
            $table->string('issue_title');
            $table->text('description');

            // Kategorisasi
            $table->unsignedBigInteger('problem_category_id');
            $table->unsignedBigInteger('problem_sub_category_id')->nullable();

            // ========== DAMPAK & PRIORITAS ==========
            $table->enum('severity_level', ['Rendah', 'Sedang', 'Tinggi', 'Kritis']);
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->boolean('impact_patient_care')->default(false);

            // ========== LOKASI & DEVICE ==========
            $table->string('location');
            $table->string('device_affected')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('connection_status')->nullable();

            // ========== WAKTU ==========
            $table->datetime('occurrence_time');
            $table->timestamp('sla_deadline')->nullable();

            // ========== TINDAKAN ==========
            $table->text('expected_action');

            // ========== FILE/LAMPIRAN ==========
            $table->string('file_path')->nullable();

            // ========== VALIDASI (Untuk Request Pengembangan) ==========
            $table->enum('validation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();

            // ========== ASSIGNMENT ==========
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();

            // ========== CLOSURE ==========
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->text('closure_notes')->nullable();
            $table->enum('user_satisfaction', ['Sangat Puas', 'Puas', 'Cukup', 'Tidak Puas'])->nullable();

            // ========== STATUS ==========
            $table->enum('ticket_status', [
                'Open',
                'Pending',
                'Approved',
                'Assigned',
                'In Progress',
                'Resolved',
                'Closed',
                'Rejected'
            ])->default('Open');

            // ========== INDEXES ==========
            $table->index('user_id');
            $table->index('unit_id');
            $table->index('assigned_to');
            $table->index('validated_by');
            $table->index('assigned_by');
            $table->index('closed_by');
            $table->index('ticket_status');
            $table->index('priority');
            $table->index('created_at');
            $table->index(['unit_id', 'ticket_status']);

            $table->timestamps();


            // ✅ HANYA foreign key untuk tabel di database yang SAMA
            $table->foreign('unit_id')->references('id')->on('hospital_units')->onDelete('restrict');
            $table->foreign('problem_category_id')->references('id')->on('problem_categories')->onDelete('restrict');
            $table->foreign('problem_sub_category_id')->references('id')->on('problem_sub_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
