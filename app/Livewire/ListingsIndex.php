<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ListingsIndex extends ListingsBase
{
    protected function getViewName(): string
    {
        return 'livewire.listings-index';
    }
}
