@component('mail::message')
# Your Meeting Room Access

Hi {{ $user->name }},

A super admin just created an account for you in the Meeting Room Booking System.

## Login Details
- **Email:** {{ $user->email }}
- **Temporary Password:** `{{ $plainPassword }}`

Please log in using the button below and change your password right away.

@component('mail::button', ['url' => $loginUrl])
Login to Meeting Room
@endcomponent

If the button doesn't work, copy and paste this link into your browser:
{{ $loginUrl }}

Need help? Reply to this email and we'll assist you.

Thanks,<br>
{{ config('app.name') }}
@endcomponent

