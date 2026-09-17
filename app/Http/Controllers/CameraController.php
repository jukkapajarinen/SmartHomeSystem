<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Services\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CameraController
{
    /**
     * Display the camera list along with the add-camera form.
     */
    public function list()
    {
        $cameras = Camera::orderBy('order')->get();

        return view('cameras', [
            'cameras' => $cameras,
            'logEntries' => ActivityLog::latestEntriesFor(
                $cameras->map(fn (Camera $camera) => "camera {$camera->name}")->all()
            ),
        ]);
    }

    /**
     * Store a newly created camera in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stream_url' => 'required|string|max:255',
            'max_width' => 'required|integer|min:1',
            'quality' => 'required|integer|min:2|max:31',
        ]);

        $nextOrder = (int) Camera::max('order') + 1;

        Camera::create($request->only('name', 'stream_url', 'max_width', 'quality') + ['order' => $nextOrder]);

        return redirect()->route('cameras.list')->with('success', 'Camera added successfully.');
    }

    /**
     * Update an existing camera's settings in storage.
     */
    public function update(Request $request, Camera $camera)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stream_url' => 'required|string|max:255',
            'max_width' => 'required|integer|min:1',
            'quality' => 'required|integer|min:2|max:31',
        ]);

        $camera->update($request->only('name', 'stream_url', 'max_width', 'quality'));

        return redirect()->route('cameras.list')->with('success', 'Camera updated successfully.');
    }

    /**
     * Move the given camera one place earlier in the list.
     */
    public function moveUp(Camera $camera)
    {
        $previous = Camera::where('order', '<', $camera->order)->orderBy('order', 'desc')->first();

        if ($previous) {
            $this->swapOrder($camera, $previous);
        }

        return redirect()->route('cameras.list');
    }

    /**
     * Move the given camera one place later in the list.
     */
    public function moveDown(Camera $camera)
    {
        $next = Camera::where('order', '>', $camera->order)->orderBy('order')->first();

        if ($next) {
            $this->swapOrder($camera, $next);
        }

        return redirect()->route('cameras.list');
    }

    /**
     * Swap the order values of two cameras.
     */
    private function swapOrder(Camera $a, Camera $b)
    {
        [$orderA, $orderB] = [$a->order, $b->order];

        $a->update(['order' => $orderB]);
        $b->update(['order' => $orderA]);
    }

    /**
     * Remove the given camera from storage.
     */
    public function destroy(Camera $camera)
    {
        $camera->delete();

        return redirect()->route('cameras.list')->with('success', 'Camera removed successfully.');
    }

    /**
     * Serve the given camera's latest snapshot, captured on a schedule by
     * CaptureCameras rather than on request. Nothing here talks to the
     * camera or shells out to ffmpeg, so a request is just a quick read of
     * the stored row and never ties up a worker waiting on the RTSP source.
     */
    public function feed(Camera $camera): Response
    {
        abort_unless($camera->has_snapshot, 404);

        return response(base64_decode($camera->snapshot), 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-store',
        ]);
    }
}
