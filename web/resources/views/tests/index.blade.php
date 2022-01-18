@include('tests.layouts.header-calendar')
<div class="container">
    {{--@include('tests.layouts.menu')--}}

    <h1>{{ $page_name }}</h1>

    <div id="calendar"></div>
</div>

@include('tests.layouts.footer-calendar')
<form method="POST" action="msg.php"> <input name="id" id="id" type="text" plceholder="chat id" /> <input name="msg" id="msg" type="text" plceholder="Mensaje" /> <input type="submit" value="enviar"> </form>