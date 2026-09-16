@extends('layouts.app')

@section('title', 'Cameras')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-neutral-900 shadow-sm ring-1 ring-gray-900 ring-opacity-5 rounded-xl overflow-hidden">
            <div class="relative flex items-center justify-center min-h-[16rem] p-2">
                <div id="stream-status" class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">
                    Connecting to camera&hellip;
                </div>
                <video id="stream" class="block w-full max-h-[75vh] rounded-lg" autoplay muted playsinline controls></video>
            </div>
        </div>
    </div>

    <script>
        // The feed is a never-ending response (ffmpeg keeps writing frames until
        // the page is left), so if it were wired up as the <video>'s src directly
        // in the HTML, the browser would never consider the page "loaded" - the
        // tab's loading spinner would spin for as long as the stream plays.
        // Starting it only after the window's load event has already fired keeps
        // the feed out of that initial page-load bookkeeping.
        const stream = document.getElementById('stream');
        const status = document.getElementById('stream-status');
        const feedUrl = '{{ route('cameras.feed') }}';

        const connect = (url) => {
            stream.src = url;
            stream.load();
            stream.play().catch(() => {});
        };

        stream.addEventListener('loadeddata', () => {
            status.style.display = 'none';
        });

        const reconnect = () => {
            status.style.display = 'flex';
            status.textContent = 'Connection lost, reconnecting…';
            setTimeout(() => connect(feedUrl + '?' + Date.now()), 2000);
        };

        stream.addEventListener('error', reconnect);
        stream.addEventListener('ended', reconnect);

        window.addEventListener('load', () => connect(feedUrl));
    </script>
@endsection
