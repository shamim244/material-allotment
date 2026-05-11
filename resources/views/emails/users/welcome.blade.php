<x-mail::message>
# Welcome, {{ $user->name }}!

An account has been created for you in the **Material Allotment System**.

Here are your login credentials:
- **Email:** {{ $user->email }}
- **Password:** {{ $password }}

We highly recommend changing your password after your first login.

<x-mail::button :url="route('login')">
Login Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
