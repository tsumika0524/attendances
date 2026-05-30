<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();

            // 申請ユーザー
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 対象勤怠
            $table->foreignId('attendance_id')
                ->constrained()
                ->cascadeOnDelete();

            // 承認者（管理者）
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            // 修正申請内容
            $table->dateTime('requested_clock_in')->nullable();
            $table->dateTime('requested_clock_out')->nullable();

            // 備考（必須）
            $table->text('note');

            // ステータス
            $table->unsignedTinyInteger('status')->default(0);
            /*
                0: 承認待ち
                1: 承認済み
                2: 却下
            */

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
