<?php
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-slate-900 text-slate-300 flex-shrink-0 transition-all duration-300 ease-in-out border-r border-slate-800 w-16 md:w-64 flex flex-col h-screen fixed left-0 top-0 z-50 shadow-2xl">
    
    <div class="flex items-center justify-center h-16 bg-slate-950 border-b border-slate-800 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <span class="hidden md:block text-lg font-bold tracking-wide text-white">BIM EST</span>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto py-6 flex flex-col gap-1 px-3">
        
        @php
            $baseClass = "flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group relative";
            $activeClass = "bg-blue-600 text-white shadow-lg shadow-blue-900/50";
            $inactiveClass = "hover:bg-slate-800 hover:text-white";
        @endphp

        <a href="{{ route('dashboard') }}" wire:navigate class="{{ $baseClass }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="hidden md:block ml-3 text-sm font-medium">Dashboard</span>
        </a>

        <a href="{{ route('cost-libraries') }}" wire:navigate class="{{ $baseClass }} {{ request()->routeIs('cost-libraries') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="hidden md:block ml-3 text-sm font-medium">Master Harga</span>
        </a>

        <a href="{{ route('cost-calculator') }}" wire:navigate class="{{ $baseClass }} {{ request()->routeIs('cost-calculator') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="hidden md:block ml-3 text-sm font-medium">Estimasi Biaya</span>
        </a>

        <a href="{{ route('model-inspector') }}" wire:navigate class="{{ $baseClass }} {{ request()->routeIs('model-inspector') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span class="hidden md:block ml-3 text-sm font-medium">Data Inspector</span>
        </a>

    </div>

    <div class="p-4 border-t border-slate-800 bg-slate-950">
        <button wire:click="logout" class="flex items-center w-full text-slate-400 hover:text-red-400 transition-colors group">
             <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="hidden md:block ml-3 text-sm font-medium">Sign Out</span>
        </button>
    </div>
</nav>