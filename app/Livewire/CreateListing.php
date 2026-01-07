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
    }

    public function render()
    {
        return view('livewire.create-listing');
    }
}
