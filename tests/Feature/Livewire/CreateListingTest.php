<?php

use App\Livewire\CreateListing;
use App\Models\User;
use App\Models\Listing;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

// Protected route
it('redirects unauthenticated users to login', function () {
    get(route('listings.create'))
        ->assertRedirect(route('login'));
});

// Mount component
it('renders the create listing compoment', function () {
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


// No images
it('requires at least one image', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->seed(\Database\Seeders\CategorySeeder::class);
    $category = \App\Models\Category::firstOrFail();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Test sin imágenes')
        ->set('description', 'Desc')
        ->set('price', 200.00)
        ->set('category_id', $category->id)
        ->set('images', [])
        ->call('save')
        ->assertHasErrors([
            'images' => 'required',
        ]);
});


// File is not an image
it('rejects non image files', function () {

    Storage::fake('public');
    $user = User::factory()->create();

    $this->seed(\Database\Seeders\CategorySeeder::class);
    $category = \App\Models\Category::firstOrFail();

    $fakeFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Test archivo inválido')
        ->set('description', 'Desc')
        ->set('price', 200.00)
        ->set('category_id', $category->id)
        ->set('images', [$fakeFile])
        ->call('$refresh')
        // Invalid file now is not in images
        ->assertSet('images', function ($images) {
            return count($images) === 0;
        })
        // It is marked as not valid in invalidFiles
        ->assertSet('invalidFiles.0', 'document.pdf');

});


// Image oversized
it('rejects too large images', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->seed(\Database\Seeders\CategorySeeder::class);
    $category = \App\Models\Category::firstOrFail();

    $bigImage = UploadedFile::fake()->image('big.jpg')->size(3_000);

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', 'Test imagen grande')
        ->set('description', 'Desc')
        ->set('price', 200.00)
        ->set('category_id', $category->id)
        ->set('images', [$bigImage])
        ->call('save')
        ->assertHasErrors([
            'images.0' => 'max',
        ]);
});

// UX: shows validation errors in the view
it('shows validation errors in the create listing view', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('title', '')
        ->set('description', '')
        ->set('price', )
        ->set('category_id', 0)
        ->call('save')
        ->assertHasErrors(['title', 'description', 'price', 'category_id'])
        ->assertSee('El título es obligatorio')
        ->assertSee('La descripción es obligatoria')
        ->assertSee('El precio debe ser mayor que 0');
});

// UX: shows success message after creating listing
it('shows success message after creating a listing', function () {
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
        ->assertHasNoErrors()
        ->assertSee('¡Producto subido!');
});

// Category selector: shows root categories
it('shows root categories in the create listing form', function () {
    $user = User::factory()->create();

    $this->seed(\Database\Seeders\CategorySeeder::class);
    $rootCategories = Category::whereNull('parent_id')->pluck('name')->toArray();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->assertSee($rootCategories[0]); // if we see 1 root, we know they are loaded
});

// Category selector: only allows leaf categories as final category_id
it('sets category_id only when selecting a leaf category', function () {
    $user = User::factory()->create();

    $this->seed(\Database\Seeders\CategorySeeder::class);

    // Get a leaf (with no children)
    $leafCategory = Category::doesntHave('children')->firstOrFail();

    // Build path from leaf to root
    $path = [];
    $current = $leafCategory;

    while ($current) {
        $path[] = $current;
        $current = $current->parent;
    }

    // Revert path to set it in the right order
    $path = array_reverse($path);

    $test = Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->assertSet('category_id', 0);

    foreach ($path as $level => $category) {
        $test->set("categoryPath.$level", $category->id)
            ->call('$refresh');

        if ($level === array_key_last($path)) {
            // Last level: it's a leaf
            $test->assertSet('category_id', $category->id);
        } else {
            // Still not a leaf
            $test->assertSet('category_id', 0);
        }
    }
});


// Images UX: previews are displayed
it('shows image previews when uploading multiple images', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('images', [
            UploadedFile::fake()->image('first.jpg'),
            UploadedFile::fake()->image('second.jpg'),
            UploadedFile::fake()->image('third.jpg'),
        ])
        ->call('$refresh')
        // Dependiendo de tu Blade, puedes comprobar por nombre de archivo
        // o por un texto que solo aparezca si hay imágenes.
        ->assertSee('1')
        ->assertSee('2')
        ->assertSee('3');
});

// Images UX: removeImage() removes an image and it despears
it('removes an image from the preview when calling removeImage', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('images', [
            UploadedFile::fake()->image('keep.jpg'),
            UploadedFile::fake()->image('remove-me.jpg'),
        ]);

    // Ensure we have 2 images
    $component
        ->call('$refresh')
        ->assertSet('images', function ($images) {
            return count($images) === 2;
        });

    // Remove the second one and ensure we have only one
    $component
        ->call('removeImage', 1)
        ->assertSet('images', function ($images) {
            return count($images) === 1;
        });
});

// Images UX: invalidFiles shows not allowed file when it isn't an image
it('tracks invalid files and shows a warning when non-image files are uploaded', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $image = UploadedFile::fake()->image('valid.jpg');
    $pdf   = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    Livewire::actingAs($user)
        ->test(CreateListing::class)
        ->set('images', [$image, $pdf])
        ->call('$refresh')
        // Only images are in the array
        ->assertSet('images', function ($images) {
            return count($images) === 1
                && $images[0]->getClientOriginalName() === 'valid.jpg';
        })
        ->assertSet('invalidFiles.0', 'document.pdf')
        // Message displayed
        ->assertSee('Algunos archivos no son imágenes válidas');
});
