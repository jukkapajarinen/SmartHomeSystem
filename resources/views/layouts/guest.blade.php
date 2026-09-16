<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('lib/tailwindcss/tailwind.min.css') }}">
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-gray-50 to-gray-100">
            <div>
                <a href="/" class="text-2xl font-bold text-gray-700">
                    SHS
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-sm ring-1 ring-gray-900 ring-opacity-5 overflow-hidden sm:rounded-xl">
                @yield('content')
            </div>
        </div>
    </body>
</html>
