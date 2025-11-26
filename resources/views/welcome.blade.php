<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BIM Cost Estimator - 5D Construction Tech</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .gradient-text {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">

    <nav class="w-full py-6 px-6 flex justify-between items-center max-w-7xl mx-auto sticky top-0 z-50 bg-slate-50/80 backdrop-blur-md">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-2 rounded-lg shadow-lg shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-slate-900">BIM EST <span class="text-blue-600">5D</span></span>
        </div>
        <div>
            @if (Route::has('login'))
                <div class="flex gap-4 items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-slate-700 hover:text-blue-600 transition">Dashboard &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-slate-900 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg">Get Started</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 pt-16 pb-24 text-center">
        <div class="inline-flex items-center gap-2 py-1 px-3 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider mb-6 animate-bounce">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span> New: AHSP Builder & Excel Import
        </div>
        <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 leading-tight mb-6 tracking-tight">
            Turn Revit Models into <br>
            <span class="gradient-text">Precise Cost Estimates</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-3xl mx-auto leading-relaxed">
            The ultimate 5D BIM solution. Connect Autodesk Construction Cloud, map your standard rates (AHSP), and visualize costs directly on your 3D model.
        </p>
        
        <div class="flex justify-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-1">
                    Launch Application
                </a>
            @else
                <a href="{{ route('register') }}" class="px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-1">
                    Start Free Trial
                </a>
                <a href="#how-it-works" class="px-8 py-4 text-lg font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition">
                    Learn More
                </a>
            @endauth
        </div>
    </div>

    <div class="bg-white py-24 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Federated Model Support</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Manage complex projects with multiple files (Architecture, Structure, MEP). Import them individually and get a unified cost report.
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Smart AHSP Builder</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Build your price recipes (Analysis) easily. Combine resources, set coefficients, or simply import your existing SNI Excel database.
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Visual Reporting</h3>
                    <p class="text-slate-500 leading-relaxed">
                        See what you pay for. Matched items turn green in 3D. Export professional BOQ reports to PDF or Excel ready for tender.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div id="how-it-works" class="bg-slate-50 py-24">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Seamless Workflow</h2>
                <p class="text-slate-500 mt-2">From Cloud to Cost in 4 steps.</p>
            </div>

            <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                
                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-300 group-[.is-active]:bg-blue-600 text-slate-500 group-[.is-active]:text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="font-bold text-slate-900 mb-1">1. Setup Master Data</div>
                        <p class="text-slate-500 text-sm">Upload your price database (Excel) or build AHSP recipes in the system.</p>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-blue-600 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="font-bold text-slate-900 mb-1">2. Import Revit Files</div>
                        <p class="text-slate-500 text-sm">Connect to Autodesk Construction Cloud and import your .rvt files. We extract the metadata automatically.</p>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-blue-600 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="font-bold text-slate-900 mb-1">3. Auto Calculate</div>
                        <p class="text-slate-500 text-sm">The engine matches Assembly Codes from Revit with your Library to generate the BOQ.</p>
                    </div>
                </div>

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-blue-600 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <div class="font-bold text-slate-900 mb-1">4. Audit & Report</div>
                        <p class="text-slate-500 text-sm">Inspect costs in 3D view (Green=Safe, Red=Missing) and export the final report to PDF/Excel.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="bg-slate-900 py-12 text-center border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-left">
                <span class="text-lg font-extrabold text-white tracking-tight">BIM EST</span>
                <p class="text-slate-400 text-sm mt-1">Building Information Modeling Cost Estimation</p>
            </div>
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} Powered by Laravel & Autodesk Platform Services.
            </p>
        </div>
    </footer>

</body>
</html>