<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
class ListingShow extends Component
{
    public Listing $listing;
    public int $currentImageIndex = 0;

    public function mount(Listing $listing)
    {
        $this->listing = $listing->load(['user', 'category', 'media']);
    }

    public function getBackRoute(): string
    {
        return Auth::check() ? route('listings.index') : route('home');
    }

    public function nextImage()
    {
        $media = $this->listing->getMedia('images');
        $this->currentImageIndex = ($this->currentImageIndex + 1) % $media->count();
    }

    public function previousImage()
    {
        $media = $this->listing->getMedia('images');
        $this->currentImageIndex = ($this->currentImageIndex - 1 + $media->count()) % $media->count();
    }

    public function delete()
    {
        if ($this->listing->user_id === auth()->id()) {
            $this->listing->delete();
            return redirect()->route('listings.index');
        }
    }

    public function render()
    {

        $media = $this->listing->getMedia('images');
        $isOwner = auth()->check() && auth()->id() === $this->listing->user_id;

        return view('livewire.listing-show', [
            'listing' => $this->listing,
            'media' => $media,
            'isOwner' => $isOwner,
            'backRoute' => $this->getBackRoute(),
        ]);
    }
}
