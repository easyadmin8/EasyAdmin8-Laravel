<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_link', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('logo', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('target', 20)->default('_blank');
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
        Schema::dropIfExists('website_link');
    }
};
