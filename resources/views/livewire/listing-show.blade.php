<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">SoundsMarket</h1>
            <flux:button wire:navigate href="{{ $backRoute }}" variant="outline">
                ← Volver
            </flux:button>
        </div>
    </header>


    <div class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Images Section -->
            <div class="lg:col-span-2">
                <div class="bg-gray-900 rounded-lg shadow-md dark:shadow-xl hover:shadow-lg dark:hover:shadow-2xl overflow-hidden">

                    @if ($media->count())
                        <div class="relative aspect-square">
                            <img src="{{ $media[$currentImageIndex]->getUrl() }}" alt="{{ $listing->title }}"
                                class="w-full h-full object-cover" />


                            @if ($media->count() > 1)
                                <button wire:click="previousImage"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-black rounded-full p-2 transition-all">
                                    ❮
                                </button>
                                <button wire:click="nextImage"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-black rounded-full p-2 transition-all">
                                    ❯
                                </button>


                                <div
                                    class="absolute bottom-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-sm">
                                    {{ $currentImageIndex + 1 }} / {{ $media->count() }}
                                </div>
                            @endif
                        </div>


                        @if ($media->count() > 1)
                            <div class="grid grid-cols-6 gap-2 p-4 bg-gray-100 dark:bg-gray-800">
                                @foreach ($media as $index => $photo)
                                    <button wire:click="$set('currentImageIndex', {{ $index }})"
                                        class="aspect-square rounded overflow-hidden border-2 {{ $currentImageIndex === $index ? 'border-blue-500' : 'border-gray-300' }}">
                                        <img src="{{ $photo->getUrl('thumb') }}" alt="Foto {{ $index + 1 }}"
                                            class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="aspect-video flex items-center justify-center text-gray-400 bg-gray-800">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p>Sin imágenes disponibles</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


            <!-- Details Section -->
            <div>
                <div class="mb-6">
                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($listing->price / 100, 2, ',', '.') }}€
                    </p>
                </div>


                <h1 class="text-3xl font-bold mb-4">{{ $listing->title }}</h1>


                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Categoría</p>
                    <p class="text-lg font-semibold">{{ $listing->category->name }}</p>
                </div>


                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Descripción</p>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $listing->description }}
                    </p>
                </div>


                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Vendedor</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center font-semibold">
                            {{ substr($listing->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $listing->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $listing->user->email }}</p>
                        </div>
                    </div>
                </div>


                <div
                    class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400">
                    <p>Publicado: {{ $listing->created_at->format('d/m/Y H:i') }}</p>
                    @if ($listing->updated_at->ne($listing->created_at))
                        <p>Actualizado: {{ $listing->updated_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>

                @if ($isOwner)
                    <div class="flex gap-3">
                        <flux:button wire:navigate href="{{ route('listing.edit', $listing) }}" class="flex-1"
                            variant="primary">
                            Editar
                        </flux:button>
                        <flux:button wire:click="delete"
                            wire:confirm="¿Estás seguro de que quieres eliminar este anuncio?" variant="danger"
                            class="flex-1">
                            Eliminar
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
