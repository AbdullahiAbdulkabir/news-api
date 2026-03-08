<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraph(),
            'image_url' => fake()->imageUrl(),
            'external_url' => fake()->url(),
            'source' => fake()->randomElement(['NewsAPI', 'The Guardian']),
            'published_at' => fake()->date(),
        ];
    }
}
