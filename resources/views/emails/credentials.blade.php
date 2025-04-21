@component('mail::message')
    # Hello {{ $name }}!

    We received a request to register your account.

    Your login credentials are:

    ✉️ Email: {{ $email }}
    🔑 Password: {{ $password }}

    If you did not request this, you can safely ignore this email.

    Regards,
    {{ config('app.name') }}
@endcomponent
