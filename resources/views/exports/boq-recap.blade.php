<table>
    <thead>
        <tr>
            <th colspan="3" style="font-weight: bold; font-size: 14px;">REKAPITULASI BIAYA PROYEK</th>
        </tr>
        <tr>
            <th colspan="3">Project: {{ $project->name }}</th>
        </tr>
        <tr>
            <th colspan="3">Date: {{ date('d M Y') }}</th>
        </tr>
        <tr></tr>
        <tr style="background-color: #eeeeee;">
            <th style="font-weight: bold; border: 1px solid #000;">NO</th>
            <th style="font-weight: bold; border: 1px solid #000;">URAIAN PEKERJAAN (DIVISI)</th>
            <th style="font-weight: bold; border: 1px solid #000;">TOTAL HARGA (RP)</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; $no = 1; @endphp
        @foreach($recap as $item)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000;">{{ $item->division }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $item->total_cost }}</td>
            </tr>
            @php $grandTotal += $item->total_cost; @endphp
        @endforeach
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000; text-align: right;">GRAND TOTAL</td>
            <td style="font-weight: bold; border: 1px solid #000; text-align: right;">{{ $grandTotal }}</td>
        </tr>
    </tbody>
</table>