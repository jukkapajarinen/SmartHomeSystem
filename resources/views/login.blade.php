@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h2 class="text-base font-semibold text-gray-900 mb-6">{{ __('Sign in') }}</h2>

    <!-- Session Status -->
    @if (session('status'))
        <div class="font-medium text-sm text-green-600 mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <label for="username" class="block font-medium text-sm text-gray-700">{{ __('Email or username') }}</label>
            <input id="username" class="appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
            @if ($errors->has('username'))
                <ul class="text-sm text-red-600 space-y-1 mt-2">
                    @foreach ($errors->get('username') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">{{ __('Password') }}</label>

            <input id="password" class="appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password">

            @if ($errors->has('password'))
                <ul class="text-sm text-red-600 space-y-1 mt-2">
                    @foreach ($errors->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border border-gray-300 text-indigo-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
@endsection
