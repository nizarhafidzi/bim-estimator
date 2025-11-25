<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 min-h-screen mx-auto max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">💰 Project Cost Calculator</h2>
        <div wire:loading class="text-blue-600 text-xs font-medium animate-pulse">Processing...</div>
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
                    @foreach($projects as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">2. Select Cost Library</label>
                <select wire:model.live="selectedLibraryId" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" {{ !$projectId ? 'disabled' : '' }}>
                    <option value="">-- Choose Standard/AHSP --</option>
                    @foreach($libraries as $lib) <option value="{{ $lib->id }}">{{ $lib->name }}</option> @endforeach
                </select>
            </div>

            <div>
                <button wire:click="calculateNow" wire:loading.attr="disabled" 
                    class="w-full bg-gray-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-black shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    {{ (!$projectId || !$selectedLibraryId) ? 'disabled' : '' }}>
                    <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span wire:loading.remove>Calculate BOQ</span>
                    <span wire:loading>Calculating...</span>
                </button>
            </div>
        </div>
    </div>

    @if($summary)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-indigo-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-blue-200 text-xs font-bold uppercase tracking-wider">Grand Total Estimate</div>
                    <div class="text-3xl font-extrabold mt-1">Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}</div>
                </div>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-2xl p-6 flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-xs font-bold uppercase">Matched Items</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $summary['matched'] }}</div>
                </div>
                <div class="bg-green-100 text-green-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-xs font-bold uppercase">Unassigned Items</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $summary['unassigned'] }}</div>
                </div>
                <div class="bg-red-100 text-red-700 p-2 rounded-full"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Bill of Quantities (BOQ)</h3>
                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">Grouped by Division</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 w-1/2">Description of Work</th>
                            <th class="px-6 py-3 text-center">Unit</th>
                            <th class="px-6 py-3 text-right">Volume</th>
                            <th class="px-6 py-3 text-right">Unit Price</th>
                            <th class="px-6 py-3 text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        
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
                                            <span class="bg-gray-100 px-1 rounded">{{ $item['code'] }}</span>
                                            <span>({{ $item['count'] }} elements)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-500">{{ $item['unit'] }}</td>
                                    <td class="px-6 py-3 text-right font-mono">{{ number_format($item['volume'], 2) }}</td>
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
                                    ⚠️ Unassigned Items (No Price Match)
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic text-sm">
                                    {{ $unassignedCount }} items found in model but not in the selected Cost Library. 
                                    <br>Check "Data Inspector" to see their Assembly Codes.
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>