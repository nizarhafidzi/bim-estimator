<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span>📊</span> Validation Report
                </h2>
                <div class="flex items-center gap-2 text-sm mt-1">
                    <span class="text-gray-500">File:</span>
                    <span class="font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $file->name }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 shadow-sm transition">
                    Back to Dashboard
                </a>
                
                <a href="{{ route('compliance.report.print', $file->id) }}" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    Full Report
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Elements Checked</div>
                    <div class="text-4xl font-black text-gray-800">{{ number_format($stats['total']) }}</div>
                </div>
                <svg class="w-24 h-24 text-gray-50 absolute -right-4 -bottom-4 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>

            <div class="bg-emerald-50 p-6 rounded-xl border border-emerald-100 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm"></span> Compliant (Pass)
                    </div>
                    <div class="text-4xl font-black text-emerald-700">{{ number_format($stats['pass']) }}</div>
                </div>
                @if($stats['total'] > 0)
                    <div class="w-full bg-emerald-200/50 rounded-full h-1.5 mt-4 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ ($stats['pass']/$stats['total'])*100 }}%"></div>
                    </div>
                    <p class="text-[10px] text-emerald-600 mt-1 font-bold">{{ round(($stats['pass']/$stats['total'])*100, 1) }}% Success Rate</p>
                @endif
            </div>

            <div class="bg-red-50 p-6 rounded-xl border border-red-100 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-sm"></span> Issues Found (Fail)
                    </div>
                    <div class="text-4xl font-black text-red-700">{{ number_format($stats['fail']) }}</div>
                </div>
                @if($stats['total'] > 0)
                    <div class="w-full bg-red-200/50 rounded-full h-1.5 mt-4 overflow-hidden">
                        <div class="bg-red-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ ($stats['fail']/$stats['total'])*100 }}%"></div>
                    </div>
                    <p class="text-[10px] text-red-600 mt-1 font-bold">{{ round(($stats['fail']/$stats['total'])*100, 1) }}% Failure Rate</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                
                <div class="flex bg-white p-1 rounded-lg border border-gray-300 shadow-sm">
                    <button wire:click="$set('filterStatus', 'fail')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'fail' ? 'bg-red-50 text-red-600 border border-red-100' : 'text-gray-500 hover:bg-gray-50' }}">
                        Fail Only
                    </button>
                    <button wire:click="$set('filterStatus', 'pass')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'pass' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'text-gray-500 hover:bg-gray-50' }}">
                        Pass Only
                    </button>
                    <button wire:click="$set('filterStatus', 'all')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'all' ? 'bg-gray-100 text-gray-800 border border-gray-200' : 'text-gray-500 hover:bg-gray-50' }}">
                        Show All
                    </button>
                </div>

                <div class="relative w-full md:w-64">
                    <input type="text" wire:model.live.debounce.300ms="searchRule" placeholder="Search element or rule..." class="w-full pl-9 text-xs border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm py-2">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Element Info</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Rule Logic</th>
                            <th class="px-6 py-4">Findings</th>
                            <th class="px-6 py-4 text-center">Visual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($results as $res)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($res->status == 'fail')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            FAIL
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            PASS
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800 text-sm">{{ $res->element->name ?? 'Unknown Element' }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-1 flex items-center gap-1" title="Revit External ID">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        {{ \Illuminate\Support\Str::limit($res->element->external_id, 12) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200 font-bold uppercase tracking-wide">
                                        {{ $res->rule->category_target ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-500 mb-1">Check Parameter:</div>
                                    <div class="font-bold text-indigo-600 text-xs">{{ $res->rule->parameter }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-1 border border-gray-200 rounded px-1.5 py-0.5 inline-block bg-white">
                                        {{ $res->rule->operator }} {{ $res->rule->value }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-xs text-gray-400">Actual Value:</div>
                                        <div class="font-mono text-sm font-bold {{ $res->status == 'fail' ? 'text-red-600' : 'text-emerald-600' }}">
                                            "{{ $res->actual_value }}"
                                        </div>
                                        @if($res->status == 'fail')
                                            <div class="text-[10px] text-red-500 italic mt-1 max-w-xs leading-tight">
                                                {{ $res->message }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button onclick="open3DModal('{{ $res->element->external_id }}', '{{ $res->status }}')" 
                                            class="p-2 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition shadow-sm text-slate-400"
                                            title="View Element in 3D">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2 1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-400 italic bg-gray-50/30">
                                    No validation results found matching criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $results->links() }}
            </div>
        </div>

    </div>

    @include('livewire.compliance.partials.viewer-modal')
    
</div>