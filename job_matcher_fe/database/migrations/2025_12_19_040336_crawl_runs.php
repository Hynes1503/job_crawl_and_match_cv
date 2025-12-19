<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('source', 50)->default('topcv');
            $table->string('status', 20)->default('running'); // running, completed, failed
            $table->text('parameters')->nullable(); // lưu keyword, location, search_range...
            $table->integer('jobs_crawled')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_runs');
    }
};