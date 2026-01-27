<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // Nama tabel yang diubah
            $table->unsignedBigInteger('record_id'); // ID dari record yang diubah
            $table->string('action'); // Aksi yang dilakukan (create, update, delete)
            $table->text('old_value')->nullable(); // Nilai sebelum perubahan
            $table->text('new_value')->nullable(); // Nilai setelah perubahan
            $table->string('user_id', 50); // ID pengguna yang melakukan perubahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
