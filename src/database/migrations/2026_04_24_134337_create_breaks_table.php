<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breaks', function (Blueprint $table) {
            $table->id();

            // 外部キー
            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->onDelete('cascade');

            // 休憩開始
            $table->dateTime('break_start');

            // 休憩終了（最初は空でOK）
            $table->dateTime('break_end')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breaks');
    }
};
