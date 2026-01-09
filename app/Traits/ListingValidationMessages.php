<?php

namespace App\Traits;

trait ListingValidationMessages
{
    public function getListingValidationMessages(): array
    {
        return [
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
            'newImages.*.image' => 'El archivo debe ser una imagen válida',
            'newImages.*.mimes' => 'La imagen debe ser PNG, JPG, JPEG o WebP',
            'newImages.*.max' => 'Algunas imágenes superan el tamaño máximo de 2MB',
        ];
    }
}
