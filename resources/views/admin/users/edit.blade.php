<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User: ') }} {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 text-black">
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
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter full name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter email address">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-gray-400">(optional)</span></label>
                            <input type="tel" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter mobile number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="worker" {{ old('role', $user->role) === 'worker' ? 'selected' : '' }}>Worker</option>
                                <option value="contractor" {{ old('role', $user->role) === 'contractor' ? 'selected' : '' }}>Contractor (Admin)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="is_active" required
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Security & Password</h3>

                            <!-- View current password -->
                            <div class="mb-4 bg-gray-50 border border-gray-200 rounded-lg p-3 flex justify-between items-center">
                                <div>
                                    <span class="block text-xs text-gray-500 font-semibold uppercase tracking-wider">Current Password</span>
                                    @if($user->plain_password)
                                        <div class="mt-1 flex items-center gap-2">
                                            <input type="password" id="current_plain_password" readonly value="{{ $user->plain_password }}"
                                                   class="bg-transparent border-0 p-0 text-sm font-mono focus:ring-0 w-48 text-gray-800">
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-600 italic mt-1 block">Hashed/Encrypted (Can be reset below)</span>
                                    @endif
                                </div>
                                @if($user->plain_password)
                                    <button type="button" onclick="togglePasswordVisibility()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                        Reveal/Hide
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-gray-400">(leave blank to keep current)</span></label>
                                    <input type="password" name="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter new password">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                Update User Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePasswordVisibility() {
            var passInput = document.getElementById("current_plain_password");
            if (passInput.type === "password") {
                passInput.type = "text";
            } else {
                passInput.type = "password";
            }
        }
    </script>
    @endpush
</x-app-layout>
