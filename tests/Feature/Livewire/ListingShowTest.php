<?php

use App\Livewire\CreateListing;
use App\Livewire\ListingShow;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects to listing show after creating a listing', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    actingAs($user);

    $response = Livewire::test(CreateListing::class)
        ->set('title', 'Controladora DJ')
        ->set('description', 'Perfecto estado')
        ->set('price', 199.90)
        ->set('category_id', $leafCategory->id)
        ->set('images', [UploadedFile::fake()->image('dj.jpg')])
        ->call('save');

    $listing = Listing::first();

    $response->assertHasNoErrors();
    $response->assertRedirect(route('listing.show', $listing));
});

it('displays listing show page for public users', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing = $user->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 199.90,
        'category_id' => $leafCategory->id,
    ]);

    $response = get(route('listing.show', $listing));

    $response->assertOk();
    $response->assertSeeLivewire(ListingShow::class);
});

it('shows public listing details to unauthenticated users', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing = $user->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    $response = get(route('listing.show', $listing));

    $response->assertOk();
    $response->assertSee($listing->title);
    $response->assertSee(number_format($listing->price / 100, 2, ',', '.'));
    $response->assertSee($listing->description);
    $response->assertSee($user->name);
    $response->assertSee($listing->category->name);
    $response->assertDontSee('Editar');
    $response->assertDontSee('Eliminar');
});

it('shows edit and delete buttons to the listing owner', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 199.90,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = get(route('listing.show', $listing));

    $response->assertOk();
    $response->assertSee('Editar');
    $response->assertSee('Eliminar');
});

it('returns 404 for non-existent listing', function () {
    $response = get(route('listing.show', 99999));

    $response->assertNotFound();
});

it('shows seller contact information', function () {
    $user = User::factory()->create([
        'name' => 'Juan Pérez',
    ]);
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = \App\Models\Category::doesntHave('children')->firstOrFail();

    $listing = $user->listings()->create([
        'title' => 'Micrófono Shure',
        'description' => 'Como nuevo',
        'price' => 149.99,
        'category_id' => $leafCategory->id,
    ]);

    $response = get(route('listing.show', $listing));

    $response->assertOk();
    $response->assertSee('Juan Pérez');
});
