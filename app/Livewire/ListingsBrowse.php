<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;

class ListingsBrowse extends ListingsBase
{
    #[Layout('components.layouts.guest')]
    
    protected function getViewName(): string
    {
        return 'livewire.listings-browse';
    }
}