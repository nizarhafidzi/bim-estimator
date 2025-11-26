<div class="p-6 max-w-7xl mx-auto space-y-8">
    
    <div class="flex justify-between items-center">
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase mb-1">Project Management</div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $project->name }}</h2>
            <span class="text-sm text-gray-500">Using Library: {{ $project->costLibrary->name ?? 'None' }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded text-sm hover:bg-gray-50 font-medium">
                &larr; Back to Dashboard
            </a>
            </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-bold">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" wire:poll.3s>
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Linked Revit Models</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">File Name</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Elements</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($files as $file)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-800 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center font-bold text-xs">RVT</div>
                            {{ $file->name }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($file->status == 'processing')
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded animate-pulse font-bold">Processing...</span>
                            @elseif($file->status == 'ready')
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">Ready</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded cursor-help font-bold" title="{{ $file->error_message }}">Error</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500 font-mono">{{ $file->elements()->count() }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2 items-center">
                            
                            @if($file->status == 'ready')
                                <a href="{{ route('file-dashboard', $file->id) }}" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 font-bold flex items-center gap-1 transition shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2 1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                                    Open 3D
                                </a>
                            @endif

                            <button wire:click="deleteFile({{ $file->id }})" wire:confirm="Are you sure? Data will be lost." class="text-xs border border-red-200 text-red-500 hover:bg-red-50 px-3 py-1.5 rounded font-bold transition">
                                Unlink
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic border-dashed border-2 border-gray-100 m-4 rounded-lg">
                            No files linked yet. Please select a file from the browser below.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
            Add File from Autodesk Construction Cloud
        </h3>
        
        <livewire:acc-project-browser />
    </div>
</div>