@extends('layouts.app')

@section('title', 'Sensors')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">MAC Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Last Read</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sensors as $sensor)
                            <tr class="hover:bg-white transition-colors">
                                <td class="px-6 py-3 text-sm">
                                    <div class="flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('sensors.move-up', $sensor) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 bg-white text-gray-600 text-base font-bold hover:bg-gray-100 hover:border-gray-400 hover:text-gray-900 active:bg-gray-200 disabled:opacity-30 disabled:hover:bg-white disabled:hover:border-gray-300 disabled:hover:text-gray-600 disabled:cursor-not-allowed" @if ($loop->first) disabled @endif title="Move up">&uarr;</button>
                                        </form>
                                        <form method="POST" action="{{ route('sensors.move-down', $sensor) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 bg-white text-gray-600 text-base font-bold hover:bg-gray-100 hover:border-gray-400 hover:text-gray-900 active:bg-gray-200 disabled:opacity-30 disabled:hover:bg-white disabled:hover:border-gray-300 disabled:hover:text-gray-600 disabled:cursor-not-allowed" @if ($loop->last) disabled @endif title="Move down">&darr;</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    <input type="text" name="name" form="sensor-update-{{ $sensor->id }}" value="{{ $sensor->name }}" class="block w-full appearance-none border border-gray-300 focus:border-indigo-500 bg-transparent hover:bg-white px-2 py-1 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md">
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    <input type="text" name="mac" form="sensor-update-{{ $sensor->id }}" value="{{ $sensor->mac }}" class="block w-full appearance-none border border-gray-300 focus:border-indigo-500 bg-transparent hover:bg-white px-2 py-1 text-sm text-gray-500 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md">
                                </td>
                                <td class="px-6 py-3 text-sm {{ !$sensor->latestData || $sensor->latestData->created_at->lt(now()->subMinutes(5)) ? 'text-red-600' : 'text-gray-500' }}">
                                    @php $log = $logEntries[$sensor->mac] ?? []; @endphp
                                    <span class="border-b border-dotted border-gray-400 cursor-help" title="{{ $log ? implode("\n", $log) : 'No log entries found for this MAC.' }}">
                                        {{ $sensor->latestData ? $sensor->latestData->created_at->format('d.m.Y - H:i:s') : 'Never' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-right space-x-3 whitespace-nowrap">
                                    <button type="submit" form="sensor-update-{{ $sensor->id }}" class="font-medium text-indigo-600 hover:text-indigo-800">Save</button>
                                    <form method="POST" action="{{ route('sensors.destroy', $sensor) }}" class="inline" onsubmit="return confirm('Delete this sensor? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No sensors yet — add one below.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 p-4">
                <form method="POST" action="{{ route('sensors.store') }}" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="Name" class="block flex-1 min-w-[10rem] appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ old('name') }}" required>
                    <input type="text" name="mac" placeholder="MAC Address" class="block flex-1 min-w-[10rem] appearance-none border border-gray-400 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-md shadow-sm font-mono" value="{{ old('mac') }}" required>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                        {{ __('Add Sensor') }}
                    </button>
                </form>
            </div>

            @foreach ($sensors as $sensor)
                <form id="sensor-update-{{ $sensor->id }}" method="POST" action="{{ route('sensors.update', $sensor) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            @endforeach
        </div>
    </div>
@endsection
