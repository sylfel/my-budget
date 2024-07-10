<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Budget</title>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-emerald-100">
    <div>
        @if (Route::has('login'))
            <livewire:welcome.navigation />
        @endif
        <div class="grid grid-cols-1 justify-items-center">
            <div class="w-2/3">
                <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" style="background:0 0"
                    viewBox="-230.7 -42 961.3 234">
                    <defs>
                        <linearGradient id="b" x1=".1" x2=".9" y1=".2" y2=".8"
                            gradientUnits="objectBoundingBox">
                            <stop offset="0" stop-color="#ffb200" />
                            <stop offset=".5" stop-color="#e10057" />
                            <stop offset="1" stop-color="#5A1A80" />
                        </linearGradient>
                        <linearGradient id="d" x1="0" x2="0" y1="0" y2="1">
                            <stop offset=".2" stop-color="#fff" stop-opacity=".8" />
                            <stop offset=".8" stop-color="#fff" stop-opacity="0" />
                        </linearGradient>
                        <filter id="c" width="300%" height="300%" x="-100%" y="-100%">
                            <feMorphology radius="2" />
                        </filter>
                        <filter id="a" width="300%" height="300%" x="-100%" y="-100%">
                            <feFlood flood-color="#fff" result="flood" />
                            <feConvolveMatrix divisor="1" in="SourceGraphic"
                                kernelMatrix="0 0 0 0 0 0 0 0 0 0 0 1 1 0 0 0 0 0 0 1 1 0 0 0 0 0 1 0 0 1 0 0 0 0 1 0 0 1 0 0 0 1 0 0 0 0 1 0 0 1 1 0 0 1 1 0 0 0 0 1 1 0 0 0"
                                order="8,8" result="conv" />
                            <feOffset dy="4" in="conv" result="offset" />
                            <feComposite in="flood" in2="offset" operator="in" result="comp" />
                            <feGaussianBlur in="offset" result="shadow" stdDeviation="3" />
                            <feColorMatrix in="shadow" result="dark-shadow"
                                values="0.7 0 0 0 0 0 0.7 0 0 0 0 0 0.7 0 0 0 0 0 0.3 0" />
                            <feOffset dy="4" in="dark-shadow" result="offset-shadow" />
                            <feOffset dy="2" in="conv" result="offset-2" />
                            <feComposite in="offset" in2="offset-2" operator="out" result="edge-diff" />
                            <feGaussianBlur in="edge-diff" result="edge-blur" stdDeviation="1" />
                            <feColorMatrix in="edge-blur" result="edge-shadow"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.5 0" />
                            <feComposite in="edge-shadow" in2="offset" operator="in" result="edge-shadow-in" />
                            <feOffset dy="1" in="edge-shadow-in" result="edge-shadow-final" />
                            <feMerge result="out">
                                <feMergeNode in="offset-shadow" />
                                <feMergeNode in="comp" />
                                <feMergeNode in="edge-shadow-final" />
                                <feMergeNode in="SourceGraphic" />
                            </feMerge>
                        </filter>
                    </defs>
                    <g filter="url(#a)">
                        <path fill="url(#b)"
                            d="M13.7 0H-1.7l24.2-88.4h28L56.7-38l25-50.5H105l2.4 66.2q.5 13 7 17.1-1.5 2.7-5.8 5.2-4.3 2.6-10 2.6t-9-1.6q-3.4-1.6-5.3-4.3-3.3-4.8-3.3-14v-41.5L54.7 0H38l-9.4-60.2L13.7 0Zm149.1-15.1q-7 7.2-19.7 7.2-16 0-19.3-11.7-1-3.4-1-7.3 0-4 .8-8l6.3-33.4L157-71l-7.4 39-.7 3.6q-.4 1.9-.4 3.8 0 2 .7 3.2.6 1.3 1.6 2.1 1.6 1 4.5 1 2.8 0 5.3-3.4 2.5-3.5 3.7-9.5l7.4-37.1 26.4-2.7-10 52.6q-5 25.8-15.2 36-5.2 5.2-12 7.3-6.8 2-15.5 2-14 0-22-4.4-8.1-4.4-8.1-11.8 0-5.5 4.1-8.6 4.2-3.2 10.6-3.2 5.6 0 10 2.4 2.5 1.5 3.7 3.4-3.1 2.7-3.1 7 0 6 5.4 6 8.8 0 13.9-20.4 1.5-6 2.8-12.4ZM229.3-.7l16.9-87.7q15.8-1.4 26-1.4 10.1 0 16.7.9t11 3.1q8.8 4.3 8.8 16.1 0 6.9-5.9 12.6-5.4 5.2-11.5 6.5 6.4 1 11 5.8 4.8 5.1 4.8 13 0 15.3-11.4 24.2-11.3 9-33.7 9-15.4 0-32.7-2ZM265.7-43 259-8.4h2.7q11 0 15.7-7.1 3.5-5.7 3.5-16 0-5.5-3.6-8.4-3.5-3-11.6-3.3Zm20.4-26.4q0-11.2-10.7-11.2h-1.2l-1.4.2-5.3 28.2h1.3q17.3-.4 17.3-17.3ZM359-5.6q-6.4 8.3-22.4 8.3-8.3 0-14.3-5t-6-12.3q0-4 .3-6l9-47.7 27.2-2.7-9.8 51.7q-.5 3-.5 4.7 0 7.8 4.7 7.8 5.2 0 9-7.1 1.2-2.3 1.7-5.3l9.5-49.1 26.4-2.7-9.9 52.2q-.4 2-.4 4.2 0 2.1 1.2 4.5 1.2 2.3 5.1 2.9Q389-4 386.2-2q-6 4.6-12.3 4.6-6.3 0-10-2.3-3.8-2.3-5-6ZM442.7-71q4.9 0 8.6 1l.4-2 .7-4.1 1.3-7.1 2.2-10.6 26.6-2.7-14.8 77.7q-.2.8-.2 2.2v2.1q0 3 1.5 5.2t3.9 2.2q-2.7 6.3-10.9 9-2.8.8-6.7.8-4 0-7.6-2.1-3.6-2-4.7-5.6-2.1 3.5-6.4 5.6-4.3 2-10.6 2-6.2 0-11.3-1.5-5.2-1.6-8.4-5.1-6-6.8-6-22.4 0-20.1 11.9-32.3Q424-71 442.7-71Zm-.9 8q-5 0-7.6 5.4-2.7 5.3-5.5 18.6-2.7 13.3-2.7 25.2 0 8.2 4.8 8.2 4.3 0 7.3-4.3t4.2-11.4l7.3-39.3q-2.8-2.4-7.8-2.4Zm52 7q4.6-6.4 11.4-10.7 7-4.3 15.4-4.3t12.4 2.7l26.3-2.7-9.1 51.4Q545.5 6.7 535.6 17q-9.5 9.8-28 9.8-14 0-22.1-4.4-8-4.4-8-11.8 0-5.5 4.1-8.7 4.1-3.1 10.6-3.1 5.6 0 9.9 2.5 2.5 1.4 3.8 3.3-3.1 2.6-3.1 7 0 5.8 5.3 5.8 9 0 14.1-21.1 1.5-5.8 2.7-11.6-6 7.4-19.7 7.4-9.5 0-15-4.6-5.5-4.5-5.5-15.2 0-6.7 2.3-14.3 2.2-7.6 6.8-14Zm17.4 28.7q0 9 4.7 9 3.2 0 6.3-3.4 2.4-2.8 3.3-7l6.9-34.4-1.4-.4q-1.3-.5-3-.5-8.2 0-13 13.4-3.8 10.4-3.8 23.3Zm107.6 4.7q3.4 2.2 3.4 7.3 0 5-2.6 8.2-2.5 3.2-6.7 5.4-8.6 4.4-17.8 4.4-9.3 0-14.7-2-5.4-2-9-5.8-7.1-7-7.1-20 0-20.3 11-32.7Q587-71 607.6-71q12.7 0 19 5.3 4.7 4 4.7 10.6 0 23.6-40.8 23.6-.5 3.5-.5 6.4 0 6.2 2.8 8.6 2.7 2.3 7.8 2.3 5 0 10.5-2.4 5.4-2.3 7.7-6Zm-27.3-14.6q9.5 0 15-6 5.5-5.6 5.5-14.5 0-3.1-1.2-4.8-1.1-1.7-3.4-1.7t-4.2.9q-2 .9-4 3.8-5 6.7-7.7 22.3ZM638-13.4q0-3.6 1.9-12.7l7-36.2H639l.6-4q16-4.9 31.6-16.7h6.4l-3.2 14.7H685l-1.2 6h-10.4l-6.8 36.2q-1.7 8.3-1.7 11 0 6.5 5.6 7.9-1.3 4.5-6.2 7.2-4.8 2.7-11.6 2.7-6.9 0-10.7-4.3-4-4.3-4-11.8Z"
                            transform="translate(-90.4 136.7)" />
                    </g>
                    <g filter="url(#c)">
                        <path fill="url(#d)"
                            d="M13.7 0H-1.7l24.2-88.4h28L56.7-38l25-50.5H105l2.4 66.2q.5 13 7 17.1-1.5 2.7-5.8 5.2-4.3 2.6-10 2.6t-9-1.6q-3.4-1.6-5.3-4.3-3.3-4.8-3.3-14v-41.5L54.7 0H38l-9.4-60.2L13.7 0Zm149.1-15.1q-7 7.2-19.7 7.2-16 0-19.3-11.7-1-3.4-1-7.3 0-4 .8-8l6.3-33.4L157-71l-7.4 39-.7 3.6q-.4 1.9-.4 3.8 0 2 .7 3.2.6 1.3 1.6 2.1 1.6 1 4.5 1 2.8 0 5.3-3.4 2.5-3.5 3.7-9.5l7.4-37.1 26.4-2.7-10 52.6q-5 25.8-15.2 36-5.2 5.2-12 7.3-6.8 2-15.5 2-14 0-22-4.4-8.1-4.4-8.1-11.8 0-5.5 4.1-8.6 4.2-3.2 10.6-3.2 5.6 0 10 2.4 2.5 1.5 3.7 3.4-3.1 2.7-3.1 7 0 6 5.4 6 8.8 0 13.9-20.4 1.5-6 2.8-12.4ZM229.3-.7l16.9-87.7q15.8-1.4 26-1.4 10.1 0 16.7.9t11 3.1q8.8 4.3 8.8 16.1 0 6.9-5.9 12.6-5.4 5.2-11.5 6.5 6.4 1 11 5.8 4.8 5.1 4.8 13 0 15.3-11.4 24.2-11.3 9-33.7 9-15.4 0-32.7-2ZM265.7-43 259-8.4h2.7q11 0 15.7-7.1 3.5-5.7 3.5-16 0-5.5-3.6-8.4-3.5-3-11.6-3.3Zm20.4-26.4q0-11.2-10.7-11.2h-1.2l-1.4.2-5.3 28.2h1.3q17.3-.4 17.3-17.3ZM359-5.6q-6.4 8.3-22.4 8.3-8.3 0-14.3-5t-6-12.3q0-4 .3-6l9-47.7 27.2-2.7-9.8 51.7q-.5 3-.5 4.7 0 7.8 4.7 7.8 5.2 0 9-7.1 1.2-2.3 1.7-5.3l9.5-49.1 26.4-2.7-9.9 52.2q-.4 2-.4 4.2 0 2.1 1.2 4.5 1.2 2.3 5.1 2.9Q389-4 386.2-2q-6 4.6-12.3 4.6-6.3 0-10-2.3-3.8-2.3-5-6ZM442.7-71q4.9 0 8.6 1l.4-2 .7-4.1 1.3-7.1 2.2-10.6 26.6-2.7-14.8 77.7q-.2.8-.2 2.2v2.1q0 3 1.5 5.2t3.9 2.2q-2.7 6.3-10.9 9-2.8.8-6.7.8-4 0-7.6-2.1-3.6-2-4.7-5.6-2.1 3.5-6.4 5.6-4.3 2-10.6 2-6.2 0-11.3-1.5-5.2-1.6-8.4-5.1-6-6.8-6-22.4 0-20.1 11.9-32.3Q424-71 442.7-71Zm-.9 8q-5 0-7.6 5.4-2.7 5.3-5.5 18.6-2.7 13.3-2.7 25.2 0 8.2 4.8 8.2 4.3 0 7.3-4.3t4.2-11.4l7.3-39.3q-2.8-2.4-7.8-2.4Zm52 7q4.6-6.4 11.4-10.7 7-4.3 15.4-4.3t12.4 2.7l26.3-2.7-9.1 51.4Q545.5 6.7 535.6 17q-9.5 9.8-28 9.8-14 0-22.1-4.4-8-4.4-8-11.8 0-5.5 4.1-8.7 4.1-3.1 10.6-3.1 5.6 0 9.9 2.5 2.5 1.4 3.8 3.3-3.1 2.6-3.1 7 0 5.8 5.3 5.8 9 0 14.1-21.1 1.5-5.8 2.7-11.6-6 7.4-19.7 7.4-9.5 0-15-4.6-5.5-4.5-5.5-15.2 0-6.7 2.3-14.3 2.2-7.6 6.8-14Zm17.4 28.7q0 9 4.7 9 3.2 0 6.3-3.4 2.4-2.8 3.3-7l6.9-34.4-1.4-.4q-1.3-.5-3-.5-8.2 0-13 13.4-3.8 10.4-3.8 23.3Zm107.6 4.7q3.4 2.2 3.4 7.3 0 5-2.6 8.2-2.5 3.2-6.7 5.4-8.6 4.4-17.8 4.4-9.3 0-14.7-2-5.4-2-9-5.8-7.1-7-7.1-20 0-20.3 11-32.7Q587-71 607.6-71q12.7 0 19 5.3 4.7 4 4.7 10.6 0 23.6-40.8 23.6-.5 3.5-.5 6.4 0 6.2 2.8 8.6 2.7 2.3 7.8 2.3 5 0 10.5-2.4 5.4-2.3 7.7-6Zm-27.3-14.6q9.5 0 15-6 5.5-5.6 5.5-14.5 0-3.1-1.2-4.8-1.1-1.7-3.4-1.7t-4.2.9q-2 .9-4 3.8-5 6.7-7.7 22.3ZM638-13.4q0-3.6 1.9-12.7l7-36.2H639l.6-4q16-4.9 31.6-16.7h6.4l-3.2 14.7H685l-1.2 6h-10.4l-6.8 36.2q-1.7 8.3-1.7 11 0 6.5 5.6 7.9-1.3 4.5-6.2 7.2-4.8 2.7-11.6 2.7-6.9 0-10.7-4.3-4-4.3-4-11.8Z"
                            transform="translate(-90.3 136.7)" />
                    </g>
                </svg>
            </div>
            <div class="text-center italic font-mono text-slate-500 text-sm w-2/3">
                Une gestion de budget personnel, simple et efficace
            </div>
        </div>
    </div>
</body>

</html>
