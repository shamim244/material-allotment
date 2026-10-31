<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('super-admin.companies.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">&larr; Back to Directory</a>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Onboard New Contractor</h1>
                <p class="text-xs text-gray-500">Provision a private workspace and administrator account for a new NCC contracting partner.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('super-admin.companies.store') }}" class="space-y-6">
            @csrf

            <!-- Card 1: Contractor Agency Details -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h2 class="text-base font-bold text-gray-900 border-b pb-2 flex items-center gap-2">
                    <span>🏢</span> Agency Information
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Company / Agency Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Maa Tara Electrical Services"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-300 @enderror">
                        @error('name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">NCC Vendor Code <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. NCC-PAT-05"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-rose-300 @enderror">
                        @error('code')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Contact Person Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="e.g. Rajesh Kumar"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_person') border-rose-300 @enderror">
                        @error('contact_person')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Agency Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="agency@example.com"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-rose-300 @enderror">
                        @error('email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Phone / Mobile</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 9876543210"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Operating Circle / Division / Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Patna Rural Circle, Bihar"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Max Workers Limit</label>
                        <input type="number" name="max_workers" value="{{ old('max_workers', 50) }}" min="1" max="1000" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Max Meters Quota</label>
                        <input type="number" name="max_meters" value="{{ old('max_meters', 100000) }}" min="100" max="1000000" step="100" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Card 2: Contractor Admin Login -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4" x-data="{
                generatePassword() {
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$';
                    let pass = '';
                    for(let i=0; i<10; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
                    this.$refs.passInput.value = pass;
                }
            }">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span>🔑</span> Contractor Administrator Login
                    </h2>
                    <button type="button" @click="generatePassword()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <span>⚡ Generate Random Password</span>
                    </button>
                </div>
                <p class="text-xs text-gray-500">These credentials will allow the contractor to sign in and begin managing their workers and stock.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Admin User Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required placeholder="e.g. Rajesh Verma"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Admin Login Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required placeholder="rajesh.login@example.com"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('admin_email') border-rose-300 @enderror">
                        @error('admin_email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Initial Password <span class="text-rose-500">*</span></label>
                        <input type="text" name="admin_password" x-ref="passInput" value="{{ old('admin_password', 'Contractor@2026') }}" required
                               class="w-full rounded-xl border-gray-200 text-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="text-[11px] text-gray-400 mt-1">This password will be encrypted using two-way AES-256 and will also be visible to the contractor admin upon sign in.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('super-admin.companies.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition">
                    Create Contractor Workspace
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
