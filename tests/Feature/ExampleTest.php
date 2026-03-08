<?php

use App\Models\Page;

it('returns a successful response when homepage exists', function () {
    // Create a homepage with the root path
    Page::factory()->create([
        'path' => '/',
        'is_published' => true,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
});

it('returns 404 when no homepage exists', function () {
    $response = $this->get('/');

    $response->assertStatus(404);
});
