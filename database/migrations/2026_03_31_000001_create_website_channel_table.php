<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_channel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pid')->default(0)->comment('父级栏目');
            $table->string('title', 100)->comment('栏目名称');
            $table->string('slug', 120)->nullable()->comment('栏目别名');
            $table->string('type', 30)->default('single')->comment('栏目类型');
            $table->string('cover', 500)->nullable()->comment('栏目封面');
            $table->string('banner', 500)->nullable()->comment('栏目横幅');
            $table->text('summary')->nullable()->comment('简介');
            $table->longText('content')->nullable()->comment('单页正文');
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_keywords', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->unsignedTinyInteger('is_nav')->default(1)->comment('是否显示在导航');
            $table->unsignedTinyInteger('is_index')->default(0)->comment('是否首页推荐');
            $table->integer('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('remark', 255)->nullable();
            $table->unsignedInteger('create_time')->nullable();
            $table->unsignedInteger('update_time')->nullable();
            $table->unsignedInteger('delete_time')->nullable();

            $table->index(['pid', 'status']);
            $table->index(['slug']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_channel');
    }
};
