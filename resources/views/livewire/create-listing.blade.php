<div class="max-w-4xl mx-auto px-2 sm:px-4 lg:px-8 py-6 space-y-8">
    <div>
        <h1 class="text-3xl font-bold">Subir producto</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Completa toda la información.
        </p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-slate-50 dark:bg-gray-800 shadow-md dark:shadow-xl rounded-lg p-4 sm:p-6 space-y-4">
            <h2 class="text-xl font-semibold mb-4">Información Básica</h2>
            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium mb-2">Título</label>
                <input type="text" id="title" wire:model.live.debounce.500ms="title" required maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                        focus:border-transparent @error('title') border-red-500 @enderror"
                    placeholder="Ej: Controladora DJ Pioneer DDJ-400" />
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium mb-2">Descripción</label>
                <textarea id="description" wire:model.live.debounce.500ms="description" required maxlength="2500" rows="5"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                        focus:border-transparent @error('description') border-red-500 @enderror"
                    placeholder="Describe tu producto en detalle..."></textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Price --}}
            <div>
                <label for="price" class="block text-sm font-medium mb-2">Precio (€)</label>
                <input type="number" id="price" wire:model.live.debounce.500ms="price" required step="0.01"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                        focus:border-transparent @error('price') border-red-500 @enderror"
                    placeholder="0.00" />
                @error('price')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Category --}}
        <div class="bg-slate-50 dark:bg-gray-800 shadow-md dark:shadow-xl rounded-lg p-4 sm:p-6 space-y-4">
            <h2 class="text-xl font-semibold mb-4">Categoría</h2>

            @if ($rootCategories->count())
                {{-- First level --}}
                <div>
                    <label for="category_0" class="block text-sm font-medium mb-3">Selecciona una categoría</label>
                    <select id="category_0" wire:model.live="categoryPath.0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                            dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                            focus:border-transparent">
                        <option value="0">-- Selecciona una categoría --</option>
                        @foreach ($rootCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Dynamic Levels --}}
                @foreach ($categoriesByLevel as $level => $categories)
                    @if ($categories->count())
                        <div>
                            <label for="category_{{ $level + 1 }}" class="block text-sm font-medium mt-3 mb-3">
                                Selecciona una subcategoría (Nivel {{ $level + 2 }})
                            </label>
                            <select id="category_{{ $level + 1 }}" wire:model.live="categoryPath.{{ $level + 1 }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                    dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500
                                    focus:border-transparent">
                                <option value="0">-- Selecciona una opción --</option>
                                @foreach ($categories as $category)
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

        {{-- Drag & drop --}}
        <div class="bg-slate-50 dark:bg-gray-800 shadow-md dark:shadow-xl rounded-lg p-4 sm:p-6  space-y-4">
            <h2 class="text-xl font-semibold mb-4">Imágenes del producto</h2>
            {{-- Hidden input file --}}
            <input id="imageInput" type="file" class="hidden" multiple wire:model.blur="images"
                accept="image/png,image/jpeg,image/jpg,image/webp" />

            {{-- Dropzone --}}
            <label for="imageInput"
                class="block border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                @dragover.prevent="$el.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20')"
                @dragleave.prevent="$el.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20')"
                @drop.prevent="
                    $el.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                    const files = $event.dataTransfer.files;
                    const input = document.getElementById('imageInput');
                    input.files = files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    ">

                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>

                <p class="text-lg font-medium mb-1">Arrastra imágenes aquí o haz clic para seleccionar</p>
                <p class="text-sm text-gray-500">Máximo 6 imágenes, PNG, JPG o WebP (hasta 2MB cada una)</p>
            </label>

            {{-- Validation errors --}}
            @error('images')
                <div class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </div>
            @enderror
            @error('images.*')
                <div class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </div>
            @enderror

            {{-- Invalid files --}}
            @if (!empty($invalidFiles))
                <div class="mt-3 text-sm text-yellow-600 dark:text-yellow-400">
                    <p>Algunos archivos no son imágenes válidas:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($invalidFiles as $fileName)
                            <li>{{ $fileName }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($images) && count($images) > 0)
                <div class="mt-6">
                    <h3 class="font-medium mb-3">Imágenes cargadas ({{ count($images) }}/10)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($images as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image->temporaryUrl() }}" alt="Imagen {{ $index + 1 }}"
                                    class="w-full h-32 object-cover rounded-lg border-2 border-green-300 dark:border-green-700" />
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 rounded-lg">
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                                <div
                                    class="absolute top-1 right-1 bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>


        <div class="flex justify-end gap-3">
            <flux:button type="button" wire:navigate href="{{ route('dashboard') }}" variant="ghost">
                Cancelar
            </flux:button>

            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Crear anuncio</span>
                <span wire:loading>Guardando...</span>
            </flux:button>
        </div>

        @if (session('success'))
            <div
                class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 p-4 rounded-lg border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

    </form>
</div>
