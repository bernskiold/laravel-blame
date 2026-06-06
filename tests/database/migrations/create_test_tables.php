<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->blameable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->createdBy();
            $table->timestamps();
        });

        Schema::create('soft_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->deletedBy();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('things', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('author_id')->nullable();
            $table->foreignId('editor_id')->nullable();
            $table->timestamps();
        });
    }
};
