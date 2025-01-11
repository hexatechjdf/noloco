<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.include.admin.style')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('style')
</head>

<body>
    <div id="app" class="position-relative">
        @include('layouts.include.admin.navbar', ['nav' => 'app'])
        <main class="ptc-100 pbc-50">
            @yield('content')
        </main>
    </div>
</body>

@include('layouts.include.admin.script')
@stack('script')

</html>
