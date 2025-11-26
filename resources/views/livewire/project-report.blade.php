<div class="min-h-screen bg-gray-100 p-8 font-serif">
    
    <style>
        @media print {
            body * { visibility: hidden; }
            #report-container, #report-container * { visibility: visible; }
            #report-container { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            /* Hide Laravel Sidebar/Nav */
            nav, header, aside { display: none !important; }
        }
    </style>

    <div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
            &larr; Back to Dashboard
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / Save PDF
        </button>
    </div>

    <div id="report-container" class="max-w-[210mm] mx-auto bg-white shadow-2xl p-12 min-h-[297mm] relative">
        
        <div class="border-b-4 border-double border-gray-800 pb-6 mb-8 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gray-900 text-white flex items-center justify-center rounded-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-widest">PT. KONSTRUKSI JAYA</h1>
                    <p class="text-sm text-gray-500">General Contractor & Engineering</p>
                    <p class="text-xs text-gray-400">Jl. Jendral Sudirman No. 1, Jakarta | +62 21 555 0199</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-800">ENGINEER'S ESTIMATE</h2>
                <p class="text-sm text-gray-500 font-mono">Doc No: EST-{{ $project->id }}-{{ date('Y') }}</p>
            </div>
        </div>

        <div class="mb-8 bg-gray-50 p-4 rounded border border-gray-200">
            <table class="w-full text-sm">
                <tr>
                    <td class="font-bold text-gray-500 w-32 py-1">Project Name</td>
                    <td class="font-bold text-gray-900">: {{ $project->name }}</td>
                    <td class="font-bold text-gray-500 w-24 text-right">Date</td>
                    <td class="text-right text-gray-900 w-32">: {{ $currentDate }}</td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-500 py-1">Cost Library</td>
                    <td class="text-gray-900">: {{ $project->costLibrary->name ?? '-' }}</td>
                    <td class="font-bold text-gray-500 text-right">Currency</td>
                    <td class="text-right text-gray-900">: IDR (Rupiah)</td>
                </tr>
            </table>
        </div>

        <div class="mb-10">
            <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-800 mb-4 pb-1">I. REKAPITULASI BIAYA</h3>
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-center w-12">NO</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">DIVISI PEKERJAAN</th>
                        <th class="border border-gray-300 px-4 py-2 text-right w-48">TOTAL (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($recapBoq as $div => $total)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $no++ }}</td>
                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $div }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="2" class="border border-gray-300 px-4 py-2 text-right">GRAND TOTAL (Exc. PPN)</td>
                        <td class="border border-gray-300 px-4 py-2 text-right text-lg">{{ number_format($totalCost, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="page-break"></div> <div>
            <h3 class="text-lg font-bold text-gray-800 border-b-2 border-gray-800 mb-4 pb-1">II. BILL OF QUANTITIES (DETAIL)</h3>
            
            <table class="w-full text-xs border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-2 py-2 text-left">DESCRIPTION</th>
                        <th class="border border-gray-300 px-2 py-2 text-center w-16">UNIT</th>
                        <th class="border border-gray-300 px-2 py-2 text-right w-20">VOL</th>
                        <th class="border border-gray-300 px-2 py-2 text-right w-24">UNIT PRICE</th>
                        <th class="border border-gray-300 px-2 py-2 text-right w-28">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedBoq as $division => $items)
                        <tr class="bg-gray-50 font-bold">
                            <td colspan="5" class="border border-gray-300 px-2 py-2 text-gray-700 uppercase tracking-wider">
                                {{ $division }}
                            </td>
                        </tr>

                        @foreach($items as $item)
                            <tr>
                                <td class="border border-gray-300 px-2 py-1 pl-6">
                                    {{ $item['name'] }}
                                    <span class="text-[10px] text-gray-400 ml-2 font-mono">({{ $item['code'] }})</span>
                                </td>
                                <td class="border border-gray-300 px-2 py-1 text-center">{{ $item['unit'] }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item['volume'], 2) }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                                <td class="border border-gray-300 px-2 py-1 text-right font-bold">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        <tr class="bg-gray-50">
                            <td colspan="4" class="border border-gray-300 px-2 py-1 text-right font-bold italic">Sub-Total {{ $division }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-right font-bold">{{ number_format($items->sum('total'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16 flex justify-between text-center text-sm avoid-break">
            <div>
                <p class="mb-20">Prepared By,</p>
                <p class="font-bold border-b border-black inline-block min-w-[150px]">{{ Auth::user()->name }}</p>
                <p>Cost Estimator</p>
            </div>
            <div>
                <p class="mb-20">Approved By,</p>
                <p class="font-bold border-b border-black inline-block min-w-[150px]">( ........................... )</p>
                <p>Project Manager</p>
            </div>
        </div>

    </div>
</div>