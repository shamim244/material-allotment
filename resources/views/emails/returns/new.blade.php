<x-mail::message>
# New Return Request

A new meter return request has been submitted and requires your attention.

**Details:**
- **Worker:** {{ $returnRequest->worker->name }}
- **Meter:** {{ $returnRequest->meter->serial_number ?? 'Unknown' }}
- **Reason:** {{ $returnRequest->reason }}

Please review this request in the admin dashboard to approve or reject it.

<x-mail::button :url="route('returns.index')">
Review Return Request
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
