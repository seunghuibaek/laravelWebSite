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
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('board_code')->unique();
            $table->string('board_name');
            $table->enum('board_type', ['normal', 'gallery'])->default('normal');
            $table->string('upload_folder')->nullable();
            $table->boolean('use_notice')->default(false);
            $table->boolean('use_file_upload')->default(false);
            $table->integer('max_file_count')->default(1);
            $table->boolean('use_editor')->default(false);
            $table->boolean('use_comment')->default(false);
            $table->integer('max_file_size')->default(10240); // KB 단위
            $table->boolean('allow_user_write')->default(true); // 사용자 글등록 가능 여부
            $table->text('memo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
