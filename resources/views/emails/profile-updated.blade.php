@component('mail::message')
# Your Profile Was Updated

Hi {{ $user->name }},

Your account details on **{{ config('app.name') }}** have just been updated.

## Updated Information
- **New Name:** {{ $user->name }}
- **New Email:** {{ $user->email }}

@if($originalEmail !== $user->email)
> Your email address changed from **{{ $originalEmail }}** to **{{ $user->email }}**.
@endif

If you changed your password, it is now active. If you did **not** make these changes, please reset your password immediately and contact an administrator.

You can log in here:

@component('mail::button', ['url' => route('login')])
Go to Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent


