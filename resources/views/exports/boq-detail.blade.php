<table>
    <thead>
        <tr><th colspan="7" style="font-weight: bold;">BILL OF QUANTITIES (DETAIL)</th></tr>
        <tr><th colspan="7">Project: {{ $project->name }}</th></tr>
        <tr></tr>
        <tr style="background-color: #eeeeee;">
            <th style="border: 1px solid #000; font-weight: bold;">NO</th>
            <th style="border: 1px solid #000; font-weight: bold;">KODE</th>
            <th style="border: 1px solid #000; font-weight: bold;">URAIAN PEKERJAAN</th>
            <th style="border: 1px solid #000; font-weight: bold;">VOLUME</th>
            <th style="border: 1px solid #000; font-weight: bold;">SATUAN</th>
            <th style="border: 1px solid #000; font-weight: bold;">HARGA SATUAN</th>
            <th style="border: 1px solid #000; font-weight: bold;">JUMLAH HARGA</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; $roman = ['I','II','III','IV','V','VI']; $i=0; @endphp
        
        @foreach($details as $division => $items)
            <tr style="background-color: #f0f8ff;">
                <td style="border: 1px solid #000; font-weight: bold;">{{ $roman[$i++] ?? $i }}</td>
                <td colspan="6" style="border: 1px solid #000; font-weight: bold;">{{ $division }}</td>
            </tr>

            @php $no = 1; @endphp
            @foreach($items as $item)
                <tr>
                    <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
                    <td style="border: 1px solid #000;">{{ $item->code }}</td>
                    <td style="border: 1px solid #000;">{{ $item->name }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ $item->volume }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $item->unit }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ $item->price }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ $item->total }}</td>
                </tr>
                @php $grandTotal += $item->total; @endphp
            @endforeach
            
            <tr>
                <td colspan="6" style="border: 1px solid #000; text-align: right; font-weight: bold;">Sub Total {{ $division }}</td>
                <td style="border: 1px solid #000; text-align: right; font-weight: bold;">{{ $items->sum('total') }}</td>
            </tr>
            <tr></tr> @endforeach

        <tr>
            <td colspan="6" style="border: 1px solid #000; text-align: right; font-weight: bold; font-size: 12px;">GRAND TOTAL (PPN Excluded)</td>
            <td style="border: 1px solid #000; text-align: right; font-weight: bold; font-size: 12px;">{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>