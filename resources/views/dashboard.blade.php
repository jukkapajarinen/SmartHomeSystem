@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($sensors->isEmpty() && $cameras->isEmpty())
                <div class="p-4 sm:p-8 bg-gray-100 shadow-sm ring-1 ring-gray-300 sm:rounded-lg">
                    <p class="text-sm text-gray-600">No sensors or cameras available.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($sensors as $sensor)
                        <div class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden">
                            <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                                <h3 class="text-xs font-medium text-white uppercase tracking-wider">{{ $sensor->name }}</h3>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider font-mono">{{ $sensor->mac }}</p>
                            </div>

                            <div class="p-6">
                                @php
                                    $latest = $sensor->data->last();

                                    $temps = $sensor->data->pluck('temperature');
                                    $minTemp = $temps->min();
                                    $maxTemp = $temps->max();
                                    $tempRange = max($maxTemp - $minTemp, 0.1);

                                    $hums = $sensor->data->pluck('humidity');
                                    $minHum = $hums->min();
                                    $maxHum = $hums->max();
                                    $humRange = max($maxHum - $minHum, 0.1);

                                    $tempColor = $latest->temperature >= 28 ? 'text-red-500' : ($latest->temperature <= 15 ? 'text-blue-500' : 'text-gray-900');
                                    $batteryColor = $latest->battery <= 20 ? 'bg-red-500' : ($latest->battery <= 50 ? 'bg-yellow-400' : 'bg-green-500');
                                    $batteryTextColor = $latest->battery <= 20 ? 'text-red-500' : ($latest->battery <= 50 ? 'text-yellow-500' : 'text-green-600');
                                @endphp

                                <!-- Stat tiles -->
                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                                        <div class="text-2xl font-bold {{ $tempColor }}">{{ number_format($latest->temperature, 1) }}°</div>
                                        <div class="text-xs text-gray-500 mt-1">Temp</div>
                                    </div>
                                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($latest->humidity, 0) }}%</div>
                                        <div class="text-xs text-gray-500 mt-1">Humidity</div>
                                    </div>
                                    <div class="rounded-lg bg-gray-50 p-3 text-center">
                                        <div class="text-2xl font-bold {{ $batteryTextColor }}">{{ $latest->battery }}%</div>
                                        <div class="text-xs text-gray-500 mt-1">Battery</div>
                                    </div>
                                </div>

                                <!-- Battery level -->
                                <div class="mb-5">
                                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $batteryColor }}" style="width: {{ $latest->battery }}%"></div>
                                    </div>
                                </div>

                                <!-- Temperature trend -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                        <span class="font-medium text-gray-600">Temperature</span>
                                        <span>{{ number_format($minTemp, 1) }}° – {{ number_format($maxTemp, 1) }}°</span>
                                    </div>
                                    <div class="flex items-end gap-px h-12">
                                        @foreach ($sensor->data as $point)
                                            @php $h = max((($point->temperature - $minTemp) / $tempRange) * 100, 8); @endphp
                                            <div class="flex-1 bg-indigo-300 hover:bg-indigo-500 rounded-t-sm transition-colors"
                                                 style="height: {{ $h }}%"
                                                 title="{{ number_format($point->temperature, 1) }}° at {{ $point->created_at->format('H:i') }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Humidity trend -->
                                <div>
                                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                        <span class="font-medium text-gray-600">Humidity</span>
                                        <span>{{ number_format($minHum, 0) }}% – {{ number_format($maxHum, 0) }}%</span>
                                    </div>
                                    <div class="flex items-end gap-px h-12">
                                        @foreach ($sensor->data as $point)
                                            @php $h = max((($point->humidity - $minHum) / $humRange) * 100, 8); @endphp
                                            <div class="flex-1 bg-blue-300 hover:bg-blue-500 rounded-t-sm transition-colors"
                                                 style="height: {{ $h }}%"
                                                 title="{{ number_format($point->humidity, 0) }}% at {{ $point->created_at->format('H:i') }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <p class="text-xs text-gray-600 mt-5">Updated {{ $latest->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach

                    @foreach ($cameras as $camera)
                        <div class="bg-gray-100 rounded-xl shadow-sm ring-1 ring-gray-300 overflow-hidden">
                            <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                                <h3 class="text-xs font-medium text-white uppercase tracking-wider">{{ $camera->name }}</h3>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider font-mono">{{ $camera->ip_address }}</p>
                            </div>
                            <!-- camera feed -->
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
