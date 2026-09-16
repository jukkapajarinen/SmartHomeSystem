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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <div class="w-full sm:max-w-md mt-6 bg-gray-100 shadow-sm ring-1 ring-gray-300 overflow-hidden sm:rounded-xl">
                <div class="bg-gray-800 px-6 py-3 text-center">
                    <a href="/" class="text-lg font-medium text-white uppercase tracking-wider">
                        SHS
                    </a>
                </div>

                <div class="px-8 py-8">
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
