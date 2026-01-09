<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateListing extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public float $price = 0.0;
    public int $category_id = 0;
    public array $categoryPath = [];
    public array $images = [];
    public array $invalidFiles = [];

    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2500',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|integer|min:1|exists:categories,id',
        'images' => 'required|array|min:1|max:6',
        'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
    ];

    protected array $messages = [
        'title.required' => 'El título es obligatorio',
        'title.max' => 'El título no puede exceder 255 caracteres',
        'description.required' => 'La descripción es obligatoria',
        'description.max' => 'La descripción no puede exceder 2500 caracteres',
        'price.required' => 'El precio es obligatorio',
        'price.numeric' => 'El precio debe ser un número válido',
        'price.min' => 'El precio debe ser mayor que 0',
        'category_id.required' => 'Selecciona una categoría válida',
        'category_id.min' => 'Selecciona una categoría válida',
        'category_id.exists' => 'La categoría seleccionada no existe',
        'images.required' => 'Sube al menos una imagen',
        'images.min' => 'Sube al menos una imagen',
        'images.max' => 'Puedes subir máximo 6 imágenes',
        'images.array' => 'Las imágenes deben ser archivos válidos',
        'images.*.image' => 'Algunos archivos no son imágenes válidas. Solo se aceptan PNG, JPG, JPEG y WebP',
        'images.*.mimes' => 'Solo se aceptan imágenes en formato PNG, JPG, JPEG o Webp',
        'images.*.max' => 'Algunas imágenes superan el tamaño máximo de 2MB',
    ];

    public function mount(): void
    {
        $this->title = '';
        $this->description = '';
        $this->price = 0.0;
        $this->category_id = 0;
        $this->categoryPath = [];
        $this->images = [];
        $this->invalidFiles = [];
        $this->resetErrorBag();
    }

    public function updatedCategoryPath($value, $key): void
    {
        $level = (int) $key;

        // Truncate array to remove lower levels
        $this->categoryPath = array_slice($this->categoryPath, 0, $level + 1);
        // Set as category if it is the last level
        $selectedCategoryId = $this->categoryPath[$level] ?? 0;

        if ($selectedCategoryId) {
            $category = Category::with('children')->find($selectedCategoryId);

            if ($category && $category->children->isEmpty()) {
                // Last level: set category
                $this->category_id = $selectedCategoryId;
                $this->resetErrorBag(['category_id']);
                $this->validateOnly('category_id');
            } else {
                // Still not last level
                $this->category_id = 0;
                $this->resetErrorBag(['category_id']);
            }
        } else {
            $this->category_id = 0;
            $this->resetErrorBag(['category_id']);
        }
    }

    public function updatedImages(): void
    {
        $this->invalidFiles = [];
        $validImages = [];

        foreach ($this->images as $image) {
            if (! $image instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }
            $ext = strtolower($image->getClientOriginalExtension());

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $validImages[] = $image;
            } else {
                $this->invalidFiles[] = $image->getClientOriginalName();
            }
        }

        // Get  only images
        $this->images = $validImages;
        $this->resetErrorBag(['images', 'images.*']);

        // Validate only images
        try {
            $this->validateOnly('images');
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    public function removeImage(int $index): void
    {
        unset($this->images[$index]);

        $this->images = array_values($this->images);
        $this->resetErrorBag(['images', 'images.*']);

        try {
            $this->validateOnly('images');
        } catch (\Exception $e) {
            // Ignore
        }
    }

    public function save(): void
    {
        $this->validate();

        $listing = Listing::create([
            'title' => $this->title,
            'description' => $this->description,
            'price' => (int)($this->price * 100),
            'category_id' => $this->category_id,
            'user_id' => auth()->id(),
        ]);

        foreach ($this->images as $image) {
            $listing->addMedia($image)->toMediaCollection('images');
        }
        $this->reset('title', 'description', 'price', 'category_id', 'images');
        $this->resetErrorBag(['images', 'images.*']);
        session()->flash('success', '¡Producto subido!');
        redirect()->route('listing.show', $listing);
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

        return view('livewire.create-listing', [
            'rootCategories' => $rootCategories,
            'categoriesByLevel' => $categoriesByLevel,
        ]);
    }
}
