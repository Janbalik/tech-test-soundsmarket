<div class="max-w-4xl mx-auto p-6 space-y-8">
    <div>
        <h1 class="text-3xl font-bold">Subir producto</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Completa toda la información.
        </p>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Título --}}
        <div>
            <label for="title" class="block text-sm font-medium mb-2">Título</label>
            <input
                type="text"
                id="title"
                wire:model.live.debounce.500ms="title"
                required
                maxlength="255"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                       dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                       focus:border-transparent @error('title') border-red-500 @enderror"
                placeholder="Ej: Controladora DJ Pioneer DDJ-400"
            />
            @error('title')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Descripción --}}
        <div>
            <label for="description" class="block text-sm font-medium mb-2">Descripción</label>
            <textarea
                id="description"
                wire:model.live.debounce.500ms="description"
                required
                maxlength="2500"
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                       dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                       focus:border-transparent @error('description') border-red-500 @enderror"
                placeholder="Describe tu producto en detalle..."
            ></textarea>
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Precio --}}
        <div>
            <label for="price" class="block text-sm font-medium mb-2">Precio (€)</label>
            <input
                type="number"
                id="price"
                wire:model.live.debounce.500ms="price"
                required
                step="0.01"
                min="1"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                       dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                       focus:border-transparent @error('price') border-red-500 @enderror"
                placeholder="0.00"
            />
            @error('price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Categoría jerárquica --}}
        <div>
            <h2 class="text-xl font-semibold mb-4">Categoría</h2>

            @if($rootCategories->count())
                {{-- Primer nivel --}}
                <div>
                    <label for="category_0" class="block text-sm font-medium mb-3">Selecciona una categoría</label>
                    <select
                        id="category_0"
                        wire:model.live="categoryPath.0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                            dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                            focus:border-transparent"
                    >
                        <option value="0">-- Selecciona una categoría --</option>
                        @foreach($rootCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Niveles dinámicos --}}
                @foreach($categoriesByLevel as $level => $categories)
                    @if($categories->count())
                        <div>
                            <label for="category_{{ $level + 1 }}" class="block text-sm font-medium mt-3 mb-3">
                                Selecciona una subcategoría (Nivel {{ $level + 2 }})
                            </label>
                            <select
                                id="category_{{ $level + 1 }}"
                                wire:model.live="categoryPath.{{ $level + 1 }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                    dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                                    focus:border-transparent"
                            >
                                <option value="0">-- Selecciona una opción --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endforeach

                @error('category_id')
                    <div class="mt-4 text-red-500 text-sm">
                        {{ $message }}
                    </div>
                @enderror
            @else
                <p class="text-gray-500">No hay categorías disponibles</p>
            @endif
        </div>

        {{-- Imágenes --}}
        <div>
            <label for="images" class="block text-sm font-medium mb-2">Imágenes del producto</label>
            <input
                type="file"
                id="images"
                wire:model="images"
                accept="image/png,image/jpeg,image/jpg,image/webp"
                multiple
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg
                       cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600
                       dark:placeholder-gray-400 focus:outline-none"
            />
            @error('images')
                <div class="mt-2 text-red-500 text-sm">{{ $message }}</div>
            @enderror
            @error('images.*')
                <div class="mt-2 text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex justify-end gap-3">
            <flux:button
                type="button"
                wire:navigate
                href="{{ route('dashboard') }}"
                variant="ghost"
            >
                Cancelar
            </flux:button>

            <flux:button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Crear anuncio</span>
                <span wire:loading>Guardando...</span>
            </flux:button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 p-4 rounded-lg border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif
        
    </form>
</div>
