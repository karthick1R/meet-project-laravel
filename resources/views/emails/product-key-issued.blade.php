@component('mail::message')
# Welcome to Meeting Room Booking System

Hello {{ $productKey->email }},

Thank you for registering with Meeting Room Booking System.

## Your Product Key

`{{ $productKey->product_key }}`

Keep this key safe—it's tied to your organization and will be required if you ever need to reactivate access.

## Complete Your Registration

Click the button below if you still need to finish setting up your account:

@component('mail::button', ['url' => $registrationUrl])
Complete Registration
@endcomponent

Or copy and paste this link into your browser:
{{ $registrationUrl }}

If you've already completed registration, jump straight to your dashboard:

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Login Now
@endcomponent

Need help? Just reply to this email and we'll get back to you shortly.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
