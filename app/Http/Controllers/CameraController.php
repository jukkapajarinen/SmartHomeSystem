<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

class CameraController
{
    /**
     * Display the page hosting the live camera stream.
     */
    public function show(): View
    {
        return view('cameras');
    }

    /**
     * Proxy the ONVIF RTSP stream to the browser as fragmented MP4.
     *
     * ffmpeg re-encodes the RTSP feed to H.264 (whatever the source codec
     * is) and muxes it as a fragmented, streamable MP4 with no seekable
     * index, which a plain <video> tag can play progressively as it
     * arrives. The output isn't forced to a fixed frame rate, so ffmpeg
     * emits frames as fast as it decodes them; a high-resolution source
     * (e.g. a camera's main stream) can outpace real-time decoding and
     * make the feed stall, so the width is capped to keep the encode cheap
     * regardless of the source stream, and the "ultrafast"/"zerolatency"
     * encoder settings favor keeping up in real time over compression.
     */
    public function feed(): StreamedResponse
    {
        $streamUrl = config('services.onvif.stream_url');

        abort_if(empty($streamUrl), 500, 'ONVIF_STREAM_URL is not configured.');

        $maxWidth = (int) config('services.onvif.max_width');
        $crf = (int) config('services.onvif.quality');

        $process = new Process([
            config('services.onvif.ffmpeg_binary'),
            '-rtsp_transport', 'tcp',
            '-i', $streamUrl,
            '-an',
            '-vf', "scale='min(iw,{$maxWidth})':-2",
            '-c:v', 'libx264',
            '-preset', 'ultrafast',
            '-tune', 'zerolatency',
            '-pix_fmt', 'yuv420p',
            '-g', '30',
            '-crf', (string) $crf,
            '-f', 'mp4',
            '-movflags', 'frag_keyframe+empty_moov+default_base_moof',
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
            'Content-Type' => 'video/mp4',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
