<tr>
    <td class="px-4 py-4">
        <div class="text-sm font-medium text-gray-900">{{ $config->label }}</div>
        <div class="text-xs text-gray-400">{{ $config->field_name }}</div>
    </td>
    <td class="px-4 py-4 text-center">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" 
                name="fields[{{ $config->id }}][is_enabled]" 
                value="1"
                {{ $config->is_enabled ? 'checked' : '' }}
                class="sr-only peer">
            <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
        </label>
    </td>
    <td class="px-4 py-4 text-center">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" 
                name="fields[{{ $config->id }}][is_required]" 
                value="1"
                {{ $config->is_required ? 'checked' : '' }}
                class="sr-only peer">
            <div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500"></div>
        </label>
    </td>
</tr>
