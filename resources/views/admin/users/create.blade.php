<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter full name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter email address">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-gray-400">(optional)</span></label>
                            <input type="tel" name="mobile" value="{{ old('mobile') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter mobile number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="worker" {{ old('role') === 'worker' ? 'selected' : '' }}>Worker</option>
                                <option value="contractor" {{ old('role') === 'contractor' ? 'selected' : '' }}>Contractor (Admin)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter password">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Confirm password">
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-xs text-blue-700">
                                <strong>Note:</strong> Users created by admin are immediately active and can login right away.
                            </p>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                            Create User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
