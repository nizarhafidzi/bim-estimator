<div class="min-h-screen bg-gray-100 p-8">
    
    <style>
        /* Gunakan Times New Roman untuk seluruh halaman */
        body, .font-serif { 
            font-family: 'Times New Roman', Times, serif !important; 
            color: #000;
        }

        /* Style Tabel Cetak (Garis Hitam Tegas) */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 6px 8px; }
        th { background-color: #f3f4f6 !important; font-weight: bold; text-align: center; text-transform: uppercase; }
        
        /* Print Settings */
        @media print {
            @page { margin: 10mm 15mm; size: A4; }
            body * { visibility: hidden; }
            #report-container, #report-container * { visibility: visible; }
            #report-container { 
                position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; box-shadow: none; 
            }
            .no-print { display: none !important; }
            nav, header, aside { display: none !important; }
            .page-break { page-break-before: always; }
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
    </style>

    <div class="max-w-[210mm] mx-auto mb-6 flex justify-between items-center no-print font-sans">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
            &larr; Back to Dashboard
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / Save PDF
        </button>
    </div>

    <div id="report-container" class="max-w-[210mm] mx-auto bg-white shadow-2xl p-10 min-h-[297mm] relative">
        
        <div class="flex items-center justify-between border-b-4 border-double border-black pb-2 mb-6">
            <div class="w-28 h-20 flex items-center justify-start">
                <img src="{{ asset('images/logo-perusahaan.png') }}" alt="Logo" class="h-full object-contain">
            </div>

            <div class="flex-1 text-center px-2">
                <h1 class="text-2xl font-bold text-[#FF0000] uppercase tracking-wide mb-1" style="color: red;">
                    PT BUANA ENJINIRING KONSULTAN
                </h1>
                <p class="text-sm text-black leading-tight">
                    Jl. Mayjen DI Panjaitan Kav 12, RT.3/RW.11 Cipinang Cempedak, Jatinegara<br>
                    DKI Jakarta 13340, email : Admin@bek.co.id
                </p>
            </div>

            <div class="w-28 h-16 flex items-center justify-end">
                <img src="{{ asset('images/bim.png') }}" alt="Logo" class="h-full object-contain">
            </div>
        </div>

        <div class="mb-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-black uppercase underline">BILL Of QUANTITY</h2>
                <p class="text-sm font-bold">Proyek: {{ $project->name }}</p>
            </div>

            <table class="text-sm w-full border-none mb-4" style="border: none !important;">
                <tr>
                    <td style="border: none; width: 100px;"><strong>Nomor</strong></td>
                    <td style="border: none; width: 10px;">:</td>
                    <td style="border: none;">EST-{{ $project->id }}/{{ date('m/Y') }}</td>
                    <td style="border: none; width: 80px;"><strong>Tanggal</strong></td>
                    <td style="border: none; width: 10px;">:</td>
                    <td style="border: none;">{{ $currentDate }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Library</strong></td>
                    <td style="border: none;">:</td>
                    <td style="border: none;">{{ $project->costLibrary->name ?? '-' }}</td>
                    <td style="border: none;"><strong>Lokasi</strong></td>
                    <td style="border: none;">:</td>
                    <td style="border: none;">Jakarta</td>
                </tr>
            </table>
        </div>

        <div class="mb-10">
            <h3 class="text-base font-bold text-black uppercase mb-2">I. REKAPITULASI BIAYA</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th>URAIAN PEKERJAAN (DIVISI)</th>
                        <th style="width: 180px;">TOTAL HARGA (RP)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($recapBoq as $div => $total)
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td style="font-weight: bold;">{{ $div }}</td>
                            <td style="text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f3f4f6;">
                        <td colspan="2" style="text-align: right; font-weight: bold;">GRAND TOTAL (Exc. PPN)</td>
                        <td style="text-align: right; font-weight: bold; font-size: 16px;">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="page-break"></div> <div>
            <div class="border-b-2 border-black mb-4 pb-1 flex justify-between items-end">
                <span class="text-xs font-bold uppercase">Proyek: {{ $project->name }}</span>
                <span class="text-xs">Detail Rincian Biaya</span>
            </div>

            <h3 class="text-base font-bold text-black uppercase mb-2">II. RINCIAN VOLUME DAN BIAYA (DETAIL)</h3>
            
            <table style="font-size: 11px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">URAIAN PEKERJAAN</th>
                        <th style="width: 40px;">SAT</th>
                        <th style="width: 60px;">VOL</th>
                        <th style="width: 90px;">HARGA SATUAN</th>
                        <th style="width: 100px;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedBoq as $division => $items)
                        <tr style="background-color: #e5e7eb;">
                            <td colspan="5" style="font-weight: bold; text-transform: uppercase;">
                                {{ $division }}
                            </td>
                        </tr>

                        @foreach($items as $item)
                            <tr>
                                <td style="padding-left: 15px;">
                                    {{ $item['name'] }}
                                    <span style="font-size: 9px; color: #666;">({{ $item['code'] }})</span>
                                </td>
                                <td style="text-align: center;">{{ $item['unit'] }}</td>
                                <td style="text-align: right;">{{ number_format($item['volume'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: bold;">{{ number_format($item['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        <tr style="background-color: #f9fafb;">
                            <td colspan="4" style="text-align: right; font-weight: bold; font-style: italic;">Sub-Total {{ $division }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($items->sum('total'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-10 flex justify-between text-center text-sm avoid-break">
            <div style="width: 200px;">
                <p class="mb-16">Disiapkan Oleh,</p>
                <p class="font-bold border-b border-black">{{ Auth::user()->name }}</p>
                <p>Cost Estimator</p>
            </div>
            <div style="width: 200px;">
                <p class="mb-16">Disetujui Oleh,</p>
                <p class="font-bold border-b border-black">( ........................... )</p>
                <p>Project Manager</p>
            </div>
        </div>

    </div>
</div>