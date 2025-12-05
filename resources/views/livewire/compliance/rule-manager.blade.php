<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </span>
                    Compliance Rules Manager
                </h2>
                <p class="text-sm text-gray-500 mt-1">Project: <span class="font-semibold text-gray-800">{{ $project->name }}</span></p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100 shadow-sm">
                    <h3 class="font-bold text-indigo-900 mb-3 text-sm uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Run Validation
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-bold text-indigo-400 uppercase block mb-1">Select Target File</label>
                            <select wire:model.live="targetFileId" class="w-full text-sm border-indigo-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                <option value="">-- Choose Revit File --</option>
                                @foreach($project->files as $file)
                                    <option value="{{ $file->id }}">{{ $file->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button wire:click="runValidation" wire:loading.attr="disabled" 
                            class="w-full bg-indigo-600 text-white text-sm font-bold py-2.5 rounded-lg hover:bg-indigo-700 shadow-md flex justify-center items-center gap-2 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="runValidation">RUN NEW CHECK</span>
                            <span wire:loading wire:target="runValidation">Processing...</span>
                        </button>
                        
                        @if($hasResults)
                            <div class="mt-3 pt-3 border-t border-indigo-200 animate-fade-in-up">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold text-indigo-400 uppercase">Last Checked</span>
                                    <span class="text-[10px] font-bold text-indigo-800">{{ $lastCheckDate }}</span>
                                </div>
                                
                                <a href="{{ route('compliance.dashboard', $targetFileId) }}" class="block w-full bg-white border border-indigo-200 text-indigo-700 text-xs font-bold py-2 rounded-lg text-center hover:bg-indigo-50 shadow-sm flex items-center justify-center gap-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Open Visual Dashboard
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 h-fit">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wide">Rule Sets</h3>
                        <button wire:click="$set('showCreateModal', true)" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200 font-bold transition">+ New</button>
                    </div>
                    
                    <div class="space-y-1 max-h-80 overflow-y-auto pr-1 custom-scrollbar">
                        @forelse($ruleSets as $set)
                            <div class="flex justify-between items-center p-2.5 rounded-lg cursor-pointer text-sm transition group {{ $activeRuleSetId == $set->id ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200 shadow-sm' : 'hover:bg-gray-50 text-gray-600 border border-transparent' }}"
                                 wire:click="$set('activeRuleSetId', {{ $set->id }})">
                                <span class="truncate">{{ $set->name }}</span>
                                <button wire:click.stop="deleteRuleSet({{ $set->id }})" class="text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition" title="Delete Set">&times;</button>
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 text-center py-8 border-2 border-dashed border-gray-100 rounded-lg">No Rule Sets found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                @if($activeRuleSetId)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            
                            <div class="w-full sm:w-auto bg-gray-50 p-2 rounded-lg border border-gray-200 flex flex-col sm:flex-row gap-2 items-center">
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="button" wire:click="downloadTemplate" class="bg-white border border-gray-300 text-gray-600 px-3 py-2 rounded text-xs font-bold hover:bg-gray-50 transition flex items-center gap-1 shadow-sm" title="Download Excel Template">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Template
                                    </button>
                                    <form wire:submit.prevent="importRules" class="flex items-center gap-2 flex-1">
                                        <input type="file" wire:model="excelFile" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                                        <button type="submit" wire:loading.attr="disabled" class="bg-green-600 text-white px-3 py-2 rounded text-xs font-bold hover:bg-green-700 shadow-sm transition flex items-center gap-1">
                                            <span>Upload</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @error('excelFile') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror

                            <button wire:click="openModal" class="w-full sm:w-auto bg-gray-900 text-white px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-black shadow-lg transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Rule Manually
                            </button>
                        </div>

                        @if(session('message')) 
                            <div class="mt-4 p-3 bg-green-50 text-green-700 text-sm rounded-lg border border-green-200 flex items-center gap-2 font-medium animate-fade-in-down">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ session('message') }}
                            </div> 
                        @endif
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3">Category</th>
                                        <th class="px-6 py-3">Parameter</th>
                                        <th class="px-6 py-3 text-center">Logic</th>
                                        <th class="px-6 py-3">Value</th>
                                        <th class="px-6 py-3">Severity</th>
                                        <th class="px-6 py-3">Description</th>
                                        <th class="px-6 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($rules as $rule)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-3 font-medium text-gray-800">{{ $rule->category_target }}</td>
                                            <td class="px-6 py-3 font-mono text-blue-600 text-xs bg-blue-50/50 rounded px-2">{{ $rule->parameter }}</td>
                                            <td class="px-6 py-3 text-center">
                                                <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-bold text-[10px] border border-gray-200">{{ $rule->operator }}</span>
                                            </td>
                                            <td class="px-6 py-3 font-mono font-bold text-gray-800">{{ $rule->value }}</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] uppercase font-bold border {{ $rule->severity == 'error' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-yellow-50 text-yellow-700 border-yellow-100' }}">
                                                    {{ $rule->severity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-gray-500 italic text-xs max-w-xs truncate">{{ $rule->description }}</td>
                                            <td class="px-6 py-3 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <button wire:click="editRule({{ $rule->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-100 px-2 py-1 rounded hover:bg-blue-50 transition">Edit</button>
                                                    <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="Delete rule?" class="text-red-500 hover:text-red-700 font-bold text-xs border border-red-100 px-2 py-1 rounded hover:bg-red-50 transition">Del</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-16 text-center text-gray-400 italic bg-gray-50/30">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                No rules found in this set.<br>Import Excel or Add Manually to start checking models.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-96 border-2 border-dashed border-gray-300 rounded-xl text-gray-400 bg-gray-50">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-lg font-medium text-gray-500">No Rule Set Selected</p>
                        <p class="text-sm mt-2">Select a Rule Set from the sidebar to view details.</p>
                    </div>
                @endif
            </div>
        </div>

        @if($showCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md animate-fade-in-up">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-800">New Rule Set</h3>
                        <button wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Name</label>
                            <input type="text" wire:model="name" placeholder="e.g. Fire Safety Standard" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                            <textarea wire:model="description" placeholder="Optional description..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Cancel</button>
                        <button wire:click="createRuleSet" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-md transition transform active:scale-95">Create</button>
                    </div>
                </div>
            </div>
        @endif

        @if($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">{{ $isEditing ? 'Edit Rule' : 'Add New Rule' }}</h3>
                        <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Revit Category</label>
                                <input type="text" wire:model="category" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="e.g. Walls">
                                @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Parameter Name</label>
                                <input type="text" wire:model="param" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="e.g. Fire Rating">
                                @error('param') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Operator</label>
                                <select wire:model="operator" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                                    <option value="">Select</option>
                                    <option value="=">=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
                                    <option value=">=">>=</option>
                                    <option value="<="><=</option>
                                    <option value="contains">Contains</option>
                                    <option value="not_contains">Not Contains</option>
                                </select>
                                @error('operator') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Value</label>
                                <input type="text" wire:model="val" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="e.g. 2">
                                @error('val') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fail Message / Desc</label>
                            <textarea wire:model="desc" rows="2" class="w-full border-gray-300 rounded-lg text-sm shadow-sm" placeholder="Error message shown in report..."></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                        <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button wire:click="saveRule" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-md transition transform active:scale-95">
                            {{ $isEditing ? 'Update Rule' : 'Save Rule' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>