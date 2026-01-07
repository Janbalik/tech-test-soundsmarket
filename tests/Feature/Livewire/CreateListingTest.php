<?php

use App\Livewire\CreateListing;
use App\Models\User;  
use App\Models\Category;
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
            'price' => 'required',
            'category_id' => 'required',
        ]);
});

// Price must be numeric and positive
it('requires price to be numeric', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('price', 'gratis')
        ->call('save')
        ->assertHasErrors(['price' => 'numeric']);
});

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
    $category = Category::first();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Test')
        ->set('description', 'Desc')
        ->set('category_id', $category->id) 
        ->set('price', 1200.50)
        ->call('save')
        ->assertHasNoErrors(['price']);
});