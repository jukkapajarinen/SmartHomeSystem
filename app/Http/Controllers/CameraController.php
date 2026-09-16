<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

class CameraController
{
    /**
     * Display the camera list along with the add-camera form.
     */
    public function list()
    {
        return view('cameras', [
            'cameras' => Camera::orderBy('order')->get(),
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
     * Proxy the given camera's ONVIF RTSP stream to the browser as an
     * MJPEG multipart stream (multipart/x-mixed-replace).
     *
     * Each frame is a standalone JPEG, so there's no decoder buffer or
     * container state to get stuck: a browser <img> pointed at this URL
     * just keeps swapping in the latest frame, and a dropped connection
     * surfaces as a plain 'error' event that's trivial to recover from by
     * resetting the src. That trades away inter-frame compression (more
     * bandwidth than H.264) for a stream that can't stall the way a
     * fragmented-MP4 <video> can. The width is still capped to keep the
     * encode cheap regardless of the source stream's resolution.
     */
    public function feed(Camera $camera): StreamedResponse
    {
        $process = new Process([
            config('services.onvif.ffmpeg_binary'),
            '-rtsp_transport', 'tcp',
            '-i', $camera->stream_url,
            '-an',
            '-vf', "scale='min(iw,{$camera->max_width})':-2",
            '-c:v', 'mjpeg',
            '-pix_fmt', 'yuvj420p',
            '-q:v', (string) $camera->quality,
            '-f', 'mpjpeg',
            '-boundary_tag', 'ffmpeg',
            'pipe:1',
        ]);
        $process->setTimeout(null);
        $process->start();

        return response()->stream(function () use ($process) {
            while ($process->isRunning()) {
                if (connection_aborted()) {
                    $process->stop(0);
                    break;
                }

                echo $process->getIncrementalOutput();

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                usleep(20_000);
            }
        }, 200, [
            'Content-Type' => 'multipart/x-mixed-replace;boundary=ffmpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
