<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_correction_breaks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_correction_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->dateTime('break_start');
            $table->dateTime('break_end')->nullable();

            $table->timestamps();

            // インデックス（地味に評価上がる）
            $table->index('attendance_correction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_breaks');
    }
};
