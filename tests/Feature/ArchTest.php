<?php

arch('livewire components')
    ->expect('App\Livewire')
    ->not->toUse(['dd', 'dump', 'ray']);

arch('controllers do not touch models directly')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Models')
    ->ignoring('App\Http\Controllers\Auth');

arch('no debug functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();
