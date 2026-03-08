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
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraph(),
            'image_url' => $this->faker->imageUrl(),
            'external_url' => $this->faker->url(),
            'source' => $this->faker->randomElement(['NewsAPI', 'The Guardian']),
            'published_at' => $this->faker->date(),
        ];
    }
}
