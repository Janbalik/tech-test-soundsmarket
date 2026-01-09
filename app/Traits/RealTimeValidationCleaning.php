<?php

namespace App\Traits;

trait RealTimeValidationCleaning
{
    public function clearAndValidateField(string $field): void
    {
        $this->resetErrorBag([$field]);
        $this->validateOnly($field);
    }

    public function updatedTitle($value)
    {
        $this->clearAndValidateField('title');
    }

    public function updatedDescription($value)
    {
        $this->clearAndValidateField('description');
    }

    public function updatedPrice($value)
    {
        $this->clearAndValidateField('price');
    }

    public function updatedCategoryId($value)
    {
        $this->clearAndValidateField('category_id');
    }
}