<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('super-admin.companies.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">&larr; Back to Directory</a>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Edit Contractor: {{ $company->name }}</h1>
                <p class="text-xs text-gray-500">Manage quota limits, operating details, and access status.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('super-admin.companies.impersonate', $company->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition">
                        Login as Contractor
                    </button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('super-admin.companies.update', $company->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Card 1: Agency Settings -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h2 class="text-base font-bold text-gray-900 border-b pb-2 flex items-center gap-2">
                    <span>🏢</span> Agency Information & Quotas
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Company / Agency Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-rose-300 @enderror">
                        @error('name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">NCC Vendor Code</label>
                        <input type="text" name="code" value="{{ old('code', $company->code) }}" placeholder="e.g. NCC-001"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-rose-300 @enderror">
                        @error('code')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Contact Person Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Agency Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Phone / Mobile</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>🟢 Active</option>
                            <option value="trial" {{ old('status', $company->status) === 'trial' ? 'selected' : '' }}>🟡 Trial</option>
                            <option value="suspended" {{ old('status', $company->status) === 'suspended' ? 'selected' : '' }}>🔴 Suspended (Access Blocked)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Operating Circle / Division / Address</label>
                        <input type="text" name="address" value="{{ old('address', $company->address) }}"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Max Workers Limit</label>
                        <input type="number" name="max_workers" value="{{ old('max_workers', $company->max_workers) }}" min="1" max="1000" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Max Meters Quota</label>
                        <input type="number" name="max_meters" value="{{ old('max_meters', $company->max_meters) }}" min="100" max="1000000" step="100" required
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Card 2: Current Administrator Info -->
            @if($primaryContractor)
            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Primary Contractor Administrator</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 block">Name</span>
                        <span class="font-semibold text-gray-800">{{ $primaryContractor->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Login Email</span>
                        <span class="font-semibold text-gray-800">{{ $primaryContractor->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Password Visible to Admin</span>
                        <span class="font-mono text-xs bg-white px-2 py-1 rounded border inline-block mt-0.5 text-gray-700">
                            {{ $primaryContractor->plain_password ?: 'Hashed' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-2">
                <div>
                    @if($company->id !== 1)
                    <button type="button" onclick="if(confirm('Are you sure you want to archive this contractor agency? All records will be safely archived.')) document.getElementById('delete-company-form').submit();"
                            class="px-4 py-2 text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-xl transition">
                        Archive Contractor
                    </button>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.companies.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>

        @if($company->id !== 1)
        <form id="delete-company-form" method="POST" action="{{ route('super-admin.companies.destroy', $company->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        @endif

    </div>
</x-app-layout>
