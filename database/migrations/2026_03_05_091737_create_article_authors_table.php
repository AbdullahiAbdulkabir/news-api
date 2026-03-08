<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_authors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['article_id', 'author_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_authors');
    }
};
