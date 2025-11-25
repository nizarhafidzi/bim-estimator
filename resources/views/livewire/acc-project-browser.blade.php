<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Autodesk Data Browser</h2>
        @if(auth()->user()->autodesk_access_token)
             <form action="{{ route('autodesk.disconnect') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Disconnect</button>
            </form>
        @endif
    </div>

    @if(!auth()->user()->autodesk_access_token)
        <div class="text-center py-10">
            <a href="{{ route('autodesk.login') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Connect Autodesk ACC
            </a>
        </div>
    @else
        @if($errorMsg)
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-sm rounded border border-red-200">{{ $errorMsg }}</div>
        @endif

        @if($viewState != 'hubs')
            <div class="flex flex-col md:flex-row gap-3 mb-4 justify-between items-center bg-gray-50 p-2 rounded-lg">
                <nav class="flex text-sm text-gray-600 overflow-x-auto whitespace-nowrap">
                    @foreach($breadcrumbs as $index => $crumb)
                        @if(!$loop->first) <span class="mx-2 text-gray-400">/</span> @endif
                        <button wire:click="navigateBreadcrumb({{ $index }})" class="hover:text-blue-600 hover:underline font-medium {{ $loop->last ? 'text-gray-900 font-bold cursor-default' : '' }}">
                            {{ $crumb['label'] }}
                        </button>
                    @endforeach
                </nav>

                <input type="text" wire:model.live="search" placeholder="Search..." class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 w-full md:w-48 py-1">
            </div>
        @endif

        @if($viewState == 'hubs')
            <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Select Hub</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($hubs as $hub)
                    <div wire:click="openHub('{{ $hub['id'] }}', '{{ $hub['attributes']['name'] }}')" 
                         class="cursor-pointer p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-300 transition flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="font-medium text-gray-800">{{ $hub['attributes']['name'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if($viewState != 'hubs')
            <div class="border rounded-lg divide-y overflow-hidden">
                @forelse($filteredItems as $item)
                    @php
                        $type = $item['type']; // projects, folders, items
                        $name = $item['attributes']['name'] ?? $item['attributes']['displayName'];
                        $id = $item['id'];
                        
                        // Deteksi apakah ini File (Items) atau Wadah (Project/Folder)
                        $isContainer = in_array($type, ['projects', 'folders']);
                    @endphp

                    <div class="p-3 bg-white hover:bg-gray-50 flex justify-between items-center group transition">
                        <div class="flex items-center gap-3 overflow-hidden cursor-pointer flex-1"
                             @if($isContainer)
                                @if($type == 'projects')
                                    wire:click="openProject('{{ $id }}', '{{ $name }}')"
                                @else
                                    wire:click="openFolder('{{ $id }}', '{{ $name }}')"
                                @endif
                             @endif
                        >
                            @if($type == 'projects')
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            @elseif($type == 'folders')
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 fill-current" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            @endif

                            <span class="text-sm text-gray-700 font-medium truncate group-hover:text-blue-600">
                                {{ $name }}
                            </span>
                        </div>

                        @if(!$isContainer)
                            <button wire:click="importFile('{{ $id }}', '{{ $name }}')"
                                    wire:loading.attr="disabled"
                                    class="text-xs bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 shadow-sm whitespace-nowrap flex items-center gap-2 disabled:opacity-50">
                                <svg wire:loading wire:target="importFile" class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Import
                            </button>
                        @else
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 italic text-sm">
                        {{ empty($search) ? 'Folder is empty.' : 'No items match your search.' }}
                    </div>
                @endforelse
            </div>
        @endif

        <div wire:loading wire:target="openHub, openProject, openFolder, navigateBreadcrumb" class="mt-2 text-blue-600 text-xs font-medium animate-pulse">
            Loading contents...
        </div>
    @endif
</div>