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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white">
            <nav class="bg-gray-800 shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center mr-6">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-bold text-white">
                                    <img src="{{ asset('favicon.ico') }}" alt="" class="w-8 h-8">
                                    SHS
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:flex">
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'inline-flex items-center px-1 pt-1 border-b-4 border-blue-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-400 transition duration-150 ease-in-out' : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-white hover:border-gray-500 focus:outline-none focus:text-white focus:border-gray-500 transition duration-150 ease-in-out' }}">
                                    {{ __('Dashboard') }}
                                </a>
                                <a href="{{ route('sensors.list') }}" class="{{ request()->routeIs('sensors.list') ? 'inline-flex items-center px-1 pt-1 border-b-4 border-blue-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-400 transition duration-150 ease-in-out' : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-white hover:border-gray-500 focus:outline-none focus:text-white focus:border-gray-500 transition duration-150 ease-in-out' }}">
                                    {{ __('Sensors') }} <span class="ml-1 text-gray-500">({{ $sensorCount }})</span>
                                </a>
                                <a href="{{ route('cameras.list') }}" class="{{ request()->routeIs('cameras.list') ? 'inline-flex items-center px-1 pt-1 border-b-4 border-blue-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-400 transition duration-150 ease-in-out' : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-white hover:border-gray-500 focus:outline-none focus:text-white focus:border-gray-500 transition duration-150 ease-in-out' }}">
                                    {{ __('Cameras') }} <span class="ml-1 text-gray-500">({{ $cameraCount }})</span>
                                </a>
                            </div>
                        </div>

                        <!-- User -->
                        <div class="hidden sm:flex sm:-my-px sm:ml-6 space-x-8">
                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'inline-flex items-center px-1 pt-1 border-b-4 border-blue-500 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-400 transition duration-150 ease-in-out' : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-white hover:border-gray-500 focus:outline-none focus:text-white focus:border-gray-500 transition duration-150 ease-in-out' }}">
                                {{ Auth::user()->name }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="flex">
                                @csrf

                                <button type="submit" class="inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-white focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <details class="relative">
                                <summary class="cursor-pointer inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </summary>

                                <!-- Responsive Navigation Menu -->
                                <div class="absolute right-0 z-50 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-gray-900 ring-opacity-5">
                                    <div class="pt-2 pb-3 space-y-1">
                                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out' : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                            {{ __('Dashboard') }}
                                        </a>
                                        <a href="{{ route('sensors.list') }}" class="{{ request()->routeIs('sensors.list') ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out' : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                            {{ __('Sensors') }} <span class="ml-1 text-gray-500">({{ $sensorCount }})</span>
                                        </a>
                                        <a href="{{ route('cameras.list') }}" class="{{ request()->routeIs('cameras.list') ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out' : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                            {{ __('Cameras') }} <span class="ml-1 text-gray-500">({{ $cameraCount }})</span>
                                        </a>
                                    </div>

                                    <!-- Responsive Settings Options -->
                                    <div class="pt-4 pb-1 border-t border-gray-200">
                                        <div class="px-4">
                                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                        </div>

                                        <div class="mt-3 space-y-1">
                                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-left text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out' : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                                {{ __('Profile') }}
                                            </a>

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf

                                                <a href="{{ route('logout') }}"
                                                        onclick="event.preventDefault();
                                                                    this.closest('form').submit();"
                                                        class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                                    {{ __('Log Out') }}
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
