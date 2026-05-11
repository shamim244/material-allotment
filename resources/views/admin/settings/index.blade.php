<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-black leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <!-- Very light off-white background with super subtle shapes to make the glass effect visible -->
    <div class="py-12 bg-[#f8fafc] min-h-screen relative overflow-hidden text-black">
        
        <!-- Abstract Background for Glass effect to refract -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-50 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-slate-100 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            
            @if (session('success'))
                <div class="p-4 flex items-center gap-3 text-sm text-black rounded-2xl bg-white/60 backdrop-blur-xl border border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)]" role="alert">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 flex items-center gap-3 text-sm text-black rounded-2xl bg-white/60 backdrop-blur-xl border border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)]" role="alert">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8" x-data="{ activeTab: 'connections' }" x-cloak>
                
                <!-- Sidebar Menu -->
                <div class="lg:w-1/4 flex-shrink-0">
                    <div class="sticky top-8 space-y-2">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-4 mb-4 mt-2">Settings Menu</h3>
                        
                        <button @click="activeTab = 'connections'" :class="{'bg-white/60 border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] text-black': activeTab === 'connections', 'border-transparent text-gray-500 hover:bg-white/40 hover:text-black': activeTab !== 'connections'}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border backdrop-blur-sm transition-all text-sm font-bold text-left focus:outline-none">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Email Connections
                        </button>
                        
                        <button @click="activeTab = 'identity'" :class="{'bg-white/60 border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] text-black': activeTab === 'identity', 'border-transparent text-gray-500 hover:bg-white/40 hover:text-black': activeTab !== 'identity'}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border backdrop-blur-sm transition-all text-sm font-bold text-left focus:outline-none">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Global Identity
                        </button>

                        <button @click="activeTab = 'events'" :class="{'bg-white/60 border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] text-black': activeTab === 'events', 'border-transparent text-gray-500 hover:bg-white/40 hover:text-black': activeTab !== 'events'}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border backdrop-blur-sm transition-all text-sm font-bold text-left focus:outline-none">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            Event Toggles
                        </button>

                        <button @click="activeTab = 'testing'" :class="{'bg-white/60 border-white/80 shadow-[0_4px_20px_rgb(0,0,0,0.03)] text-black': activeTab === 'testing', 'border-transparent text-gray-500 hover:bg-white/40 hover:text-black': activeTab !== 'testing'}" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border backdrop-blur-sm transition-all text-sm font-bold text-left focus:outline-none mt-4">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verify & Test
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:w-3/4">
                    <form method="post" action="{{ route('admin.settings.update') }}">
                        @csrf
                        
                        <!-- TAB: Connections -->
                        <div x-show="activeTab === 'connections'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-8 sm:p-10 mb-6">
                                <div class="mb-10">
                                    <h2 class="text-2xl font-bold text-black tracking-tight flex items-center gap-3">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ __('Email Connections') }}
                                    </h2>
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ __("Configure exactly how the application transmits transactional emails to your users.") }}
                                    </p>
                                </div>

                                <!-- Mailer Selection -->
                                <div class="mb-8">
                                    <label for="mail_mailer" class="block text-sm font-medium text-black mb-1">{{ __('Primary Connection Provider') }}</label>
                                    <select id="mail_mailer" name="mail_mailer" class="block w-full max-w-md rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" required>
                                        <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP Server (Traditional)</option>
                                        <option value="postmark" {{ ($settings['mail_mailer'] ?? '') == 'postmark' ? 'selected' : '' }}>Postmark API</option>
                                        <option value="mailgun" {{ ($settings['mail_mailer'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun API</option>
                                        <option value="ses" {{ ($settings['mail_mailer'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES API</option>
                                        <option value="log" {{ ($settings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>Local Log (Testing Mode)</option>
                                    </select>
                                </div>

                                <div class="border-t border-gray-200/50 pt-8" id="provider-fields-container">
                                    
                                    <!-- SMTP Section -->
                                    <div id="section-smtp" class="provider-section hidden space-y-8">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div>
                                                <label for="mail_host" class="block text-sm font-medium text-black mb-1">{{ __('SMTP Host') }}</label>
                                                <input id="mail_host" name="mail_host" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" placeholder="smtp.example.com" />
                                            </div>
                                            <div>
                                                <label for="mail_port" class="block text-sm font-medium text-black mb-1">{{ __('Port') }}</label>
                                                <input id="mail_port" name="mail_port" type="number" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" placeholder="587" />
                                            </div>
                                            <div>
                                                <label for="mail_encryption" class="block text-sm font-medium text-black mb-1">{{ __('Encryption') }}</label>
                                                <select id="mail_encryption" name="mail_encryption" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5">
                                                    <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="md:col-span-2">
                                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest">{{ __('SMTP Authentication') }}</h3>
                                            </div>
                                            <div>
                                                <label for="mail_username" class="block text-sm font-medium text-black mb-1">{{ __('Username / Login') }}</label>
                                                <input id="mail_username" name="mail_username" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" />
                                            </div>
                                            <div>
                                                <label for="mail_password" class="block text-sm font-medium text-black mb-1">{{ __('Password') }}</label>
                                                <input id="mail_password" name="mail_password" type="password" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" placeholder="Leave blank to keep unchanged" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Postmark Section -->
                                    <div id="section-postmark" class="provider-section hidden space-y-8">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="md:col-span-2">
                                                <label for="postmark_token" class="block text-sm font-medium text-black mb-1">{{ __('Postmark Server API Token') }}</label>
                                                <input id="postmark_token" name="postmark_token" type="password" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('postmark_token', $settings['postmark_token'] ?? '') }}" placeholder="••••••••••••••••" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mailgun Section -->
                                    <div id="section-mailgun" class="provider-section hidden space-y-8">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="mailgun_domain" class="block text-sm font-medium text-black mb-1">{{ __('Mailgun Domain') }}</label>
                                                <input id="mailgun_domain" name="mailgun_domain" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mailgun_domain', $settings['mailgun_domain'] ?? '') }}" placeholder="mg.example.com" />
                                            </div>
                                            <div>
                                                <label for="mailgun_secret" class="block text-sm font-medium text-black mb-1">{{ __('Mailgun Private API Key') }}</label>
                                                <input id="mailgun_secret" name="mailgun_secret" type="password" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mailgun_secret', $settings['mailgun_secret'] ?? '') }}" placeholder="key-••••••••••••••••" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SES Section -->
                                    <div id="section-ses" class="provider-section hidden space-y-8">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="ses_key" class="block text-sm font-medium text-black mb-1">{{ __('AWS Access Key ID') }}</label>
                                                <input id="ses_key" name="ses_key" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('ses_key', $settings['ses_key'] ?? '') }}" placeholder="AKIAIOSFODNN7EXAMPLE" />
                                            </div>
                                            <div>
                                                <label for="ses_secret" class="block text-sm font-medium text-black mb-1">{{ __('AWS Secret Access Key') }}</label>
                                                <input id="ses_secret" name="ses_secret" type="password" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('ses_secret', $settings['ses_secret'] ?? '') }}" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY" />
                                            </div>
                                            <div>
                                                <label for="ses_region" class="block text-sm font-medium text-black mb-1">{{ __('AWS Region') }}</label>
                                                <input id="ses_region" name="ses_region" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('ses_region', $settings['ses_region'] ?? 'us-east-1') }}" placeholder="us-east-1" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Log Section -->
                                    <div id="section-log" class="provider-section hidden pt-4">
                                        <div class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200/50 text-amber-900 shadow-sm flex items-start gap-4">
                                            <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <div>
                                                <h4 class="font-bold text-amber-900">Testing Mode Active</h4>
                                                <p class="text-sm text-amber-800 mt-1">Real emails will not be dispatched to external users. The application will render and dump the raw HTML content of the emails securely into <code class="bg-amber-100 px-1.5 py-0.5 rounded text-amber-900 border border-amber-200">storage/logs/laravel.log</code>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end pt-8">
                                    <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 rounded-xl font-semibold text-white hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-600/30">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Identity -->
                        <div x-show="activeTab === 'identity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-8 sm:p-10 mb-6">
                                <div class="mb-10">
                                    <h2 class="text-2xl font-bold text-black tracking-tight flex items-center gap-3">
                                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ __('Global Identity') }}
                                    </h2>
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ __("Configure the default sender details that appear in user inboxes.") }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="mail_from_address" class="block text-sm font-medium text-black mb-1">{{ __('Global From Address') }}</label>
                                        <input id="mail_from_address" name="mail_from_address" type="email" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" required placeholder="system@example.com" />
                                    </div>
                                    <div>
                                        <label for="mail_from_name" class="block text-sm font-medium text-black mb-1">{{ __('Global From Name') }}</label>
                                        <input id="mail_from_name" name="mail_from_name" type="text" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" required placeholder="System Delivery" />
                                    </div>
                                </div>
                                <div class="flex items-center justify-end pt-8">
                                    <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-emerald-600 rounded-xl font-semibold text-white hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-600/30">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Event Toggles -->
                        <div x-show="activeTab === 'events'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-8 sm:p-10 mb-6">
                                <div class="mb-10">
                                    <h2 class="text-2xl font-bold text-black tracking-tight flex items-center gap-3">
                                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        {{ __('Event Toggles') }}
                                    </h2>
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ __("Select which automatic emails the system should actively send.") }}
                                    </p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Toggle 1 -->
                                    <label class="relative flex items-center justify-between p-5 rounded-2xl border border-gray-200 bg-white/50 backdrop-blur-sm cursor-pointer hover:bg-white/80 transition-colors">
                                        <div class="flex flex-col pr-4">
                                            <span class="text-sm font-bold text-black">Welcome Emails</span>
                                            <span class="text-xs text-gray-500 mt-1">Sent when a worker is created</span>
                                        </div>
                                        <div class="relative inline-flex shrink-0 items-center">
                                            <input type="checkbox" name="email_welcome_enabled" class="sr-only peer" {{ ($settings['email_welcome_enabled'] ?? 'true') === 'true' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                        </div>
                                    </label>

                                    <!-- Toggle 2 -->
                                    <label class="relative flex items-center justify-between p-5 rounded-2xl border border-gray-200 bg-white/50 backdrop-blur-sm cursor-pointer hover:bg-white/80 transition-colors">
                                        <div class="flex flex-col pr-4">
                                            <span class="text-sm font-bold text-black">Assignment Alerts</span>
                                            <span class="text-xs text-gray-500 mt-1">Sent for new meter assignments</span>
                                        </div>
                                        <div class="relative inline-flex shrink-0 items-center">
                                            <input type="checkbox" name="email_assignment_enabled" class="sr-only peer" {{ ($settings['email_assignment_enabled'] ?? 'true') === 'true' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                        </div>
                                    </label>

                                    <!-- Toggle 3 -->
                                    <label class="relative flex items-center justify-between p-5 rounded-2xl border border-gray-200 bg-white/50 backdrop-blur-sm cursor-pointer hover:bg-white/80 transition-colors">
                                        <div class="flex flex-col pr-4">
                                            <span class="text-sm font-bold text-black">Return Requests</span>
                                            <span class="text-xs text-gray-500 mt-1">Sent to admins upon request</span>
                                        </div>
                                        <div class="relative inline-flex shrink-0 items-center">
                                            <input type="checkbox" name="email_return_enabled" class="sr-only peer" {{ ($settings['email_return_enabled'] ?? 'true') === 'true' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-end pt-8">
                                    <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-amber-500 rounded-xl font-semibold text-white hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>

                    <!-- TAB: Validate & Test (Separate Form) -->
                    <div x-show="activeTab === 'testing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Send Test Email Card -->
                            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-8">
                                <h3 class="text-xl font-bold text-black mb-3">
                                    {{ __('Verify Settings') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-8">
                                    {{ __('Ensure your configuration is correct before leaving this page by sending a test email.') }}
                                </p>

                                <form method="post" action="{{ route('admin.settings.test-email') }}" class="space-y-6">
                                    @csrf

                                    <div>
                                        <label for="test_email" class="block text-sm font-medium text-black mb-1">{{ __('Recipient Email') }}</label>
                                        <input id="test_email" name="test_email" type="email" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-black shadow-sm transition-colors py-2.5" required placeholder="admin@example.com" />
                                        <x-input-error class="mt-2" :messages="$errors->get('test_email')" />
                                    </div>

                                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-black rounded-xl font-semibold text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10">
                                        {{ __('Send Test Email') }}
                                    </button>
                                </form>
                            </div>

                            <!-- Helpful Tips Card -->
                            <div class="bg-white/30 backdrop-blur-xl border border-white/60 shadow-[0_4px_20px_rgb(0,0,0,0.02)] sm:rounded-3xl p-8 h-fit">
                                <h4 class="font-bold text-black mb-4">
                                    Integration Notes
                                </h4>
                                <ul class="space-y-4 text-sm text-gray-600">
                                    <li>&bull; We recommend dedicated services like Postmark or Mailgun for higher deliverability.</li>
                                    <li>&bull; For Gmail, use an "App Password" to bypass 2FA restrictions.</li>
                                    <li>&bull; Configuration changes apply to the next outgoing background job queue. Clear the cache if using octane.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Scripts for dynamic form -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const select = document.getElementById('mail_mailer');
                            const sections = document.querySelectorAll('.provider-section');
                            
                            function renderUI() {
                                sections.forEach(el => el.classList.add('hidden'));
                                const active = document.getElementById('section-' + select.value);
                                if (active) active.classList.remove('hidden');
                            }
                            
                            if (select) {
                                select.addEventListener('change', renderUI);
                                renderUI();
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
