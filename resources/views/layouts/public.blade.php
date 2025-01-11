<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.include.public.style')
    @stack('style')
</head>

<body>
    <div class="container-fluid">
        <div class="alertContainer"></div>
        @yield('content')

    </div>
</body>

@include('layouts.include.public.script')
@stack('script')

</html>
