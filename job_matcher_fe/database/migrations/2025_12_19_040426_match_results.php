<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('match_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cv_id')
                  ->constrained('cvs')
                  ->cascadeOnDelete();

            $table->foreignId('job_id')
                  ->constrained('jobs')
                  ->cascadeOnDelete();

            $table->float('score'); // cosine similarity, etc
            $table->timestamps();

            // tránh trùng match
            $table->unique(['cv_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
