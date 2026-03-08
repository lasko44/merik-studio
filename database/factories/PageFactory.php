<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'path' => '/' . $slug,
            'meta_description' => fake()->sentence(),
            'content' => [
                [
                    'type' => 'text',
                    'data' => [
                        'content' => fake()->paragraphs(3, true),
                    ],
                ],
            ],
            'template' => 'default',
            'is_published' => true,
            'is_admin_page' => false,
            'show_in_nav' => false,
            'nav_order' => 0,
            'sort_order' => 0,
        ];
    }

    /**
     * Indicate that the page is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the page is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the page is an admin page.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin_page' => true,
        ]);
    }

    /**
     * Indicate that the page is shown in navigation.
     */
    public function inNavigation(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_in_nav' => true,
        ]);
    }

    /**
     * Set a specific path.
     */
    public function withPath(string $path): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => $path,
        ]);
    }
}
