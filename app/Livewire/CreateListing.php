<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateListing extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public float $price = 0.0;
    public int $category_id = 0;
    public array $images = [];

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
        'images.max' => 'Puedes subir máximo 10 imágenes',
        'images.array' => 'Las imágenes deben ser archivos válidos',
        'images.*.image' => 'Algunos archivos no son imágenes válidas. Solo se aceptan PNG, JPG, JPEG y WebP',
        'images.*.mimes' => 'Solo se aceptan imágenes en formato PNG, JPG, JPEG o Webp',
        'images.*.max' => 'Algunas imágenes superan el tamaño máximo de 2MB',
    ];

    public function save(): void
    {
        $this->validate();

        $listing = Listing::create([
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'user_id' => auth()->id(),
        ]);

        foreach ($this->images as $image) {
            $listing->addMedia($image)->toMediaCollection('images');
        }
        session()->flash('success', '¡Producto subido!');
    }

    public function render()
    {
        return view('livewire.create-listing');
    }
}
