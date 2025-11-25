
<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 mx-auto max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">🔍 BIM Data Inspector</h2>
        <div wire:loading class="text-blue-600 text-xs font-medium animate-pulse">
            Loading data...
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Project</label>
            <select wire:model.live="projectId" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Select Project --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter by Type Name</label>
            <select wire:model.live="filterTypeName" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                @foreach($uniqueTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Keyword</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Free text search..." 
                       class="w-full pl-10 border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    @if($projectId && isset($elements))
        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-100 text-gray-700 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Type Name / Family</th>
                        <th class="px-6 py-3">Assembly Code</th>
                        <th class="px-6 py-3 text-right">Volume (m³)</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($elements as $el)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 text-sm">
                                        {{ \Illuminate\Support\Str::beforeLast($el->name, '[') }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $el->category }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @if($el->assembly_code)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        {{ $el->assembly_code }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200">Empty</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-gray-700">
                                {{ $el->volume > 0 ? number_format($el->volume, 2) : '-' }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <button wire:click="inspect({{ $el->id }})" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 px-3 py-1 rounded text-xs font-bold border border-indigo-200 transition">INSPECT</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">No elements match the filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">{{ $elements->links() }}</div>
    @elseif(!$projectId)
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <p class="text-gray-500">Select a project to inspect.</p>
        </div>
    @endif

    @if($showModal && $selectedElement)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-gray-800 px-6 py-4 border-b border-gray-700 flex justify-between items-center text-white">
                <div>
                    <h3 class="font-bold text-lg">{{ \Illuminate\Support\Str::beforeLast($selectedElement->name, '[') }}</h3>
                    <span class="text-xs text-gray-400 uppercase tracking-widest">{{ $selectedElement->category }}</span>
                </div>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto bg-gray-50 flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(is_array($selectedElement->raw_properties))
                        @foreach($selectedElement->raw_properties as $groupName => $props)
                            @if(is_array($props))
                                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <h4 class="font-bold text-blue-600 border-b pb-2 mb-3 text-xs uppercase tracking-wide">{{ $groupName }}</h4>
                                    <div class="space-y-2">
                                        @foreach($props as $key => $val)
                                            <div class="flex justify-between text-sm border-b border-gray-50 pb-1 last:border-0">
                                                <span class="text-gray-600 font-medium mr-2">{{ $key }}</span>
                                                <span class="text-gray-900 text-right select-all break-all max-w-[60%] font-mono text-xs">
                                                    @if(is_array($val)) {{ json_encode($val) }} @else {{ $val }} @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
