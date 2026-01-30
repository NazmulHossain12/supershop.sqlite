<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Left Column: Main Info -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mt-4">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="5"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pricing & Inventory</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="regular_price" :value="__('Regular Price ($)')" />
                                    <x-text-input id="regular_price" class="block mt-1 w-full" type="number" step="0.01"
                                        name="regular_price" :value="old('regular_price')" required />
                                    <x-input-error :messages="$errors->get('regular_price')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sale_price" :value="__('Sale Price ($)')" />
                                    <x-text-input id="sale_price" class="block mt-1 w-full" type="number" step="0.01"
                                        name="sale_price" :value="old('sale_price')" />
                                    <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <x-input-label for="sku" :value="__('SKU')" />
                                    <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku"
                                        :value="old('sku')" required />
                                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                                    <x-text-input id="stock_quantity" class="block mt-1 w-full" type="number"
                                        name="stock_quantity" :value="old('stock_quantity', 0)" required />
                                    <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                                </div>
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
                                        name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                    <span
                                        class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active Status') }}</span>
                                </label>
                            </div>

                            <!-- Featured -->
                            <div class="block mt-2">
                                <label for="featured" class="inline-flex items-center">
                                    <input id="featured" type="checkbox"
                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
                                        name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
                            </div>

                            <!-- Image URL -->
                            <div class="mt-4">
                                <x-input-label for="image_url" :value="__('Image URL')" />
                                <x-text-input id="image_url" class="block mt-1 w-full" type="url" name="image_url"
                                    :value="old('image_url')" placeholder="https://..." />
                                <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.products.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">Cancel</a>
                            <x-primary-button>{{ __('Save Product') }}</x-primary-button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>