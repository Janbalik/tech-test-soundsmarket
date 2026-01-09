<?php

use App\Livewire\ListingsIndex;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication to view listings index', function () {
    $response = get(route('listings.index'));

    $response->assertRedirect(route('login'));
});

it('displays listings index page for authenticated users', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('listings.index'));

    $response->assertOk();
    $response->assertSeeLivewire(ListingsIndex::class);
});

it('shows all listings on index page', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing1 = $user1->listings()->create([
        'title' => 'Controladora DJ Pioneer',
        'description' => 'Excelente estado',
        'price' => 299.99,
        'category_id' => $leafCategory->id,
    ]);

    $listing2 = $user2->listings()->create([
        'title' => 'Micrófono Shure',
        'description' => 'Como nuevo',
        'price' => 149.99,
        'category_id' => $leafCategory->id,
    ]);

    $authenticatedUser = User::factory()->create();
    actingAs($authenticatedUser);

    $response = get(route('listings.index'));

    $response->assertOk();
    $response->assertSee($listing1->title);
    $response->assertSee($listing2->title);
});

it('allows search on index page', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing1 = $user->listings()->create([
        'title' => 'Controladora DJ Pioneer',
        'description' => 'Excelente estado',
        'price' => 299.99,
        'category_id' => $leafCategory->id,
    ]);

    $listing2 = $user->listings()->create([
        'title' => 'Micrófono Shure',
        'description' => 'Como nuevo',
        'price' => 149.99,
        'category_id' => $leafCategory->id,
    ]);

    $authenticatedUser = User::factory()->create();
    actingAs($authenticatedUser);

    $response = get(route('listings.index'))
        ->assertSee($listing1->title)
        ->assertSee($listing2->title);

    $component = \Livewire\Livewire::test(ListingsIndex::class)
        ->set('search', 'Pioneer')
        ->assertSee($listing1->title)
        ->assertDontSee($listing2->title);
});

it('shows sidebar for authenticated users', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('listings.index'));

    $response->assertOk();
    $response->assertSee('Dashboard'); //
});
