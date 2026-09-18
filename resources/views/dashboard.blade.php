@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($sensors->isEmpty() && $cameras->isEmpty())
                <div class="p-4 sm:p-8 bg-gray-100 shadow-sm ring-1 ring-gray-300 sm:rounded-lg">
                    <p class="text-sm text-gray-600">No data available.</p>
                </div>
            @else
                <div class="flex items-center justify-between gap-4 mb-4">

                    <div class="flex items-center gap-4">
                        <select onchange="cardsGrid.prepend(...cardsGrid.querySelectorAll(`[data-type='${this.value}']`))" class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option value="camera">Cameras first</option>
                            <option value="sensor">Sensors first</option>
                        </select>
                        <select onchange="cardsGrid.className=cardsGrid.className.replace(/xl:grid-cols-\d/,`xl:grid-cols-${this.value}`)" class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option value="1">1 column</option>
                            <option value="2">2 columns</option>
                            <option value="3" selected>3 columns</option>
                            <option value="4">4 columns</option>
                        </select>
                    </div>                    
                    <div class="text-xs text-gray-500 flex items-center gap-1.5">
                        Data from: {{ $lastUpdate->format('d.m.Y - H:i:s') }}
                        <img
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMCAxMCI+PGNpcmNsZSBjeD0iNSIgY3k9IjUiIHI9IjUiIGZpbGw9IiM2MGE1ZmEiLz48L3N2Zz4="
                            alt=""
                            class="w-2 h-2 animate-bounce"
                            onload="setTimeout(() => location.reload(), 60000)"
                        >
                    </div>
                </div>

                <div id="cardsGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($cameras as $camera)
                        <div data-type="camera" class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden flex flex-col">
                            <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                                <h3 class="text-xs font-medium text-white uppercase tracking-wider">{{ $camera->name }}</h3>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider font-mono">{{ $camera->ip_address }}</p>
                            </div>
                            <div class="aspect-video flex-1 bg-black flex items-center justify-center">
                                @if ($camera->has_snapshot)
                                    <img
                                        src="{{ route('cameras.feed', $camera) }}"
                                        alt="{{ $camera->name }}"
                                        loading="lazy"
                                        class="w-full h-full object-contain"
                                    >
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @foreach ($sensors as $sensor)
                        <div data-type="sensor" class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden flex flex-col">
                            <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                                <h3 class="text-xs font-medium text-white uppercase tracking-wider">{{ $sensor->name }}</h3>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider font-mono">{{ $sensor->mac }}</p>
                            </div>

                            <div class="flex-1 flex flex-col">
                                @php
                                    $latest = $sensor->data->last();
                                    $recent = $sensor->data->reverse()->take(5);

                                    $tempColor = $latest->temperature >= 28 ? 'text-red-500' : ($latest->temperature <= 15 ? 'text-blue-500' : 'text-gray-900');
                                    $batteryTextColor = $latest->battery <= 20 ? 'text-red-500' : ($latest->battery <= 50 ? 'text-yellow-500' : 'text-green-600');
                                @endphp

                                <!-- Stat tiles -->
                                <div class="grid grid-cols-3 divide-x divide-gray-300">
                                    <div class="p-3 flex flex-col items-center justify-center text-center cursor-default">
                                        <div class="text-2xl font-bold {{ $tempColor }}">{{ number_format($latest->temperature, 1) }}°</div>
                                        <div class="text-xs text-gray-500 mt-1">Temp</div>
                                    </div>
                                    <div class="p-3 flex flex-col items-center justify-center text-center cursor-default">
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($latest->humidity, 0) }}%</div>
                                        <div class="text-xs text-gray-500 mt-1">Humidity</div>
                                    </div>
                                    <div class="p-3 flex flex-col items-center justify-center text-center cursor-default">
                                        <div class="text-2xl font-bold {{ $batteryTextColor }}">{{ $latest->battery }}%</div>
                                        <div class="text-xs text-gray-500 mt-1">Battery</div>
                                    </div>
                                </div>

                                <!-- History -->
                                <table class="w-full border-t border-gray-300 text-xs">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-300">
                                            <th class="px-3 py-2 text-left font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-400 uppercase tracking-wider">Temp</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-400 uppercase tracking-wider">Humidity</th>
                                            <th class="px-3 py-2 text-right font-semibold text-gray-400 uppercase tracking-wider">Battery</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($recent as $point)
                                            @php
                                                $pointTempColor = $point->temperature >= 28 ? 'text-red-500' : ($point->temperature <= 15 ? 'text-blue-500' : 'text-gray-900');
                                                $pointBatteryColor = $point->battery <= 20 ? 'text-red-500' : ($point->battery <= 50 ? 'text-yellow-500' : 'text-green-600');
                                            @endphp
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }} hover:bg-white transition-colors">
                                                <td class="px-3 py-1.5 text-gray-500">{{ $point->created_at->format('d.m.Y - H:i:s') }}</td>
                                                <td class="px-3 py-1.5 text-right font-medium {{ $pointTempColor }}">{{ number_format($point->temperature, 1) }}°</td>
                                                <td class="px-3 py-1.5 text-right font-medium text-blue-600">{{ number_format($point->humidity, 0) }}%</td>
                                                <td class="px-3 py-1.5 text-right font-medium {{ $pointBatteryColor }}">{{ $point->battery }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
