<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 255)->index();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('image_url')->nullable();
            $table->text('external_url')->nullable();
            $table->string('source', 100);
            $table->timestamp('published_at')->index();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
