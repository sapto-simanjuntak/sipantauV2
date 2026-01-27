<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50);
            $table->enum('verified_role', ['requester', 'validator', 'technician']);
            $table->enum('verification_status', ['valid', 'invalid', 'tampered', 'not_found']);
            $table->string('verified_by_ip', 45)->nullable();
            $table->text('verified_by_user_agent')->nullable();
            $table->bigInteger('verified_by_user_id')->unsigned()->nullable();
            $table->json('verification_data')->nullable(); // Store QR data
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index('ticket_number');
            $table->index('verification_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_verifications');
    }
};
