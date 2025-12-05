<div class="min-h-screen bg-gray-100 p-8 font-serif">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
        body, .font-serif { font-family: 'Times New Roman', Times, serif !important; color: #000; }
        
        /* Print Config */
        @media print {
            @page { margin: 10mm 15mm; size: A4; }
            body * { visibility: hidden; }
            #report-container, #report-container * { visibility: visible; }
            #report-container { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; box-shadow: none; }
            .no-print { display: none !important; }
            nav, header, aside { display: none !important; }
            .page-break { page-break-before: always; }
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
            
            /* Table Borders for Print */
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid black; padding: 4px; font-size: 10pt; }
            th { background-color: #f3f4f6 !important; font-weight: bold; text-align: center; }
        }
        
        /* Screen Table Style */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #eee; }
    </style>

    <div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center no-print font-sans">
        <div class="flex gap-3">
            <a href="{{ route('compliance.dashboard', $fileId) }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
                &larr; Back to Dashboard
            </a>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportExcel" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-bold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print PDF
            </button>
        </div>
    </div>

    <div id="report-container" class="max-w-[210mm] mx-auto bg-white shadow-2xl p-10 min-h-[297mm] relative">
        
        <div class="flex items-center justify-between border-b-4 border-double border-black pb-2 mb-6">
            <div class="w-24 h-16 flex items-center justify-start">
                <img src="{{ asset('images/logo-perusahaan.png') }}" alt="Logo" class="h-full object-contain">
            </div>
            <div class="flex-1 text-center px-2">
                <h1 class="text-xl font-bold text-[#FF0000] uppercase tracking-wide mb-1">PT BUANA ENJINIRING KONSULTAN</h1>
                <p class="text-xs text-black leading-tight">Jl. Mayjen DI Panjaitan Kav 12, Jakarta Timur 13340</p>
            </div>
            <div class="w-24 h-16"></div>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-black uppercase underline">COMPLIANCE CHECK REPORT</h2>
            <p class="text-sm font-bold">File Model: {{ $file->name }}</p>
        </div>

        <table class="text-sm w-full border-none mb-6" style="border: none !important;">
            <tr>
                <td style="border: none; width: 100px;"><strong>Project</strong></td>
                <td style="border: none;">: {{ $project->name }}</td>
                <td style="border: none; width: 80px;"><strong>Date</strong></td>
                <td style="border: none;">: {{ $currentDate }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Elements</strong></td>
                <td style="border: none;">: {{ $stats['total'] }} items</td>
                <td style="border: none;"><strong>Score</strong></td>
                <td style="border: none;">: <strong>{{ $stats['score'] }}%</strong> Compliant</td>
            </tr>
        </table>

        <div class="mb-6 border border-black p-4 bg-gray-50">
            <h3 class="text-sm font-bold uppercase mb-2">Executive Summary</h3>
            <div class="flex justify-between text-sm">
                <div><span class="font-bold text-green-700">PASS:</span> {{ $stats['pass'] }} items</div>
                <div><span class="font-bold text-red-700">FAIL:</span> {{ $stats['fail'] }} items</div>
                <div><span class="font-bold">TOTAL:</span> {{ $stats['total'] }} items</div>
            </div>
        </div>

        <div class="text-xs">
            <h3 class="text-sm font-bold uppercase mb-2">Detailed Validation Results</h3>
            
            @foreach($groupedResults as $category => $items)
                <div class="mb-4 break-inside-avoid">
                    <div class="bg-gray-200 px-2 py-1 font-bold border border-black border-b-0">{{ $category }}</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30px;">STS</th>
                                <th>Element ID / Name</th>
                                <th>Rule Check</th>
                                <th>Actual Value</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="{{ $item->status == 'fail' ? 'bg-red-50' : '' }}">
                                    <td style="text-align: center; font-weight: bold; color: {{ $item->status=='fail'?'red':'green' }}">
                                        {{ strtoupper($item->status) }}
                                    </td>
                                    <td>
                                        {{ $item->element->name ?? 'Unknown' }} <br>
                                        <span style="font-size: 8px; color: #666;">ID: {{ $item->element->external_id }}</span>
                                    </td>
                                    <td>
                                        {{ $item->rule->parameter }} {{ $item->rule->operator }} {{ $item->rule->value }}
                                    </td>
                                    <td style="font-family: monospace;">{{ $item->actual_value }}</td>
                                    <td style="font-style: italic; color: #555;">{{ $item->message }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-end text-center text-sm avoid-break">
            <div style="width: 200px;">
                <p class="mb-16">Verified By,</p>
                <p class="font-bold border-b border-black">{{ Auth::user()->name }}</p>
                <p>BIM Manager</p>
            </div>
        </div>

    </div>
</div>