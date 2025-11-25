<div class="h-[calc(100vh-64px)] flex flex-col md:flex-row overflow-hidden relative">
    
    <div id="viewerTooltip" class="fixed z-[100] hidden pointer-events-none transform -translate-x-1/2 -translate-y-full mt-[-10px]">
        <div class="bg-gray-900/90 backdrop-blur text-white text-xs rounded-lg py-2 px-3 shadow-2xl border border-gray-700">
            <div class="font-bold text-gray-200 mb-1" id="tooltipTitle">Wall Generic</div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-blue-600 px-1.5 rounded text-[10px]" id="tooltipCode">C2010</span>
                <span class="text-gray-300" id="tooltipStatus">Matched</span>
            </div>
            <div class="font-mono text-green-400 font-bold text-sm" id="tooltipPrice">Rp 15.000.000</div>
        </div>
        <div class="w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-gray-900/90 mx-auto"></div>
    </div>

    <div class="w-full md:w-3/4 relative bg-gray-900">
        <div id="forgeViewer" class="w-full h-full"></div>
        
        <div class="absolute top-6 left-6 z-50">
            <div class="backdrop-blur-md bg-white/80 border border-white/40 shadow-2xl rounded-2xl p-5 w-64">
                <h3 class="font-bold text-gray-900 mb-3 text-sm border-b border-gray-200 pb-2">{{ $project->name }}</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            <span class="text-xs font-semibold text-gray-700">Matched</span>
                        </div>
                        <span class="text-xs font-mono font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded">{{ count($matchedIds) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 bg-red-500 rounded-full shadow-[0_0_8px_rgba(239,68,68,0.6)]"></span>
                            <span class="text-xs font-semibold text-gray-700">Unassigned</span>
                        </div>
                        <span class="text-xs font-mono font-bold bg-red-100 text-red-700 px-2 py-0.5 rounded">{{ count($unassignedIds) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/4 bg-white border-l border-gray-200 flex flex-col h-full">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Cost Summary</h2>
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-5 rounded-xl shadow-lg text-white">
                <div class="text-xs text-blue-100 uppercase font-bold tracking-wider mb-1">Total Estimated</div>
                @php
                    $total = \App\Models\CostResult::where('project_id', $projectId)->sum('total_cost');
                @endphp
                <div class="text-2xl font-extrabold">Rp {{ number_format($total, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-0">
            <div class="px-6 py-3 bg-white sticky top-0 border-b border-gray-100 z-10">
                <h3 class="text-xs font-bold text-gray-500 uppercase">Breakdown by Work Item</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                @foreach($costBreakdown as $item)
                    <div class="px-6 py-3 hover:bg-blue-50 transition-colors flex justify-between items-center group">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-[10px] font-mono border border-gray-200 group-hover:bg-white">
                                    {{ $item->matched_work_code }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Work Item Code</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-800">Rp {{ number_format($item->total, 0, ',', '.') }}</div>
                            @php $percent = ($item->total / $total) * 100; @endphp
                            <div class="w-16 h-1 bg-gray-100 rounded-full ml-auto mt-1 overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(count($unassignedIds) > 0)
                    <div class="px-6 py-3 bg-red-50/30 flex justify-between items-center border-l-4 border-red-500">
                        <div>
                            <span class="text-red-600 font-bold text-sm">Unassigned Items</span>
                            <div class="text-xs text-red-400 mt-0.5">{{ count($unassignedIds) }} elements</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-400">Rp 0</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-4 border-t border-gray-200 bg-white">
            <a href="{{ route('cost-calculator') }}" class="flex items-center justify-center gap-2 w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-black transition text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Full Details
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
    <script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            var viewer;
            
            // Data dari PHP
            const matchedGuids = @json($matchedIds);
            const unassignedGuids = @json($unassignedIds);
            const costData = @json($elementCosts); // Data Harga { "GUID": {cost, code, name} }

            // Reverse Map (DbId -> GUID) untuk Tooltip
            var dbIdToGuid = {}; 

            // Tooltip Elements
            const tooltip = document.getElementById('viewerTooltip');
            const ttTitle = document.getElementById('tooltipTitle');
            const ttCode = document.getElementById('tooltipCode');
            const ttStatus = document.getElementById('tooltipStatus');
            const ttPrice = document.getElementById('tooltipPrice');

            var options = {
                env: 'AutodeskProduction',
                accessToken: '{{ $viewerToken }}',
                api: 'derivativeV2' + (atob('{{ $viewerUrn }}').indexOf('urn:adsk.objects') === 0 ? '_EU' : '') 
            };
            
            Autodesk.Viewing.Initializer(options, function() {
                var htmlDiv = document.getElementById('forgeViewer');
                viewer = new Autodesk.Viewing.GuiViewer3D(htmlDiv);
                window.viewer = viewer;

                var startedCode = viewer.start();
                if (startedCode > 0) return;

                Autodesk.Viewing.Document.load('urn:' + '{{ $viewerUrn }}', onDocumentLoadSuccess, onDocumentLoadFailure);
            });

            function onDocumentLoadSuccess(doc) {
                var viewables = doc.getRoot().getDefaultGeometry();
                viewer.loadDocumentNode(doc, viewables).then(i => {
                    viewer.addEventListener(Autodesk.Viewing.OBJECT_TREE_CREATED_EVENT, function() {
                        viewer.model.getExternalIdMapping(onMappingSuccess, onMappingFailure);
                    });

                    // EVENT LISTENER: MOUSE HOVER
                    // Kita pakai Canvas Event agar realtime mengikuti mouse
                    const canvas = viewer.canvas;
                    canvas.addEventListener('mousemove', onMouseMove);
                    canvas.addEventListener('mouseout', () => tooltip.classList.add('hidden'));
                });
            }

            function onMappingSuccess(mapping) {
                // 1. Convert untuk Pewarnaan
                const matchedDbIds = matchedGuids.map(guid => mapping[guid]).filter(id => id !== undefined);
                const unassignedDbIds = unassignedGuids.map(guid => mapping[guid]).filter(id => id !== undefined);
                
                // 2. Buat Reverse Mapping (DbId -> GUID) agar saat hover DbId kita tahu GUID-nya
                // Mapping: { "GUID": 123 }
                for (const [guid, dbId] of Object.entries(mapping)) {
                    dbIdToGuid[dbId] = guid;
                }

                applyTheming(matchedDbIds, unassignedDbIds);
            }

            function onMappingFailure(err) { console.error(err); }
            function onDocumentLoadFailure(err) { console.error(err); }

            function applyTheming(matched, unassigned) {
                if (!viewer.model) return;
                const colorGreen = new THREE.Vector4(0, 1, 0, 0.5); 
                const colorRed = new THREE.Vector4(1, 0, 0, 0.5);
                viewer.clearThemingColors();
                matched.forEach(id => viewer.setThemingColor(parseInt(id), colorGreen));
                unassigned.forEach(id => viewer.setThemingColor(parseInt(id), colorRed));
                viewer.impl.invalidate(true, true, true);
            }

            // LOGIKA TOOLTIP
            function onMouseMove(event) {
                // Dapatkan koordinat mouse relatif terhadap canvas
                const rect = viewer.canvas.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;

                // Hit Test: Cek apakah mouse mengenai objek 3D?
                const res = viewer.impl.hitTest(x, y);

                if (res && res.dbId) {
                    const dbId = res.dbId;
                    const guid = dbIdToGuid[dbId]; // Ubah DbId jadi GUID

                    if (guid && costData[guid]) {
                        // Data ditemukan! Isi Tooltip
                        const data = costData[guid];
                        
                        ttTitle.innerText = data.name || 'Unknown Element';
                        ttCode.innerText = data.code;
                        ttPrice.innerText = data.cost;

                        if (data.cost === 'Unassigned') {
                            ttStatus.innerText = 'Unassigned';
                            ttStatus.className = 'text-red-400 text-[10px] uppercase font-bold';
                            ttPrice.className = 'font-mono text-red-400 font-bold text-sm';
                        } else {
                            ttStatus.innerText = 'Matched';
                            ttStatus.className = 'text-green-400 text-[10px] uppercase font-bold';
                            ttPrice.className = 'font-mono text-green-400 font-bold text-sm';
                        }

                        // Posisi Tooltip Mengikuti Mouse (Global Coordinates)
                        tooltip.style.left = event.clientX + 'px';
                        tooltip.style.top = event.clientY + 'px';
                        tooltip.classList.remove('hidden');
                    } else {
                        tooltip.classList.add('hidden');
                    }
                } else {
                    tooltip.classList.add('hidden');
                }
            }
        });
    </script>
</div>