@component('mail::message')
##Welcome to {{env('APP_NAME')}}

Dear {{$display_name}},

Thank you for choosing {{ config('app.name') }}.

##The Team at {{ config('app.name') }}

@component('mail::subcopy')
    You have a new referral registered and you are credited with {{$points}} points
@endcomponent

@endcomponent
