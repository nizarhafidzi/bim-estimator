<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📏 Compliance Rules</h2>
            <p class="text-sm text-gray-500">Project: {{ $project->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">Back to Dashboard</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <div class="md:col-span-1 space-y-4">
            
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                <h3 class="font-bold text-indigo-900 mb-2 text-sm uppercase">Run Validation</h3>
                
                <div class="space-y-2">
                    <label class="text-xs text-indigo-700 block">Select Target File:</label>
                    <select wire:model="targetFileId" class="w-full text-xs border-indigo-200 rounded mb-2">
                        <option value="">-- Choose File --</option>
                        @foreach($project->files as $file)
                            <option value="{{ $file->id }}">{{ $file->name }}</option>
                        @endforeach
                    </select>

                    <button wire:click="runValidation" wire:loading.attr="disabled" class="w-full bg-indigo-600 text-white text-xs font-bold py-2 rounded hover:bg-indigo-700 shadow-md flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="runValidation">▶ RUN CHECK</span>
                        @if($targetFileId)
                            <a href="{{ route('compliance.dashboard', $targetFileId) }}" class="block mt-3 text-center text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                Open Visual Dashboard &rarr;
                            </a>
                        @endif
                        <span wire:loading wire:target="runValidation">Running...</span>
                    </button>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-700">Rule Sets</h3>
                    <button wire:click="$set('showCreateModal', true)" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">+ New</button>
                </div>
                
                <div class="space-y-2">
                    @forelse($ruleSets as $set)
                        <div class="flex justify-between items-center p-2 rounded cursor-pointer text-sm {{ $activeRuleSetId == $set->id ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200' : 'hover:bg-gray-50 text-gray-600' }}"
                             wire:click="$set('activeRuleSetId', {{ $set->id }})">
                            <span class="truncate">{{ $set->name }}</span>
                            <button wire:click.stop="deleteRuleSet({{ $set->id }})" class="text-gray-400 hover:text-red-500">&times;</button>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 text-center py-4">No Rule Sets</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="md:col-span-3">
            @if($activeRuleSetId)
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase">Import Rules</h4>
                        <form wire:submit.prevent="importRules" class="flex items-center gap-2">
                            <input type="file" wire:model="excelFile" class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <button type="submit" wire:loading.attr="disabled" class="bg-green-600 text-white px-3 py-2 rounded-lg text-xs font-bold hover:bg-green-700 shadow-sm">
                                Import
                            </button>
                             <button type="button" wire:click="downloadTemplate" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-xs font-bold hover:bg-gray-200 shadow-sm border border-gray-300">
                                Template
                            </button>
                        </form>
                        @error('excelFile') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm flex items-center gap-1">
                        <span>+ Add Rule Manual</span>
                    </button>
                </div>
                
                @if(session('message')) <div class="mb-4 text-green-600 text-sm font-bold bg-green-50 p-2 rounded border border-green-100">{{ session('message') }}</div> @endif
                @if(session('error')) <div class="mb-4 text-red-600 text-sm font-bold bg-red-50 p-2 rounded border border-red-100">{{ session('error') }}</div> @endif

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Parameter</th>
                                <th class="px-4 py-3 text-center">Op</th>
                                <th class="px-4 py-3">Value</th>
                                <th class="px-4 py-3">Severity</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($rules as $rule)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium">{{ $rule->category_target }}</td>
                                    <td class="px-4 py-2 font-mono text-blue-600 text-xs">{{ $rule->parameter }}</td>
                                    <td class="px-4 py-2 text-center"><span class="bg-gray-100 px-2 py-1 rounded font-bold text-xs">{{ $rule->operator }}</span></td>
                                    <td class="px-4 py-2 font-mono">{{ $rule->value }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $rule->severity == 'error' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $rule->severity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-500 italic text-xs">{{ $rule->description }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <button wire:click="editRule({{ $rule->id }})" class="text-blue-600 hover:underline text-xs mr-2">Edit</button>
                                        <button wire:click="deleteRule({{ $rule->id }})" class="text-red-600 hover:underline text-xs" wire:confirm="Delete rule?">Del</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">No rules found. Import Excel or Add Manually.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex items-center justify-center h-64 border-2 border-dashed border-gray-300 rounded-xl text-gray-400 bg-gray-50">
                    Select or Create a Rule Set to view/manage rules.
                </div>
            @endif
        </div>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-xl shadow-2xl w-96">
                <h3 class="font-bold text-lg mb-4">New Rule Set</h3>
                <input type="text" wire:model="name" placeholder="Set Name (e.g. Fire Safety)" class="w-full border p-2 rounded mb-2 text-sm">
                <textarea wire:model="description" placeholder="Description" class="w-full border p-2 rounded mb-4 text-sm"></textarea>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="px-3 py-1 bg-gray-100 rounded text-sm hover:bg-gray-200">Cancel</button>
                    <button wire:click="createRuleSet" class="px-3 py-1 bg-blue-600 text-white rounded text-sm font-bold hover:bg-blue-700">Create</button>
                </div>
            </div>
        </div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800">{{ $isEditing ? 'Edit Rule' : 'Add New Rule' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Category</label>
                            <input type="text" wire:model="category" class="w-full border-gray-300 rounded-lg text-sm" placeholder="e.g. Walls">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Parameter</label>
                            <input type="text" wire:model="param" class="w-full border-gray-300 rounded-lg text-sm" placeholder="e.g. Fire Rating">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Operator</label>
                            <select wire:model="operator" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Select</option>
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<"><</option>
                                <option value=">=">>=</option>
                                <option value="<="><=</option>
                                <option value="contains">Contains</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Value</label>
                            <input type="text" wire:model="val" class="w-full border-gray-300 rounded-lg text-sm" placeholder="e.g. 2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                        <textarea wire:model="desc" rows="2" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Error message..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg">Cancel</button>
                    <button wire:click="saveRule" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">
                        {{ $isEditing ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>