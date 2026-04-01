<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_article', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->default(0);
            $table->string('title', 180);
            $table->string('slug', 180)->nullable();
            $table->string('cover', 500)->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('author', 100)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_keywords', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->unsignedTinyInteger('is_recommend')->default(0);
            $table->unsignedInteger('published_at')->nullable();
            $table->integer('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('remark', 255)->nullable();
            $table->unsignedInteger('create_time')->nullable();
            $table->unsignedInteger('update_time')->nullable();
            $table->unsignedInteger('delete_time')->nullable();

            $table->index(['category_id', 'status']);
            $table->index(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_article');
    }
};
