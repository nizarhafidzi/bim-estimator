<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📚 Cost Libraries</h2>
            <p class="text-sm text-gray-500">Manage your price databases (SNI, Project Specific, etc)</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
        <h3 class="text-xs font-bold uppercase text-gray-500 mb-4 tracking-wide">Create New Library</h3>
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-gray-700 mb-1">Library Name</label>
                <input type="text" wire:model="name" class="w-full border-gray-300 rounded-md text-sm shadow-sm" placeholder="e.g. SNI 2024 - DKI Jakarta">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-gray-700 mb-1">Description</label>
                <input type="text" wire:model="description" class="w-full border-gray-300 rounded-md text-sm shadow-sm" placeholder="Optional description">
            </div>
            <button wire:click="createLibrary" class="bg-gray-900 text-white px-5 py-2 rounded-md text-sm font-bold hover:bg-black transition w-full md:w-auto">
                + Create
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($libraries as $lib)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full hover:shadow-md transition-shadow">
                <div class="p-5 border-b border-gray-100">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-lg text-gray-900 line-clamp-1" title="{{ $lib->name }}">{{ $lib->name }}</h3>
                        <button wire:click="deleteLibrary({{ $lib->id }})" wire:confirm="Delete this library? All data inside will be lost." class="text-gray-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2 h-10">{{ $lib->description ?? 'No description provided.' }}</p>
                </div>

                <div class="px-5 py-3 bg-gray-50 flex justify-between text-xs font-medium text-gray-600">
                    <span>📦 {{ $lib->resources_count }} Resources</span>
                    <span>📑 {{ $lib->ahsps_count }} AHSPs</span>
                </div>

                <div class="p-5 mt-auto space-y-3">
                    @if($importLibraryId === $lib->id)
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 animate-fade-in">
                            <form wire:submit.prevent="importExcel" class="space-y-2">
                                <label class="text-xs font-bold text-blue-800 uppercase">Select Excel File</label>
                                <input type="file" wire:model="excelFile" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-white file:text-blue-700 hover:file:bg-blue-50">
                                
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="flex-1 bg-blue-600 text-white py-1.5 rounded text-xs font-bold hover:bg-blue-700">
                                        <span wire:loading.remove wire:target="excelFile, importExcel">Start Upload</span>
                                        <span wire:loading wire:target="excelFile, importExcel">Processing...</span>
                                    </button>
                                    <button type="button" wire:click="$set('importLibraryId', null)" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-bold text-gray-600 hover:bg-gray-50">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                    <div class="flex gap-2 mt-2">
                        <a href="{{ route('resource-manager', $lib->id) }}" class="flex-1 text-center border border-gray-300 px-2 py-1 text-xs rounded hover:bg-gray-50">
                            Manage Resources
                        </a>
                        <button wire:click="exportLibrary({{ $lib->id }})" class="flex-1 text-center border border-gray-300 px-2 py-1 text-xs rounded hover:bg-gray-50">
                            Export Excel
                        </button>
                    </div>
                        <button wire:click="openImportModal({{ $lib->id }})" class="w-full flex items-center justify-center gap-2 border border-blue-200 text-blue-700 bg-blue-50/50 py-2 rounded-lg text-sm font-bold hover:bg-blue-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Excel Data
                        </button>
                    @endif
                    
                    <a href="{{ route('ahsp-builder', $lib->id) }}" class="flex-1 text-center bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md text-sm hover:bg-gray-50 font-bold flex items-center justify-center gap-2 transition shadow-sm">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Open Builder
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>