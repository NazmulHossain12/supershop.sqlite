<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product') }}: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Left Column: Main Info -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name', $product->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mt-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="5"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pricing & Inventory</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="regular_price" :value="__('Regular Price ($)')" />
                                    <x-text-input id="regular_price" class="block mt-1 w-full" type="number" step="0.01"
                                        name="regular_price" :value="old('regular_price', $product->regular_price)"
                                        required />
                                    <x-input-error :messages="$errors->get('regular_price')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sale_price" :value="__('Sale Price ($)')" />
                                    <x-text-input id="sale_price" class="block mt-1 w-full" type="number" step="0.01"
                                        name="sale_price" :value="old('sale_price', $product->sale_price)" />
                                    <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="vat_rate" :value="__('VAT Rate (%) - Optional')" />
                                <x-text-input id="vat_rate" class="block mt-1 w-full" type="number" step="0.01"
                                    name="vat_rate" :value="old('vat_rate', $product->vat_rate)" />
                                <p class="mt-1 text-xs text-gray-500">Value Added Tax percentage for this product.</p>
                                <x-input-error :messages="$errors->get('vat_rate')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <x-input-label for="sku" :value="__('SKU')" />
                                    <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku"
                                        :value="old('sku', $product->sku)" required />
                                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                                    <x-text-input id="stock_quantity" class="block mt-1 w-full" type="number"
                                        name="stock_quantity" :value="old('stock_quantity', $product->stock_quantity)"
                                        required />
                                    <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Current Images -->
                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Current Images</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @forelse($product->images as $image)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="Product Image" class="w-full h-32 object-cover rounded-lg border dark:border-gray-700">
                                        <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                            <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        @if($image->is_primary)
                                            <span class="absolute top-2 left-2 bg-primary-600 text-white text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">Primary</span>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-sm col-span-full">No images uploaded yet.</p>
                                @endforelse
                                
                                {{-- Fallback for old URL image --}}
                                @if($product->image_url && $product->images->isEmpty())
                                    <div class="relative">
                                        <img src="{{ $product->image_url }}" alt="Old Product Image" class="w-full h-32 object-cover rounded-lg border dark:border-gray-700">
                                        <span class="absolute top-2 left-2 bg-yellow-600 text-white text-[10px] px-2 py-0.5 rounded-full uppercase font-bold text-center">External URL</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Organization</h3>

                            <!-- Status -->
                            <div class="block">
                                <label for="status" class="inline-flex items-center">
                                    <input id="status" type="checkbox"
                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
                                        name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                    <span
                                        class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active Status') }}</span>
                                </label>
                            </div>

                            <!-- Featured -->
                            <div class="block mt-2">
                                <label for="featured" class="inline-flex items-center">
                                    <input id="featured" type="checkbox"
                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
                                        name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                    <span
                                        class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Featured Product') }}</span>
                                </label>
                            </div>

                            <!-- Category -->
                            <div class="mt-4">
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <!-- Brand -->
                            <div class="mt-4">
                                <x-input-label for="brand_id" :value="__('Brand')" />
                                <select id="brand_id" name="brand_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">None</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
                            </div>

                            <!-- Product Images -->
                            <div class="mt-4">
                                <x-input-label for="images" :value="__('Add Images (Max 6 total)')" />
                                <input id="images" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="images[]" multiple accept="image/*" />
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                    <p>• Current total: {{ $product->images->count() }}/6</p>
                                    <p>• Max file size: 2MB per image.</p>
                                    <p>• Recommended: 800x800px (1:1 Ratio).</p>
                                    <p>• Supported formats: JPEG, PNG, JPG, WEBP.</p>
                                </div>
                                <x-input-error :messages="$errors->get('images')" class="mt-2" />
                                <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.products.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">Cancel</a>
                            <x-primary-button>{{ __('Update Product') }}</x-primary-button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>