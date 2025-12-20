<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->json('result')->nullable()->after('detail');
            // Nếu bạn muốn lưu nhiều lần matching cho cùng 1 run, có thể dùng json array
            // Hoặc nếu chỉ lưu lần matching mới nhất → vẫn dùng json
        });
    }

    public function down(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }
};