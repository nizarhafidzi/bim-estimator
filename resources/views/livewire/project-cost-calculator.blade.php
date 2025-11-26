<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 min-h-screen mx-auto max-w-7xl">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">💰 Project Cost Calculator</h2>
        <div wire:loading class="text-blue-600 text-xs font-medium animate-pulse">
            Processing...
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">1. Select Project</label>
                <select wire:model.live="projectId" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Choose Project --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">2. Select Cost Library</label>
                <select wire:model.live="selectedLibraryId" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" @if(!$projectId) disabled @endif>
                    <option value="">-- Choose Standard/AHSP --</option>
                    @foreach($libraries as $lib)
                        <option value="{{ $lib->id }}">{{ $lib->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button wire:click="exportExcel" 
                        @if(!$summary) disabled @endif
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg font-bold text-sm hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>

                <button wire:click="calculateNow" wire:loading.attr="disabled" 
                        @if(!$projectId || !$selectedLibraryId) disabled @endif
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-bold text-sm hover:bg-black transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                    
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    
                    <span wire:loading.remove>Calculate</span>
                    <span wire:loading class="text-xs">Working...</span>
                </button>
            </div>
        </div>
    </div>

    @if($summary)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="relative overflow-hidden rounded-2xl shadow-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-6">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
                <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-20 h-20 rounded-full bg-white/10 blur-lg"></div>
                
                <div class="relative z-10">
                    <div class="text-blue-100 text-sm font-semibold uppercase tracking-wider mb-2">Total Estimated Cost</div>
                    <div class="text-3xl lg:text-4xl font-extrabold tracking-tight">
                        Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-blue-200 bg-blue-800/30 w-fit px-3 py-1 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Calculation Successful
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <div class="text-gray-500 text-xs font-bold uppercase">Matched Elements</div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800">{{ $summary['matched'] }}</div>
                </div>
                <div class="mt-4 text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded w-fit font-medium">
                    Ready for purchasing
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <div class="text-gray-500 text-xs font-bold uppercase">Unassigned Elements</div>
                    </div>
                    <div class="text-3xl font-bold text-gray-800">{{ $summary['unassigned'] }}</div>
                </div>
                <div class="mt-4 text-xs text-red-600 bg-red-50 px-2 py-1 rounded w-fit font-medium">
                    Needs Master Price
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg overflow-hidden shadow-sm mt-8">
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Bill of Quantities (BOQ)</h3>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-bold">Grouped by Division</span>
            </div>
            
            <div class="overflow-x-auto max-h-[600px]">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 w-1/2">Description of Work</th>
                            <th class="px-6 py-3 text-center">Unit</th>
                            <th class="px-6 py-3 text-right">Volume</th>
                            <th class="px-6 py-3 text-right">Unit Price</th>
                            <th class="px-6 py-3 text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($groupedResults as $division => $items)
                            <tr class="bg-gray-50/80">
                                <td colspan="5" class="px-6 py-3 font-bold text-gray-800 uppercase tracking-wide text-xs border-t border-b border-gray-200">
                                    📂 {{ $division }}
                                </td>
                            </tr>

                            @foreach($items as $item)
                                <tr class="hover:bg-blue-50 transition-colors group">
                                    <td class="px-6 py-3 pl-10">
                                        <div class="font-medium text-gray-900">{{ $item['name'] }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono flex gap-2">
                                            <span class="bg-gray-100 px-1 rounded border border-gray-200">{{ $item['code'] }}</span>
                                            <span>({{ $item['count'] }} elements)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-500">{{ $item['unit'] }}</td>
                                    <td class="px-6 py-3 text-right font-mono text-gray-700">{{ number_format($item['volume'], 2) }}</td>
                                    <td class="px-6 py-3 text-right font-mono text-gray-600">
                                        {{ number_format($item['unit_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900 font-mono">
                                        {{ number_format($item['total_price'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            
                            <tr class="bg-blue-50/30">
                                <td colspan="4" class="px-6 py-2 text-right text-xs font-bold text-blue-800 uppercase">
                                    Sub-Total {{ $division }}
                                </td>
                                <td class="px-6 py-2 text-right font-bold text-blue-800 border-t border-blue-100">
                                    {{ number_format($items->sum('total_price'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        @if($unassignedCount > 0)
                            <tr class="bg-red-50/50">
                                <td colspan="5" class="px-6 py-3 font-bold text-red-800 uppercase tracking-wide text-xs border-t border-red-200">
                                    ⚠️ Unassigned Items
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500 italic text-sm">
                                        <svg class="w-8 h-8 text-red-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <p>{{ $unassignedCount }} items found in model but not in the selected Library.</p>
                                        <p class="text-xs mt-1">Check "Data Inspector" to see their Assembly Codes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>