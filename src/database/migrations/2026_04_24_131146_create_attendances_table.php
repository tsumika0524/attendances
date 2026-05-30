<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // ユーザー
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 勤務日（←超重要）
            $table->date('work_date');

            // 打刻
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();

            // ステータス（数値管理）
            $table->unsignedTinyInteger('status')->default(0);
            /*
                0: 勤務外
                1: 出勤中
                2: 休憩中
                3: 退勤済
            */

            // 備考
            $table->text('note')->nullable();

            $table->timestamps();

            // 1日1レコード制約（超重要）
            $table->unique(['user_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};