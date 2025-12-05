<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('BIM Tech Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(auth()->user()->autodesk_access_token)
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <div>
                            <span class="text-sm font-bold text-emerald-800 block">Autodesk Cloud Connected</span>
                            <span class="text-xs text-emerald-600">Account: {{ auth()->user()->autodesk_account_name }}</span>
                        </div>
                    </div>
                    <form action="{{ route('autodesk.disconnect') }}" method="POST">
                        @csrf 
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold underline decoration-dotted">
                            Disconnect
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-white border border-gray-200 p-6 rounded-xl text-center shadow-sm">
                    <div class="mb-4">
                        <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Connect to Autodesk</h3>
                        <p class="text-sm text-gray-500">Link your BIM 360 / ACC account to start importing models.</p>
                    </div>
                    <a href="{{ route('autodesk.login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Connect Now
                    </a>
                </div>
            @endif

            <livewire:project-list />

            @if(auth()->user()->autodesk_access_token)
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Quick Import from Cloud
                    </h3>
                    <livewire:acc-project-browser />
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>