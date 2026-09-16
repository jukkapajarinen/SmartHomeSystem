<?php

namespace App\Jobs;

use App\Models\Camera;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Grabs one still frame from each camera's ONVIF/RTSP stream and saves it to
 * storage, so the dashboard can serve a cheap, periodically refreshed
 * snapshot instead of holding a request open to proxy a live stream.
 */
class CaptureCameras implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Storage::disk('local')->makeDirectory('camera-snapshots');

        foreach (Camera::all() as $camera) {
            $fullPath = Storage::disk('local')->path($camera->snapshotPath());
            $tmpPath = "{$fullPath}.tmp";

            $process = new Process([
                config('services.onvif.ffmpeg_binary'),
                '-y',
                '-rtsp_transport', 'tcp',
                '-i', $camera->stream_url,
                '-frames:v', '1',
                '-vf', "scale='min(iw,{$camera->max_width})':-2",
                '-q:v', (string) $camera->quality,
                $tmpPath,
            ]);
            $process->setTimeout(15);

            try {
                $process->mustRun();

                // Capture to a temp file and rename into place so the
                // controller never serves a half-written frame.
                rename($tmpPath, $fullPath);

                Log::info("Captured snapshot for camera {$camera->name}.");
            } catch (ProcessFailedException $e) {
                @unlink($tmpPath);
                Log::error("Failed to capture snapshot for camera {$camera->name}: {$e->getMessage()}");
            }
        }
    }
}
