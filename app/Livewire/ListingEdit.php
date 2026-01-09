<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Listing;
use App\Models\Category;
use App\Traits\ListingValidationMessages;
use App\Traits\RealTimeValidationCleaning;

class ListingEdit extends Component
{
    use WithFileUploads;
    use ListingValidationMessages;
    use RealTimeValidationCleaning;

    public Listing $listing;
    public string $title = '';
    public string $description = '';
    public string $price = '';
    public string $category_id = '';
    public array $categoryPath = [];
    public array $newImages = [];
    public $existingImages;

    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2500',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|integer|min:1|exists:categories,id',
    ];

    public function mount(Listing $listing)
    {
        // Verify owner
        if (auth()->id() !== $listing->user_id) {
            abort(403, 'No tienes permiso para editar este anuncio');
        }

        $this->listing = $listing;
        $this->title = $listing->title;
        $this->description = $listing->description;
        $this->price = $listing->price / 100;
        $this->category_id = $listing->category_id;
        $this->buildCategoryPath($listing->category_id);
        $this->existingImages = $listing->getMedia('images');
    }

    private function buildCategoryPath($categoryId)
    {
        $path = [];
        $category = Category::find($categoryId);

        while ($category) {
            array_unshift($path, $category->id);
            $category = $category->parent;
        }

        $this->categoryPath = $path;
    }

    public function updatedCategoryPath($value, $key)
    {
        $level = (int) $key;

        $this->categoryPath = array_slice($this->categoryPath, 0, $level + 1);

        $selectedCategoryId = $this->categoryPath[$level] ?? 0;
        if ($selectedCategoryId) {
            $category = Category::find($selectedCategoryId);
            if ($category && $category->children->isEmpty()) {
                $this->category_id = $selectedCategoryId;
                $this->clearAndValidateField('category_id');
            } else {
                $this->category_id = 0;
                $this->resetErrorBag(['category_id']);
            }
        } else {
            $this->category_id = 0;
            $this->resetErrorBag(['category_id']);
        }
    }

    public function deleteImage($mediaId)
    {
        $media = $this->listing->media()->find($mediaId);
        if ($media) {
            $media->delete();
            $this->existingImages = $this->listing->getMedia('images');
            session()->flash('success', 'Imagen eliminada correctamente');
        }
    }

    public function removeNewImage($index)
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'newImages.*' => 'image|mimes:png,jpg,jpeg,webp|max:10240',
        ], $this->getListingValidationMessages());

        // Update basic information
        $this->listing->update([
            'title' => $this->title,
            'description' => $this->description,
            'price' => (int)($this->price * 100),
            'category_id' => $this->category_id,
        ]);

        // Add new images
        if (count($this->newImages) > 0) {
            foreach ($this->newImages as $image) {
                $this->listing->addMedia($image)
                    ->toMediaCollection('images');
            }
            $this->newImages = [];
        }

        session()->flash('success', 'Anuncio actualizado correctamente');
        redirect()->route('listing.show', $this->listing);
    }

    public function render()
    {
        $rootCategories = Category::whereNull('parent_id')
        ->with('children')
        ->get();

        $categoriesByLevel = [];

        for ($level = 0; $level < count($this->categoryPath); $level++) {
            $parentId = $this->categoryPath[$level];
            $categoriesByLevel[$level] = Category::where('parent_id', $parentId)
                ->with('children')
                ->get();
        }

        return view('livewire.listing-edit', [
            'rootCategories' => $rootCategories,
            'categoriesByLevel' => $categoriesByLevel,
        ]);
    }
}
