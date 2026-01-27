<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('problem_category_id');
            $table->string('sub_category_name');
            // Contoh untuk Hardware: PC Mati, Printer Rusak, Monitor Blank
            // Contoh untuk Software: SIMRS Error, Aplikasi Lambat
            // Contoh untuk Network: Internet Down, Wifi Lemah
            $table->timestamps();

            $table->foreign('problem_category_id')->references('id')->on('problem_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_sub_categories');
    }
};
