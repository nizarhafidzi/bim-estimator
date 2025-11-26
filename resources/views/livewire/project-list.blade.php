<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">My Projects</h2>
        <button wire:click="$set('showCreateModal', true)" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700">+ New Project</button>
    </div>

    <div class="overflow-hidden border rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs border-b">
                <tr>
                    <th class="px-6 py-3 w-10"></th> <th class="px-6 py-3">Project Name</th>
                    <th class="px-6 py-3">Library</th>
                    <th class="px-6 py-3 text-center">Files</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" x-data="{ expandedProject: null }">
                @foreach($projects as $p)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" @click="expandedProject === {{ $p->id }} ? expandedProject = null : expandedProject = {{ $p->id }}">
                        <td class="px-6 py-4 text-gray-400 text-center">
                            <svg class="w-4 h-4 transition-transform duration-200" 
                                 :class="expandedProject === {{ $p->id }} ? 'rotate-90 text-blue-600' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $p->costLibrary->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">{{ $p->files_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2" @click.stop>
                            <a href="{{ route('project-report', $p->id) }}" class="text-emerald-600 hover:bg-emerald-50 p-2 rounded border border-emerald-200 text-xs font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Report
                            </a>
                            <a href="{{ route('project.files', $p->id) }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded border border-blue-200 text-xs font-bold">Manage Files</a>
                            <button wire:click="deleteProject({{ $p->id }})" class="text-red-500 hover:bg-red-50 p-2 rounded border border-red-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </td>
                    </tr>

                    <tr x-show="expandedProject === {{ $p->id }}" x-cloak class="bg-gray-50/50 border-b-2 border-blue-100">
                        <td colspan="5" class="px-6 py-4 pl-16">
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Linked Revit Files</h4>
                                
                                @if($p->files->isEmpty())
                                    <div class="text-sm text-gray-400 italic">No files linked. Click "Manage Files" to add.</div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($p->files as $file)
                                            <div class="bg-white border border-gray-200 p-3 rounded-lg shadow-sm flex justify-between items-center">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center rounded font-bold text-xs">RVT</div>
                                                    <div>
                                                        <div class="font-bold text-sm text-gray-800">{{ $file->name }}</div>
                                                        <div class="text-xs text-gray-500">Status: {{ ucfirst($file->status) }}</div>
                                                    </div>
                                                </div>
                                                
                                                @if($file->status == 'ready')
                                                    <a href="{{ route('file-dashboard', $file->id) }}" 
                                                       class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded hover:bg-indigo-700 font-bold shadow flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2 1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                                                        Open 3D
                                                    </a>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Processing...</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-xl shadow-xl w-96">
                <h3 class="font-bold text-lg mb-4">Create New Project</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Project Name</label>
                        <input type="text" wire:model="newName" class="w-full border-gray-300 rounded text-sm" placeholder="e.g. Apartemen Tower A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Cost Library</label>
                        <select wire:model="newLibraryId" class="w-full border-gray-300 rounded text-sm">
                            <option value="">-- Select Standard --</option>
                            @foreach($libraries as $lib)
                                <option value="{{ $lib->id }}">{{ $lib->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-100 rounded">Cancel</button>
                    <button wire:click="createProject" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700">Create</button>
                </div>
            </div>
        </div>
    @endif
</div>