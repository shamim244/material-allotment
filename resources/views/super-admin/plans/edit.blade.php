<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Edit Subscription Tier: ') }} {{ $plan->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Update tier quotas, features, and multi-cycle prices.
                </p>
            </div>
            <a href="{{ route('super-admin.plans.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Plans
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

            <form method="POST" action="{{ route('super-admin.plans.update', $plan) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- BASIC DETAILS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="name" :value="__('Tier Name')" />
                        <x-text-input id="name" class="block mt-1 w-full text-sm" type="text" name="name" :value="old('name', $plan->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="slug" :value="__('Slug Identifier')" />
                        <x-text-input id="slug" class="block mt-1 w-full text-sm font-mono" type="text" name="slug" :value="old('slug', $plan->slug)" required />
                        <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="badge" :value="__('Highlight Badge (Optional)')" />
                        <x-text-input id="badge" class="block mt-1 w-full text-sm" type="text" name="badge" :value="old('badge', $plan->badge)" />
                        <x-input-error :messages="$errors->get('badge')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Plan Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $plan->status) === 'active' ? 'selected' : '' }}>Active Public</option>
                            <option value="promo" {{ old('status', $plan->status) === 'promo' ? 'selected' : '' }}>Promo / Special</option>
                            <option value="archived" {{ old('status', $plan->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" :value="__('Short Description')" />
                    <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $plan->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                {{-- QUOTAS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <x-input-label for="max_meters" :value="__('Max Meters Quota')" />
                        <x-text-input id="max_meters" class="block mt-1 w-full text-sm" type="number" name="max_meters" :value="old('max_meters', $plan->max_meters)" required min="100" />
                        <x-input-error :messages="$errors->get('max_meters')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="max_workers" :value="__('Max Workers Quota')" />
                        <x-text-input id="max_workers" class="block mt-1 w-full text-sm" type="number" name="max_workers" :value="old('max_workers', $plan->max_workers)" required min="1" />
                        <x-input-error :messages="$errors->get('max_workers')" class="mt-1" />
                    </div>
                </div>

                {{-- MULTI-CYCLE PRICING GRID --}}
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Multi-Cycle Pricing</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="price_1m" :value="__('1 Month (30 Days)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₹</span>
                                <x-text-input id="price_1m" class="block pl-7 w-full text-sm font-semibold" type="number" step="0.01" name="price_1m" :value="old('price_1m', $plan->price_1m)" required />
                            </div>
                            <x-input-error :messages="$errors->get('price_1m')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="price_3m" :value="__('3 Months (90 Days)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₹</span>
                                <x-text-input id="price_3m" class="block pl-7 w-full text-sm font-semibold" type="number" step="0.01" name="price_3m" :value="old('price_3m', $plan->price_3m)" required />
                            </div>
                            <x-input-error :messages="$errors->get('price_3m')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="price_6m" :value="__('6 Months (180 Days)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₹</span>
                                <x-text-input id="price_6m" class="block pl-7 w-full text-sm font-semibold" type="number" step="0.01" name="price_6m" :value="old('price_6m', $plan->price_6m)" required />
                            </div>
                            <x-input-error :messages="$errors->get('price_6m')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="price_1y" :value="__('1 Year (365 Days)')" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₹</span>
                                <x-text-input id="price_1y" class="block pl-7 w-full text-sm font-semibold text-emerald-700" type="number" step="0.01" name="price_1y" :value="old('price_1y', $plan->price_1y)" required />
                            </div>
                            <x-input-error :messages="$errors->get('price_1y')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- FEATURES LIST --}}
                <div class="pt-4 border-t border-gray-100">
                    <x-input-label for="features" :value="__('Included Feature Highlights (One per line)')" />
                    <textarea id="features" name="features" rows="5" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-xs font-mono focus:border-indigo-500 focus:ring-indigo-500">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                    <x-input-error :messages="$errors->get('features')" class="mt-1" />
                </div>

                {{-- SORT ORDER & FEATURED --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <x-input-label for="sort_order" :value="__('Display Sort Order')" />
                        <x-text-input id="sort_order" class="block mt-1 w-full text-sm" type="number" name="sort_order" :value="old('sort_order', $plan->sort_order)" />
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-700">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span>Featured (Highlighted on pricing cards)</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('super-admin.plans.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Update Plan Tier
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
