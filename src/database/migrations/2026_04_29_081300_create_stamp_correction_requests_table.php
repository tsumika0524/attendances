<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStampCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('stamp_correction_requests', function (Blueprint $table) {
        $table->id();

        // 誰が
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        // どの勤怠を
        $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();

        // 対象日
        $table->date('target_date');

        // 修正内容
        $table->time('start_time')->nullable();
        $table->time('end_time')->nullable();

        // 休憩（複数対応）
        $table->json('breaks')->nullable();

        // 理由
        $table->text('reason');

        // 状態（超重要）
        $table->string('status')->default('pending'); // pending / approved

        // 承認日時
        $table->timestamp('approved_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stamp_correction_requests');
    }
}
