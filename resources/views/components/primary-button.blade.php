<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-slate-900 dark:bg-white border border-transparent rounded-full font-bold text-sm text-white dark:text-slate-900 hover:bg-brand-600 dark:hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all duration-200 shadow-lg shadow-slate-900/20 dark:shadow-white/10 hover:-translate-y-0.5 w-full sm:w-auto']) }}>
    {{ $slot }}
</button>
