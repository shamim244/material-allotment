<x-mail::message>
# Hello, {{ $workerName }}!

You have a new assignment in your queue.

**Details:**
- You have been assigned **{{ $meterCount }}** new {{ Str::plural('meter', $meterCount) }}.
@if($lotName)
- **Lot:** {{ $lotName }}
@endif

Please log into your dashboard to review and manage your new meters.

<x-mail::button :url="route('worker.dashboard')">
View My Meters
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
