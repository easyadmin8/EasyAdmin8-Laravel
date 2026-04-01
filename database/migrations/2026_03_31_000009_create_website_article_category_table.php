<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_article_category', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('slug', 120)->nullable();
            $table->text('summary')->nullable();
            $table->string('cover', 500)->nullable();
            $table->integer('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('remark', 255)->nullable();
            $table->unsignedInteger('create_time')->nullable();
            $table->unsignedInteger('update_time')->nullable();
            $table->unsignedInteger('delete_time')->nullable();

            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_article_category');
    }
};
