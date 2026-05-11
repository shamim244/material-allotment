@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200/50 dark:border-slate-700/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-md text-slate-900 dark:text-white focus:bg-white/70 dark:focus:bg-slate-900/70 focus:border-brand-500 focus:ring-brand-500 rounded-xl shadow-inner transition-all duration-200']) }}>
