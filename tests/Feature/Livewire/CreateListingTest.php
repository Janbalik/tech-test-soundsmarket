<?php

use App\Livewire\CreateListing;
use App\Models\User;  
use App\Models\Listing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

// Protected route
it('redirects unauthenticated users to login', function() {
    get(route('listings.create'))
        ->assertRedirect(route('login')); 
});

// Mount component
it('renders the create listing compoment', function() {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('listings.create'))
        ->assertOk()
        ->assertSeeLivewire(CreateListing::class);
});

// Required fields
it('requires title, description, price and category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', '') 
        ->set('description', '')
        ->set('price', '') 
        ->set('category_id', null)
        ->call('save') 
        ->assertHasErrors([ 
            'title' => 'required',
            'description' => 'required',
        ])
        ->assertHasErrors(['price', 'category_id']);
});

// Price must be numeric and positive
it('requires price to be positive', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('price', -10)
        ->call('save')
        ->assertHasErrors(['price' => 'min']);
});

it('accepts valid decimal price format', function () {
    $user = User::factory()->create();
   
    $this->seed(\Database\Seeders\CategorySeeder::class);

    $category = \App\Models\Category::firstOrFail();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Test')
        ->set('description', 'Desc')
        ->set('category_id', $category->id) 
        ->set('price', 1200.50)
        ->call('save')
        ->assertHasNoErrors(['price']);
});

// Create listing and upload images
it('can upload images and create listing', function () {
    Storage::fake('public'); 
    $user = User::factory()->create();
    
    $this->seed(\Database\Seeders\CategorySeeder::class);
    $category = \App\Models\Category::firstOrFail();

    $file = UploadedFile::fake()->image('guitar.jpg');

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Fender Stratocaster')
        ->set('description', 'Guitarra en buen estado')
        ->set('price', 1200.50)
        ->set('category_id', $category->id)
        ->set('images', [$file])
        ->call('save')
        ->assertHasNoErrors();

    $listing = Listing::first();
    expect($listing)->not->toBeNull();
    expect($listing->title)->toBe('Fender Stratocaster');

    expect($listing->getMedia('images'))->toHaveCount(1);
    
    $mediaItem = $listing->getFirstMedia('images');
    Storage::disk('public')->assertExists($mediaItem->getPathRelativeToRoot());
});
