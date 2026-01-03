<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_selectors', function (Blueprint $table) {
            $table->id();
            $table->string('site', 50);
            $table->string('page_type', 50);
            $table->string('element_key', 100);
            $table->string('selector_type', 20)->default('xpath');
            $table->text('selector_value');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['site', 'page_type', 'element_key'], 'uq_site_page_element');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_selectors');
    }
};
