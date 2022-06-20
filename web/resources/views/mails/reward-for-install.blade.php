@component('mail::message')
##Welcome to {{env('APP_NAME')}}

Dear {{$display_name}},

Thank you for install our application {{ config('app.name') }}.

You were awarded {{$points}} points for installing the application

##The Team at {{ config('app.name') }}
@endcomponent
