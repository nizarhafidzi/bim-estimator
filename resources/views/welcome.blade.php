<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BIM Cost Estimator - Next Gen QS Tool</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">

    <nav class="w-full py-6 px-6 flex justify-between items-center max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-slate-900">BIM EST</span>
        </div>
        <div>
            @if (Route::has('login'))
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-slate-600 hover:text-blue-600 transition">Go to Dashboard &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-slate-900 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full font-bold text-sm transition shadow-lg shadow-blue-200">Get Started</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-6 pt-12 pb-24 text-center">
        <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            5D BIM Technology
        </span>
        <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
            Automated Cost Estimation <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600"> directly from Revit</span>
        </h1>
        <p class="text-lg text-slate-600 mb-10 max-w-2xl mx-auto">
            Stop manual counting. Connect your Autodesk Construction Cloud, import your AHSP Library, and get instant Bill of Quantities (BOQ) mapped to your 3D Model.
        </p>
        
        @auth
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold leading-6 text-white transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue md:py-4 md:text-lg md:px-10 shadow-xl shadow-blue-200">
                Launch Application
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold leading-6 text-white transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue md:py-4 md:text-lg md:px-10 shadow-xl shadow-blue-200">
                Start Free Trial
            </a>
        @endauth
    </div>

    <div class="bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-20">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">How It Works</h2>
                <p class="text-slate-500 mt-2">Follow this workflow to generate your first estimate.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-slate-100 -z-10"></div>

                <div class="relative bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition text-center group">
                    <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 text-2xl font-bold group-hover:scale-110 transition-transform border-4 border-white">1</div>
                    <h3 class="font-bold text-lg mb-2">Connect ACC</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Login with your Autodesk account to access your BIM 360 / ACC projects directly.
                    </p>
                </div>

                <div class="relative bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition text-center group">
                    <div class="w-16 h-16 mx-auto bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-4 text-2xl font-bold group-hover:scale-110 transition-transform border-4 border-white">2</div>
                    <h3 class="font-bold text-lg mb-2">Prepare Library</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Upload your Price Database (AHSP) via Excel. This acts as your standard "Recipe Book".
                    </p>
                </div>

                <div class="relative bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition text-center group">
                    <div class="w-16 h-16 mx-auto bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-4 text-2xl font-bold group-hover:scale-110 transition-transform border-4 border-white">3</div>
                    <h3 class="font-bold text-lg mb-2">Import Model</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Import 3D Revit file. We extract metadata (Volume & Assembly Codes) automatically.
                    </p>
                </div>

                <div class="relative bg-white p-6 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition text-center group">
                    <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-4 text-2xl font-bold group-hover:scale-110 transition-transform border-4 border-white">4</div>
                    <h3 class="font-bold text-lg mb-2">Auto Estimate</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        The system matches Revit Codes with AHSP Codes. View the colored 3D BOQ instantly.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-20">
        <div class="bg-slate-900 text-slate-300 rounded-2xl p-8 md:p-12 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                User Manual & Requirements
            </h2>
            
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="font-mono text-blue-400 font-bold">01</div>
                    <div>
                        <h4 class="text-white font-bold mb-1">Revit Modeling Standard</h4>
                        <p class="text-sm leading-relaxed">
                            To allow automatic matching, every element in Revit must have a valid 
                            <code class="bg-slate-800 px-1 py-0.5 rounded text-blue-200">Assembly Code</code> 
                            (e.g., <span class="text-yellow-400">C2010</span>). This code acts as the key to link with your Price Library.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="font-mono text-blue-400 font-bold">02</div>
                    <div>
                        <h4 class="text-white font-bold mb-1">Autodesk Integration</h4>
                        <p class="text-sm leading-relaxed">
                            You must add this App's Client ID to your BIM 360 / ACC Account Admin (Custom Integrations) to allow file access.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="font-mono text-blue-400 font-bold">03</div>
                    <div>
                        <h4 class="text-white font-bold mb-1">Excel Template</h4>
                        <p class="text-sm leading-relaxed">
                            When uploading Cost Library, use the provided template. Sheet 1 for <strong>Resources</strong> (Materials/Labor) and Sheet 2 for <strong>Analysis</strong> (AHSP Recipes). Ensure codes match exactly.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="font-mono text-blue-400 font-bold">04</div>
                    <div>
                        <h4 class="text-white font-bold mb-1">3D Visualization Logic</h4>
                        <ul class="text-sm list-disc pl-4 mt-2 space-y-1 text-slate-400">
                            <li><span class="text-green-400 font-bold">Green</span> = Matched (Price found & Calculated).</li>
                            <li><span class="text-red-400 font-bold">Red</span> = Unassigned (Assembly Code missing or not found in Library).</li>
                            <li><span class="text-blue-400 font-bold">Blue</span> = Selected/Isolated via Sidebar.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200 py-12 text-center">
        <p class="text-slate-500 text-sm">
            &copy; {{ date('Y') }} BIM Cost Estimator. Built with Laravel & Autodesk Platform Services.
        </p>
    </footer>

</body>
</html>