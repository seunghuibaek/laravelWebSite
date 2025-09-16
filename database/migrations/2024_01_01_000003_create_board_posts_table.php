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
        Schema::create('board_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('password')->nullable(); // 비회원 게시글용
            $table->boolean('is_notice')->default(false);
            $table->boolean('is_secret')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['board_id', 'created_at']);
            $table->index(['board_id', 'is_notice', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_posts');
    }
};