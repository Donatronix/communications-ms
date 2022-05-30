@component('mail::message')
##Welcome to {{env('APP_NAME')}}

Dear {{$display_name}},

Verify your email address

Please confirm that you want to use this as your {{ env('APP_NAME')}} account email address. Once it's done you will be able to use {{env('APP_NAME')}}

@component('mail::button', ['url' => $url, 'color' => 'black'])
    Verify Your Email Address
@endcomponent


Thank you for choosing {{ config('app.name') }}.

##The Team at {{ config('app.name') }}

@component('mail::subcopy')
If you’re having trouble clicking the "Verify Your Email Address" button, copy and paste the URL below
into your web browser: [{!! $url !!}]({!! $url !!})
@endcomponent

@endcomponent
