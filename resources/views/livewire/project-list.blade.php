<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">My Projects</h2>
        <button wire:click="$set('showCreateModal', true)" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Project
        </button>
    </div>

    <div class="overflow-hidden border rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs border-b">
                <tr>
                    <th class="px-6 py-3 w-10"></th> 
                    <th class="px-6 py-3">Project Name</th>
                    <th class="px-6 py-3">Library</th>
                    <th class="px-6 py-3 text-center">Files</th>
                    <th class="px-6 py-3 text-right">Modules & Actions</th>
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
                        
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 text-base">{{ $p->name }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $p->created_at->format('d M Y') }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs border border-gray-200">
                                {{ $p->costLibrary->name ?? 'No Library' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full">
                                {{ $p->files_count }} Files
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right" @click.stop>
                            <div class="flex justify-end gap-2">
                                
                                <div class="group relative">
                                    <a href="{{ route('cost-calculator') }}" class="flex items-center gap-1 bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 px-3 py-1.5 rounded-md text-xs font-bold transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cost
                                    </a>
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block px-2 py-1 bg-gray-800 text-white text-[10px] rounded whitespace-nowrap z-10">Open Estimator</span>
                                </div>

                                <div class="group relative">
                                    <a href="{{ route('compliance.rules', $p->id) }}" class="flex items-center gap-1 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-md text-xs font-bold transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Check
                                    </a>
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block px-2 py-1 bg-gray-800 text-white text-[10px] rounded whitespace-nowrap z-10">Open Compliance</span>
                                </div>

                                <div class="w-px bg-gray-200 mx-1"></div>

                                <a href="{{ route('project.files', $p->id) }}" class="text-gray-500 hover:text-blue-600 p-1.5 rounded hover:bg-gray-100" title="Manage Files">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </a>

                                <button wire:click="deleteProject({{ $p->id }})" class="text-gray-400 hover:text-red-500 p-1.5 rounded hover:bg-red-50" title="Delete Project">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr x-show="expandedProject === {{ $p->id }}" x-cloak class="bg-gray-50/50 border-b border-blue-100 shadow-inner">
                        <td colspan="5" class="px-6 py-4 pl-16">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Linked Revit Models</h4>
                                    <a href="{{ route('project.files', $p->id) }}" class="text-xs text-blue-600 hover:underline font-medium">Add / Manage Files &rarr;</a>
                                </div>
                                
                                @if($p->files->isEmpty())
                                    <div class="text-sm text-gray-400 italic border-2 border-dashed border-gray-200 rounded-lg p-4 text-center">
                                        No files linked yet. Go to "Manage Files" to import from Autodesk.
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($p->files as $file)
                                            <div class="bg-white border border-gray-200 p-3 rounded-lg shadow-sm flex justify-between items-center hover:border-blue-300 transition">
                                                <div class="flex items-center gap-3 overflow-hidden">
                                                    <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center rounded font-bold text-[10px]">RVT</div>
                                                    <div class="truncate">
                                                        <div class="font-bold text-sm text-gray-800 truncate w-40" title="{{ $file->name }}">{{ $file->name }}</div>
                                                        <div class="text-[10px] text-gray-500 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $file->status == 'ready' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                                            {{ ucfirst($file->status) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($file->status == 'ready')
                                                    <div class="flex gap-1">
                                                        <a href="{{ route('file-dashboard', $file->id) }}" class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded hover:bg-emerald-100 font-bold" title="3D Cost View">
                                                            Cost View
                                                        </a>
                                                        <a href="{{ route('compliance.dashboard', $file->id) }}" class="text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-1 rounded hover:bg-indigo-100 font-bold" title="3D Check View">
                                                            QC View
                                                        </a>
                                                    </div>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-xl shadow-xl w-96 animate-fade-in-up">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Create New Project</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Project Name</label>
                        <input type="text" wire:model="newName" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Apartemen Tower A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cost Library</label>
                        <select wire:model="newLibraryId" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select Standard --</option>
                            @foreach($libraries as $lib)
                                <option value="{{ $lib->id }}">{{ $lib->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">*Used for Cost Estimation module</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-gray-600 text-sm hover:bg-gray-100 rounded font-medium">Cancel</button>
                    <button wire:click="createProject" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700 shadow-sm">Create Project</button>
                </div>
            </div>
        </div>
    @endif
</div>