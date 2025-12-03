<nav x-data="{ open: false }" class="bg-slate-900 text-slate-300 flex-shrink-0 w-64 flex flex-col h-screen fixed left-0 top-0 z-50 border-r border-slate-800 shadow-2xl hidden md:flex">
    
    <div class="h-20 flex items-center px-6 bg-slate-950 border-b border-slate-800">
        <div class="flex items-center gap-3 text-white font-extrabold tracking-tight text-xl">
            <div class="bg-indigo-600 p-1.5 rounded-lg shadow-lg shadow-indigo-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span>BIM <span class="text-indigo-500">TECH</span></span>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4 space-y-8">
        
        @php
            $baseClass = "flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group relative text-sm";
            $activeClass = "bg-indigo-600 text-white shadow-lg shadow-indigo-900/50 font-semibold";
            $inactiveClass = "hover:bg-slate-800 hover:text-white text-slate-400";
            
            // Helper function to check active routes
            $isActive = function($patterns) {
                return request()->routeIs($patterns) ? true : false;
            };
        @endphp

        <div>
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-2">General</h3>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" wire:navigate class="{{ $baseClass }} {{ $isActive('dashboard') ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path></svg>
                    Dashboard
                </a>
                
                <a href="{{ route('documentation') }}" wire:navigate class="{{ $baseClass }} {{ $isActive('documentation') ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Documentation
                </a>
            </div>
        </div>

        <div>
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 px-2 flex justify-between items-center">
                BIM Estimator
                <span class="bg-slate-800 text-slate-400 text-[9px] px-1.5 py-0.5 rounded">Cost</span>
            </h3>
            <div class="space-y-1">
                <a href="{{ route('cost-libraries') }}" wire:navigate class="{{ $baseClass }} {{ $isActive(['cost-libraries', 'resource-manager', 'ahsp-builder']) ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Cost Libraries
                </a>

                <a href="{{ route('cost-calculator') }}" wire:navigate class="{{ $baseClass }} {{ $isActive(['cost-calculator', 'project-report']) ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Project Calculator
                </a>
            </div>
        </div>

        <div>
            <h3 class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-2 px-2 flex justify-between items-center">
                BIM Model Checker
                <span class="bg-indigo-900/30 text-indigo-300 text-[9px] px-1.5 py-0.5 rounded">QC</span>
            </h3>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" wire:navigate class="{{ $baseClass }} {{ $isActive(['project.files', 'file-dashboard']) ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Manage Models
                </a>

                <a href="{{ route('model-inspector') }}" wire:navigate class="{{ $baseClass }} {{ $isActive('model-inspector') ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Data Inspector
                </a>
            </div>
        </div>

    </div>

    <div class="p-4 bg-slate-950 border-t border-slate-800">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-lg">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white py-2 rounded-md text-xs font-bold transition-colors group">
                <svg class="w-3 h-3 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sign Out
            </button>
        </form>
    </div>
</nav>