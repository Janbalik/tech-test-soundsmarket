<?php

namespace App\Livewire;

use Livewire\Component;

class CreateListing extends Component
{
    public string $title = '';
    public string $description = '';
    public float $price = 0.0;
    public int $category_id = 0;

    protected array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2500',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'required|integer|min:1|exists:categories,id',
    ];

    public function save(): void
    {
        $this->validate();
    }

    public function render()
    {
        return view('livewire.create-listing');
    }
}
