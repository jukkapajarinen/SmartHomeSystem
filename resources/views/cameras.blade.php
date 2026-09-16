@extends('layouts.app')

@section('title', 'Cameras')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-neutral-900 shadow-sm ring-1 ring-gray-900 ring-opacity-5 rounded-xl overflow-hidden">
            <div class="relative flex items-center justify-center min-h-[16rem] p-2">
                <button id="stream-start" type="button" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm">Click to start camera feed</span>
                </button>
                <div id="stream-status" class="absolute inset-0 hidden items-center justify-center text-gray-400 text-sm">
                    Connecting to camera&hellip;
                </div>
                <video id="stream" class="block w-full max-h-[75vh] rounded-lg" muted playsinline controls></video>
            </div>
        </div>
    </div>

    <script>
        // The feed is a never-ending response (ffmpeg keeps writing frames until
        // the page is left). Pointing the <video>'s src straight at it makes the
        // browser track that open-ended network request as part of the page's
        // own loading state, so the tab's loading spinner never stops for as
        // long as the stream plays.
        //
        // Instead, the stream is fetched manually and fed into a MediaSource via
        // the Media Source Extensions API. The <video> element then only ever
        // points at a local blob URL, so its network activity is invisible to
        // the browser's page-loading bookkeeping. This relies on the feed being
        // fragmented MP4 with a fixed H.264 profile/level (see
        // CameraController::feed()), which is exactly what MSE expects.
        //
        // ffmpeg is spawned server-side per request, so the stream is also only
        // started once the visitor clicks play, rather than on every page load.
        const startButton = document.getElementById('stream-start');
        const stream = document.getElementById('stream');
        const status = document.getElementById('stream-status');
        const feedUrl = '{{ route('cameras.feed') }}';
        const mimeCodec = 'video/mp4; codecs="avc1.42E028"';

        // Live feed can be left open for hours; cap how much decoded video MSE
        // is allowed to keep buffered so memory use doesn't grow without bound.
        const MAX_BUFFER_SECONDS = 30;
        const TRIM_TO_SECONDS = 15;

        let abortController = null;
        let reconnectTimer = null;
        let objectUrl = null;

        const scheduleReconnect = () => {
            if (reconnectTimer) {
                return;
            }
            status.style.display = 'flex';
            status.textContent = 'Connection lost, reconnecting…';
            reconnectTimer = setTimeout(() => {
                reconnectTimer = null;
                connect();
            }, 2000);
        };

        const connect = () => {
            if (!('MediaSource' in window) || !MediaSource.isTypeSupported(mimeCodec)) {
                status.textContent = 'This browser cannot play the camera feed.';
                return;
            }

            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();
            const { signal } = abortController;

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }

            const mediaSource = new MediaSource();
            objectUrl = URL.createObjectURL(mediaSource);
            stream.src = objectUrl;
            stream.play().catch(() => {});

            mediaSource.addEventListener('sourceopen', () => {
                if (signal.aborted) {
                    return;
                }

                const sourceBuffer = mediaSource.addSourceBuffer(mimeCodec);
                const queue = [];

                const pump = () => {
                    if (sourceBuffer.updating || queue.length === 0) {
                        return;
                    }

                    const next = queue.shift();
                    if (next.type === 'append') {
                        sourceBuffer.appendBuffer(next.data);
                    } else {
                        sourceBuffer.remove(next.start, next.end);
                    }
                };

                sourceBuffer.addEventListener('updateend', () => {
                    if (queue.length === 0 && sourceBuffer.buffered.length > 0) {
                        const bufferedStart = sourceBuffer.buffered.start(0);
                        const bufferedEnd = sourceBuffer.buffered.end(sourceBuffer.buffered.length - 1);
                        if (bufferedEnd - bufferedStart > MAX_BUFFER_SECONDS) {
                            queue.push({ type: 'remove', start: bufferedStart, end: bufferedEnd - TRIM_TO_SECONDS });
                        }
                    }
                    pump();
                });

                sourceBuffer.addEventListener('error', scheduleReconnect);

                fetch(feedUrl, { signal })
                    .then((response) => {
                        const reader = response.body.getReader();

                        const read = () => {
                            reader.read().then(({ done, value }) => {
                                if (signal.aborted) {
                                    return;
                                }

                                if (done) {
                                    if (mediaSource.readyState === 'open') {
                                        mediaSource.endOfStream();
                                    }
                                    scheduleReconnect();
                                    return;
                                }

                                queue.push({ type: 'append', data: value });
                                pump();
                                read();
                            }).catch((error) => {
                                if (error.name !== 'AbortError') {
                                    scheduleReconnect();
                                }
                            });
                        };

                        read();
                    })
                    .catch((error) => {
                        if (error.name !== 'AbortError') {
                            scheduleReconnect();
                        }
                    });
            });
        };

        stream.addEventListener('loadeddata', () => {
            status.style.display = 'none';
        });

        stream.addEventListener('error', scheduleReconnect);
        stream.addEventListener('ended', scheduleReconnect);

        startButton.addEventListener('click', () => {
            startButton.style.display = 'none';
            status.style.display = 'flex';
            connect();
        });
    </script>
@endsection
