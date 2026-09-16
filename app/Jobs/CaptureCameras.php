<?php

namespace App\Jobs;

use App\Models\Camera;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Grabs one still frame from each camera's ONVIF/RTSP stream and stores it,
 * base64-encoded, on the camera's row, so the dashboard can serve a cheap,
 * periodically refreshed snapshot instead of holding a request open to proxy
 * a live stream.
 */
class CaptureCameras implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        foreach (Camera::all() as $camera) {
            $process = new Process([
                config('services.onvif.ffmpeg_binary'),
                '-rtsp_transport', 'tcp',
                '-i', $camera->stream_url,
                '-frames:v', '1',
                '-vf', "scale='min(iw,{$camera->max_width})':-2",
                '-q:v', (string) $camera->quality,
                '-f', 'image2pipe',
                '-vcodec', 'mjpeg',
                'pipe:1',
            ]);
            $process->setTimeout(15);

            try {
                $process->mustRun();

                $camera->snapshot = base64_encode($process->getOutput());
                $camera->snapshot_captured_at = now();
                $camera->save();

                Log::info("Captured snapshot for camera {$camera->name}.");
            } catch (ProcessFailedException $e) {
                Log::error("Failed to capture snapshot for camera {$camera->name}: {$e->getMessage()}");
            }
        }
    }
}
