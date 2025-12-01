<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories
        $instrumentosMusicales = Category::create([
            'name' => 'Instrumentos Musicales',
            'parent_id' => null,
        ]);

        $equiposDJ = Category::create([
            'name' => 'Equipos de DJ',
            'parent_id' => null,
        ]);

        // Create child categories for Instrumentos Musicales
        $guitarras = Category::create([
            'name' => 'Guitarras',
            'parent_id' => $instrumentosMusicales->id,
        ]);

        Category::create([
            'name' => 'Baterías',
            'parent_id' => $instrumentosMusicales->id,
        ]);

        Category::create([
            'name' => 'Teclados',
            'parent_id' => $instrumentosMusicales->id,
        ]);

        // Create sub-categories for Guitarras
        Category::create([
            'name' => 'Guitarras Eléctricas',
            'parent_id' => $guitarras->id,
        ]);

        Category::create([
            'name' => 'Guitarras Acústicas',
            'parent_id' => $guitarras->id,
        ]);

        // Create child categories for Equipos de DJ
        Category::create([
            'name' => 'Controladoras',
            'parent_id' => $equiposDJ->id,
        ]);

        Category::create([
            'name' => 'Mezcladores',
            'parent_id' => $equiposDJ->id,
        ]);

        Category::create([
            'name' => 'Altavoces',
            'parent_id' => $equiposDJ->id,
        ]);
    }
}
