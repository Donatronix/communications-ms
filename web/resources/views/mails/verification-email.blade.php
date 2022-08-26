<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ULTAINFINITY GLOBAL</title>

    <style>
        .flex-container>ul {
            display: flex;
            justify-content: center;
            list-style-type: none;
        }

        .flex-container>ul>li>a>img {

            margin: 10px;
            text-align: center;
            justify-content: center;
            line-height: 75px;
            font-size: 30px;
        }
    </style>
</head>

<body style=" background: #F9FCFF; font-family: poppins, sans-serif; width:100%; overflow-x: hidden; text-align: justify;">
    <div class="container-fluid" style="background-color: #000000; background-size:cover; padding: 20px; z-index: 5;">

    </div>
    <!-- <img src="./Untitled-3.png"  class="" alt="top-side" style="text-align:end;"> -->
    <section style=" background-color: white;  padding: 30px;   background-repeat: no-repeat;
    background-size: 100% 100%;">

        <div class="container">
            <nav style="margin-bottom:6em ;">

                <a>
                    <img src="/assets/Frame.svg" alt="">
                </a>

            </nav>

            <div class="container">
                <h1 style="margin-top: 2em; font-style: normal;  font-weight:600; font-size:25px;  line-height: 45px;">
                    Dear {{$display_name}},</h1>
                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">Verify your email address.</p>
                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">
                    Please confirm that you want to use this as your {{ env('APP_NAME')}} account email address. Once it's done you will be able to use {{env('APP_NAME')}}.</p>

                @component('mail::button', ['url' => $url, 'color' => 'black'])
                Verify Your Email Address
                @endcomponent

                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">Thank you for choosing {{ config('app.name') }}.</p>
                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">The Team at {{ config('app.name') }}
                </p>
                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">If you’re having trouble clicking the "Verify Your Email Address" button, copy and paste the URL below
                    into your web browser
                </p>
                <p style="margin-top:1em; font-style: normal; font-weight: normal; font-size:16px; line-height: 27px; color:#000000">[{!! $url !!}]({!! $url !!})
                </p>

            </div>
        </div>
    </section>
    <footer>
        <div class="container-fluid" style="background-color: #000000; background-size:cover; padding: 20px;">

        </div>
        <!-- <img src="./Untitled-4.png" alt="top-side" style="position: relative;"> -->
    </footer>
</body>

</html>