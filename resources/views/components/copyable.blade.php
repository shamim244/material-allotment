@props(['value', 'label' => ''])
@if($value && $value !== '—')
<span x-data="{ copied: false }" class="inline-flex items-center gap-1 group cursor-pointer"
    @click.stop="
        navigator.clipboard.writeText('{{ $value }}').then(() => { copied = true; setTimeout(() => copied = false, 1500); });
    "
    :title="copied ? 'Copied!' : 'Click to copy'">
    <span class="font-medium">{{ $label ?: $value }}</span>
    <span class="text-gray-400 group-hover:text-indigo-500 transition" x-show="!copied">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
    </span>
    <span class="text-green-500 text-xs font-semibold" x-show="copied" x-transition>✓</span>
</span>
@else
<span class="font-medium text-gray-400">—</span>
@endif
