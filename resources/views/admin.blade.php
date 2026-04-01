<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Logic As Data | Admin</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        window.LogicAsData = @json($config);
    </script>

    @if(env('LOGIC_PACKAGE_DEV'))
        <link rel="stylesheet" href="http://localhost:5199/resources/css/app.css">
        <script type="module" src="http://localhost:5199/@@vite/client"></script>
        <script type="module" src="http://localhost:5199/resources/js/app.js" defer></script>
    @else
        <link rel="stylesheet" href="{{ asset('vendor/logic-as-data/app.css') }}">
        <script src="{{ asset('vendor/logic-as-data/app.js') }}" defer></script>
    @endif
</head>
<body class="h-full m-0 antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div id="logic-as-data-admin-app" class="h-full"></div>
</body>
</html>
