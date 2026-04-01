<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_hot_keyword', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 100);
            $table->string('link', 500)->nullable();
            $table->integer('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('remark', 255)->nullable();
            $table->unsignedInteger('create_time')->nullable();
            $table->unsignedInteger('update_time')->nullable();
            $table->unsignedInteger('delete_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_hot_keyword');
    }
};
