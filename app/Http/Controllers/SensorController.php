<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController
{
    /**
     * Display the sensor list along with the add-sensor form.
     */
    public function list()
    {
        return view('sensors', [
            'sensors' => Sensor::with('latestData')->orderBy('order')->get(),
        ]);
    }

    /**
     * Store a newly created sensor in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac' => 'required|string|max:255|unique:sensors,mac',
        ]);

        $nextOrder = (int) Sensor::max('order') + 1;

        Sensor::create($request->only('name', 'mac') + ['reachable' => false, 'order' => $nextOrder]);

        return redirect()->route('sensors.list')->with('success', 'Sensor added successfully.');
    }

    /**
     * Update an existing sensor's name/MAC address in storage.
     */
    public function update(Request $request, Sensor $sensor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac' => 'required|string|max:255|unique:sensors,mac,' . $sensor->id,
        ]);

        $sensor->update($request->only('name', 'mac'));

        return redirect()->route('sensors.list')->with('success', 'Sensor updated successfully.');
    }

    /**
     * Move the given sensor one place earlier in the list.
     */
    public function moveUp(Sensor $sensor)
    {
        $previous = Sensor::where('order', '<', $sensor->order)->orderBy('order', 'desc')->first();

        if ($previous) {
            $this->swapOrder($sensor, $previous);
        }

        return redirect()->route('sensors.list');
    }

    /**
     * Move the given sensor one place later in the list.
     */
    public function moveDown(Sensor $sensor)
    {
        $next = Sensor::where('order', '>', $sensor->order)->orderBy('order')->first();

        if ($next) {
            $this->swapOrder($sensor, $next);
        }

        return redirect()->route('sensors.list');
    }

    /**
     * Swap the order values of two sensors.
     */
    private function swapOrder(Sensor $a, Sensor $b)
    {
        [$orderA, $orderB] = [$a->order, $b->order];

        $a->update(['order' => $orderB]);
        $b->update(['order' => $orderA]);
    }

    /**
     * Remove the given sensor from storage.
     */
    public function destroy(Sensor $sensor)
    {
        $sensor->delete();

        return redirect()->route('sensors.list')->with('success', 'Sensor removed successfully.');
    }
}
