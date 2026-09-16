<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Sensor;

class DashboardController
{
    /**
     * Number of most recent readings to show per sensor.
     */
    private const HISTORY_SIZE = 30;

    public function index()
    {
        $sensors = Sensor::with(['data' => function ($query) {
            $query->latest()->limit(self::HISTORY_SIZE);
        }])->orderBy('order')->get()->reject(fn ($sensor) => $sensor->data->isEmpty());

        // The eager-loaded data comes back newest-first; flip it to
        // chronological order so charts read left (old) to right (new).
        $sensors->each(fn ($sensor) => $sensor->setRelation('data', $sensor->data->reverse()->values()));

        $cameras = Camera::orderBy('order')->get();

        return view('dashboard', compact('sensors', 'cameras'));
    }
}
