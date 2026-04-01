<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->default(0);
            $table->string('title', 150);
            $table->string('slug', 150)->nullable();
            $table->string('model_no', 120)->nullable()->comment('型号');
            $table->string('cover', 500)->nullable();
            $table->text('gallery')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->longText('parameters')->nullable()->comment('参数文本 一行一项');
            $table->string('download_url', 500)->nullable()->comment('资料下载');
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_keywords', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->unsignedTinyInteger('is_featured')->default(0);
            $table->unsignedTinyInteger('is_new')->default(0);
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
        Schema::dropIfExists('website_product');
    }
};
