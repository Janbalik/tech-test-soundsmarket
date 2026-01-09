<?php

use App\Livewire\ListingsBrowse;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('displays listings browse page without authentication', function () {
    $response = get(route('home'));

    $response->assertOk();
    $response->assertSeeLivewire(ListingsBrowse::class);
});

it('shows listings on browse page', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing = $user->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 199.99,
        'category_id' => $leafCategory->id,
    ]);

    $response = get(route('home'));

    $response->assertOk();
    $response->assertSee($listing->title);
    $response->assertSee((string) $listing->price);
    $response->assertSee($user->name);
});

it('allows search on browse page', function () {
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

    $response = get(route('home'))
        ->assertSee($listing1->title)
        ->assertSee($listing2->title);

    // Buscar por "Pioneer"
    $component = \Livewire\Livewire::test(ListingsBrowse::class)
        ->set('search', 'Pioneer')
        ->assertSee($listing1->title)
        ->assertDontSee($listing2->title);
});

it('shows login button for unauthenticated users', function () {
    $response = get(route('home'));

    $response->assertOk();
    $response->assertSee('Iniciar Sesión');
});

it('shows sell button for authenticated users', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('home'));

    $response->assertOk();
    $response->assertSee('Subir producto');
});
