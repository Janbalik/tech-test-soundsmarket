<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <h1 class="text-2xl font-bold">Productos disponibles</h1>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <flux:heading level="1">Todos los Productos</flux:heading>
        </div>

        <div class="mb-6">
            <flux:input wire:model.live.debounce-300ms="search" label="Buscar productos"
                placeholder="Escribe lo que buscas" type="search" />
        </div>

        @if ($listings->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($listings as $listing)
                    <a href="{{ route('listing.show', $listing) }}" class="group block" wire:navigate>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-xl hover:shadow-lg dark:hover:shadow-2xl transition-shadow overflow-hidden h-full flex flex-col">
                            <!-- Image -->
                            <div class="relative bg-gray-200 dark:bg-gray-700 aspect-video overflow-hidden">
                                @if ($listing->getFirstMedia('images'))
                                    <img src="{{ $listing->getFirstMediaUrl('images') }}" alt="{{ $listing->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-end p-2">
                                        <span class="text-white text-xs font-medium bg-black/60 px-2 py-1 rounded">Ver
                                            detalles →</span>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex-1 flex flex-col">
                                <h3
                                    class="font-semibold text-lg truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $listing->title }}
                                </h3>

                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 my-2">
                                    {{ number_format($listing->price / 100, 2, ',', '.') }}€
                                </p>

                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                    {{ $listing->description }}
                                </p>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $listing->user->name }}</p>

                                <!-- Footer -->
                                <div
                                    class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">{{ $listing->category->name }}</span>
                                    <span>{{ $listing->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $listings->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 mb-4 text-lg">
                    @if ($search)
                        No hay productos que coincidan con tu búsqueda
                    @else
                        Aún no hay productos. ¡Sé el primero en subir uno!
                    @endif
                </p>
                <flux:button wire:navigate href="{{ route('listings.create') }}" variant="primary">
                    ➕ Subir primer producto
                </flux:button>
            </div>
        @endif
    </div>
</div>
