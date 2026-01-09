<?php

use App\Livewire\ListingEdit;
use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects unauthenticated users to login', function () {
    $user = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $user->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    $response = get(route('listing.edit', $listing));

    $response->assertRedirect(route('login'));
});

it('denies access to non-owner users', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($otherUser);

    $response = get(route('listing.edit', $listing));

    $response->assertStatus(403);
});

it('allows owner to access edit page', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = get(route('listing.edit', $listing));

    $response->assertOk();
    $response->assertSeeLivewire(ListingEdit::class);
});

it('loads listing data on mount', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = get(route('listing.edit', $listing));

    $response->assertSee($listing->title);
    $response->assertSee($listing->description);
});

it('validates required fields on update', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', '')
        ->set('description', '')
        ->set('price', '')
        ->set('category_id', '')
        ->call('save');

    $response->assertHasErrors(['title', 'description', 'price', 'category_id']);
});

it('validates price is numeric', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Valid Title')
        ->set('description', 'Valid Description')
        ->set('price', 'invalid-price')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertHasErrors('price');
});

it('validates price is positive', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Valid Title')
        ->set('description', 'Valid Description')
        ->set('price', '-10')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertHasErrors('price');
});

it('validates title max length', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', str_repeat('a', 256))
        ->set('description', 'Valid Description')
        ->set('price', '100')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertHasErrors('title');
});

it('validates description max length', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Valid Title')
        ->set('description', str_repeat('a', 5001))
        ->set('price', '100')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertHasErrors('description');
});

it('validates category exists', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Valid Title')
        ->set('description', 'Valid Description')
        ->set('price', '100')
        ->set('category_id', '9999')
        ->call('save');

    $response->assertHasErrors('category_id');
});

it('updates listing with valid data', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated Description')
        ->set('price', '299.99')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertHasNoErrors();

    $listing->refresh();

    expect($listing->title)->toBe('Updated Title');
    expect($listing->description)->toBe('Updated Description');
    expect($listing->price)->toBe(29999);
});

it('redirects to listing show after successful update', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated Description')
        ->set('price', '100')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $response->assertRedirect(route('listing.show', $listing));
});

it('updates category correctly', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory1 = Category::doesntHave('children')->first();
    $leafCategory2 = Category::doesntHave('children')->skip(1)->first();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory1->id,
    ]);

    actingAs($owner);

    $response = Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated Description')
        ->set('price', '100')
        ->set('category_id', $leafCategory2->id)
        ->call('save');

    $response->assertHasNoErrors();

    $listing->refresh();

    expect($listing->category_id)->toBe($leafCategory2->id);
});

it('preserves user_id when updating listing', function () {
    $owner = User::factory()->create();
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    $listing = $owner->listings()->create([
        'title' => 'Controladora DJ',
        'description' => 'Perfecto estado',
        'price' => 19990,
        'category_id' => $leafCategory->id,
    ]);

    $originalUserId = $listing->user_id;

    actingAs($owner);

    Livewire::test(ListingEdit::class, ['listing' => $listing])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated Description')
        ->set('price', '100')
        ->set('category_id', $leafCategory->id)
        ->call('save');

    $listing->refresh();

    expect($listing->user_id)->toBe($originalUserId);
});

it('handles listing not found gracefully', function () {
    $owner = User::factory()->create();

    actingAs($owner);

    $response = get(route('listing.edit', 9999));

    $response->assertNotFound();
});
