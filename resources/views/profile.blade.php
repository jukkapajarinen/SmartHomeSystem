@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden">
                <div class="bg-gray-800 px-6 py-3">
                    <h2 class="text-xs font-medium text-white uppercase tracking-wider">
                        {{ __('Update Password') }}
                    </h2>
                </div>

                <div class="p-6 sm:p-8">
                <form method="post" action="{{ route('password.update') }}" class="max-w-md">
                    @csrf
                    @method('put')

                    <div class="space-y-5">
                    <div>
                        <label for="update_password_current_password" class="block font-medium text-sm text-gray-700">{{ __('Current Password') }}</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" autocomplete="current-password">
                        @if ($errors->updatePassword->has('current_password'))
                            <ul class="text-sm text-red-600 space-y-1 mt-2">
                                @foreach ($errors->updatePassword->get('current_password') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div>
                        <label for="update_password_password" class="block font-medium text-sm text-gray-700">{{ __('New Password') }}</label>
                        <input id="update_password_password" name="password" type="password" class="appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" autocomplete="new-password">
                        @if ($errors->updatePassword->has('password'))
                            <ul class="text-sm text-red-600 space-y-1 mt-2">
                                @foreach ($errors->updatePassword->get('password') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block font-medium text-sm text-gray-700">{{ __('Confirm Password') }}</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" autocomplete="new-password">
                        @if ($errors->updatePassword->has('password_confirmation'))
                            <ul class="text-sm text-red-600 space-y-1 mt-2">
                                @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Save') }}</button>

                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-green-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                {{ __('Saved.') }}
                            </p>
                        @endif
                    </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
