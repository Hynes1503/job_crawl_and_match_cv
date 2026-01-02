<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->json('cv_used')->nullable()->after('result');
            // Hoặc nếu bạn chỉ muốn lưu thông tin cơ bản:
            // $table->unsignedBigInteger('cv_id')->nullable()->after('result');
            // $table->foreign('cv_id')->references('id')->on('cvs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->dropColumn('cv_used');
            // Nếu dùng cv_id thì drop foreign và column tương ứng
        });
    }
};