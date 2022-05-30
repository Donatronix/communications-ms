@component('mail::message')
##Reset Password

Dear {{$display_name}},

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => $url, 'color' => 'black'])
    Reset Password
@endcomponent

If you did not request a password reset, no further action is required.

Thank you for choosing {{ config('app.name') }}.

##The Team at {{ config('app.name') }}

@component('mail::subcopy')
If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below
into your web browser: [{!! $url !!}]({!! $url !!})

If you don't use this link within 1 hour, it will expire. To get a new password reset link, visit {!! $reset_link !!}
@endcomponent

@endcomponent
