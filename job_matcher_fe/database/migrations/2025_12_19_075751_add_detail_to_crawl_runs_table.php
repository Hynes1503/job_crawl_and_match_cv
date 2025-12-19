<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->longText('detail')->nullable()->after('error_message');
            // longText đủ lớn để lưu JSON hàng nghìn jobs
        });
    }

    public function down(): void
    {
        Schema::table('crawl_runs', function (Blueprint $table) {
            $table->dropColumn('detail');
        });
    }
};