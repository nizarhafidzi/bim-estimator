<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 min-h-screen mx-auto max-w-7xl">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">💰 Project Cost Calculator</h2>
        <div wire:loading class="text-blue-600 text-xs font-medium animate-pulse">
            Processing...
        </div>
    </div>

    <div class="bg-gray-50 p-6 rounded-lg mb-8 flex flex-col md:flex-row gap-4 items-end border border-gray-200">
        <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Select Project to Estimate</label>
            <select wire:model.live="projectId" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">-- Choose Project --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        @if($projectId)
            <button wire:click="calculateNow" wire:loading.attr="disabled" 
                class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 shadow-lg flex items-center gap-2 disabled:opacity-50 transition-all text-sm">
                
                <svg wire:loading wire:target="calculateNow" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <span>Calculate Cost</span>
            </button>
        @endif
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
                <h3 class="font-bold text-gray-700">Cost Breakdown (Bill of Quantities)</h3>
            </div>
            
            <div class="overflow-x-auto max-h-[600px]">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3">Element Name</th>
                            <th class="px-6 py-3">Assembly Code</th>
                            <th class="px-6 py-3 text-right">Volume (m3)</th>
                            <th class="px-6 py-3 text-right">Unit Price</th>
                            <th class="px-6 py-3 text-right">Total Cost</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($results as $res)
                            <tr class="hover:bg-blue-50 {{ $res->status == 'unassigned' ? 'bg-red-50/50' : '' }} transition-colors">
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-900">
                                        {{ \Illuminate\Support\Str::beforeLast($res->element->name ?? 'Unknown', '[') }}
                                    </div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $res->element->category ?? '-' }}</div>
                                </td>
                                
                                <td class="px-6 py-3 font-mono text-xs">
                                    @if($res->element->assembly_code)
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded border border-gray-200">
                                            {{ $res->element->assembly_code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-3 text-right text-gray-600 font-mono">
                                    {{ number_format($res->element->volume, 2) }}
                                </td>
                                
                                <td class="px-6 py-3 text-right text-gray-600 font-mono">
                                    Rp {{ number_format($res->unit_price_applied, 0, ',', '.') }}
                                </td>
                                
                                <td class="px-6 py-3 text-right font-bold text-gray-900 font-mono">
                                    Rp {{ number_format($res->total_cost, 0, ',', '.') }}
                                </td>
                                
                                <td class="px-6 py-3 text-center">
                                    @if($res->status == 'matched')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            Matched
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            Unassigned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>