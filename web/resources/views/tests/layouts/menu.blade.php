<ul class="nav">
    @foreach ($urls as $url)
        @if (preg_match('/^h2(.+)/',$url,$m))
            <li class="nav-item" class="h2">{{ $m[1] }}</li>
        @elseif (preg_match('/^h3(.+)/',$url,$m))
            <li class="nav-item" class="h3">{{ $m[1] }}</li>
        @elseif ($url == '-')
            <li class="nav-item" class="space"></li>
        @else
            <li class="nav-item"><a href="{{ $url }}">{{ $url }}</a></li>
        @endif
    @endforeach
</ul>
