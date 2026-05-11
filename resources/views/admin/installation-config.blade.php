<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Installation Field Settings') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <p class="text-sm text-gray-500 mb-6">
                        Configure which fields are shown and required for NSC and Replacement installations.
                    </p>

                    <form method="POST" action="{{ route('admin.installation-config.update') }}">
                        @csrf
                        @method('PATCH')

                        @php
                            $nscFields = $configs->filter(fn($c) => in_array($c->install_type, ['nsc', 'shared']));
                            $replacementFields = $configs->filter(fn($c) => in_array($c->install_type, ['replacement', 'shared']));
                            $sharedFields = $configs->where('install_type', 'shared');
                            $nscOnly = $configs->where('install_type', 'nsc');
                            $replacementOnly = $configs->where('install_type', 'replacement');
                        @endphp

                        <!-- NSC Section -->
                        <div class="mb-8">
                            <h3 class="text-sm font-bold text-indigo-700 uppercase tracking-wide mb-3 flex items-center">
                                <span class="px-2 py-1 bg-indigo-100 rounded-full mr-2">🆕</span> NSC Fields
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-indigo-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Enabled</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Required</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($nscOnly as $config)
                                            @include('admin.partials._field-config-row', ['config' => $config])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Replacement Section -->
                        <div class="mb-8">
                            <h3 class="text-sm font-bold text-orange-700 uppercase tracking-wide mb-3 flex items-center">
                                <span class="px-2 py-1 bg-orange-100 rounded-full mr-2">🔄</span> Replacement Fields
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-orange-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Enabled</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Required</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($replacementOnly as $config)
                                            @include('admin.partials._field-config-row', ['config' => $config])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Shared Section -->
                        <div class="mb-8">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center">
                                <span class="px-2 py-1 bg-gray-100 rounded-full mr-2">📋</span> Shared Fields (Both Types)
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Enabled</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Required</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sharedFields as $config)
                                            @include('admin.partials._field-config-row', ['config' => $config])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Configuration
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
