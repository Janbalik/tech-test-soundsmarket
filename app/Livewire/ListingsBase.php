<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Listing;

abstract class ListingsBase extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    protected function getListings()
    {
        return Listing::query()
            ->when($this->search, fn($query) =>
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->with(['user', 'category', 'media'])
            ->orderByDesc('created_at')
            ->paginate(12);
    }

    public function render()
    {
        return view($this->getViewName(), [
            'listings' => $this->getListings(),
        ]);
    }

    abstract protected function getViewName(): string;
}
