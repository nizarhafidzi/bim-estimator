<div class="p-6 max-w-7xl mx-auto space-y-8">
    
    <div class="flex justify-between items-center">
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase mb-1">Project Management</div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $project->name }}</h2>
            <span class="text-sm text-gray-500">Using Library: {{ $project->costLibrary->name ?? 'None' }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded text-sm hover:bg-gray-50">Back</a>
            <a href="{{ route('project-dashboard', $project->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm font-bold hover:bg-indigo-700 shadow">
                Open 3D Dashboard
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" wire:poll.2s>
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
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $file->name }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($file->status == 'processing')
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded animate-pulse">Processing...</span>
                            @elseif($file->status == 'ready')
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Ready</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded cursor-help" title="{{ $file->error_message }}">Error</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $file->elements()->count() }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="deleteFile({{ $file->id }})" class="text-red-500 hover:text-red-700 text-xs">Unlink</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                            No files linked yet. Add one from Autodesk below.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-700 mb-4">Add File from Autodesk Construction Cloud</h3>
        
        <livewire:acc-project-browser />
        
    </div>
</div>