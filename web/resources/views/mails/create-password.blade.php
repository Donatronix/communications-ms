@component('mail::message')
##Welcome to {{env('APP_NAME')}}

Dear {{$display_name}},

If you want to create a password for your account please press a button below:

@component('mail::button', ['url' => $url, 'color' => 'black'])
    Create Password
@endcomponent

Thank you for choosing {{ config('app.name') }}.

##The Team at {{ config('app.name') }}

@component('mail::subcopy')
If you’re having trouble clicking the "Create Password" button, copy and paste the URL below
into your web browser: [{!! $url !!}]({!! $url !!})

If you don't use this link within 1 hour, it will expire. To get a new password create link, visit {!! $reset_link !!}
@endcomponent

@endcomponent
