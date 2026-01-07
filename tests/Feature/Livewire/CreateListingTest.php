<?php

use App\Livewire\CreateListing;
use App\Models\User;    
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
