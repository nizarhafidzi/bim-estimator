<div class="h-[calc(100vh-64px)] flex flex-col md:flex-row overflow-hidden relative font-sans">
    
    <div id="viewerTooltip" class="fixed z-[100] hidden pointer-events-none transform -translate-x-1/2 -translate-y-full mt-[-15px] transition-opacity duration-200">
        <div class="bg-slate-900/95 backdrop-blur-md text-white rounded-xl py-3 px-4 shadow-2xl border border-slate-600 w-64">
            <div class="border-b border-slate-700 pb-2 mb-2">
                <div class="text-[10px] text-slate-400 uppercase tracking-wider" id="ttCategory">Element Property</div>
                <div class="font-bold text-sm text-white leading-tight" id="ttRevitName">Basic Wall</div>
            </div>
            <div class="space-y-1">
                <div class="text-xs text-slate-400">Work Item:</div>
                <div class="text-xs font-semibold text-blue-300 mb-1" id="ttWorkName">Pek. Dinding Bata</div>
                <div class="flex justify-between items-center">
                    <span class="bg-slate-700 text-slate-300 px-1.5 py-0.5 rounded text-[10px] font-mono" id="ttCode">C2010</span>
                    <span class="text-[10px] text-slate-400" id="ttDivision">STRUKTUR</span>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-700 flex justify-between items-center">
                <span class="text-xs font-bold" id="ttStatus">Matched</span>
                <div class="font-mono text-emerald-400 font-bold text-base" id="ttPrice">Rp 0</div>
            </div>
        </div>
        <div class="w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-t-[8px] border-t-slate-900/95 mx-auto"></div>
    </div>

    <div class="w-full md:w-3/4 relative bg-gray-900 group">
        <div id="forgeViewer" class="w-full h-full"></div>
        
        <div class="absolute top-6 left-6 z-50 opacity-90 hover:opacity-100 transition-opacity">
            <div class="backdrop-blur-md bg-white/90 border border-white/50 shadow-lg rounded-xl p-4 w-60">
                <h3 class="font-bold text-gray-800 mb-3 text-xs uppercase tracking-wide border-b border-gray-200 pb-2">{{ $project->name }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full shadow-sm"></span>
                            <span class="font-medium text-gray-700">Matched</span>
                        </div>
                        <span class="font-mono font-bold bg-green-100 text-green-800 px-1.5 rounded">{{ count($matchedIds) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-red-500 rounded-full shadow-sm"></span>
                            <span class="font-medium text-gray-700">Unassigned</span>
                        </div>
                        <span class="font-mono font-bold bg-red-100 text-red-800 px-1.5 rounded">{{ count($unassignedIds) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/4 bg-white border-l border-gray-200 flex flex-col h-full shadow-xl z-10">
        
        <div class="p-5 border-b border-gray-100 bg-gradient-to-b from-white to-gray-50">
            <div class="flex justify-between items-end mb-2">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">Total Cost</h2>
                <button onclick="resetFilter()" class="text-[10px] bg-gray-200 hover:bg-gray-300 text-gray-600 px-2 py-1 rounded transition font-bold flex items-center gap-1 cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset View
                </button>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 tracking-tight">
                Rp {{ number_format($totalCost, 0, ',', '.') }}
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @foreach($groupedBoq as $division => $items)
                <div class="sticky top-0 bg-gray-100/95 backdrop-blur px-5 py-2 border-y border-gray-200 z-10">
                    <h3 class="text-xs font-bold text-gray-700 uppercase flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        {{ $division }}
                    </h3>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($items as $item)
                        <div onclick="filterByWorkCode('{{ $item['code'] }}')" 
                             class="px-5 py-3 hover:bg-blue-50 cursor-pointer transition-colors group border-l-4 border-transparent hover:border-blue-500">
                            
                            <div class="flex justify-between items-start mb-1">
                                <div class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug group-hover:text-blue-700">
                                    {{ $item['name'] }}
                                </div>
                                <div class="text-sm font-bold text-gray-900 whitespace-nowrap ml-2">
                                    {{ number_format($item['total'] / 1000000, 1) }} M
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-gray-400">
                                <div class="flex gap-2 font-mono">
                                    <span class="bg-gray-100 px-1 rounded text-gray-500 group-hover:bg-white">{{ $item['code'] }}</span>
                                    <span>{{ $item['count'] }} pcs</span>
                                </div>
                                <span class="text-blue-500 opacity-0 group-hover:opacity-100 font-bold transition-opacity">Click to View</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            @if(count($unassignedIds) > 0)
                <div class="p-4 bg-red-50 border-t border-red-100 mt-2">
                    <div class="flex items-center gap-2 text-red-700 font-bold text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Unassigned Items
                    </div>
                    <p class="text-xs text-red-600">{{ count($unassignedIds) }} elements found in model without matching price.</p>
                </div>
            @endif
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('cost-calculator') }}" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-100 hover:text-black transition text-xs font-bold shadow-sm">
                Full Calculation Details
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
    <script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            var viewer;
            
            // Data dari Backend (PHP)
            const matchedGuids = @json($matchedIds);
            const unassignedGuids = @json($unassignedIds);
            const elementData = @json($elementData);
            const workCodeMap = @json($workCodeMap); // Peta Kode AHSP -> Array GUID

            // Helper Maps untuk JS
            var dbIdToGuid = {}; 
            var workCodeToDbIds = {}; 

            // Warna
            const colorGreen = new THREE.Vector4(0, 1, 0, 0.5); 
            const colorRed = new THREE.Vector4(1, 0, 0, 0.5);
            const colorBlue = new THREE.Vector4(0, 0, 1, 0.6); // Warna Seleksi/Filter

            // Tooltip References
            const tooltip = document.getElementById('viewerTooltip');
            const els = {
                revitName: document.getElementById('ttRevitName'),
                workName: document.getElementById('ttWorkName'),
                code: document.getElementById('ttCode'),
                division: document.getElementById('ttDivision'),
                status: document.getElementById('ttStatus'),
                price: document.getElementById('ttPrice'),
            };

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
                    // Tunggu Object Tree siap sebelum mapping
                    viewer.addEventListener(Autodesk.Viewing.OBJECT_TREE_CREATED_EVENT, function() {
                        viewer.model.getExternalIdMapping(onMappingSuccess, onMappingFailure);
                    });

                    // Mouse Hover Event untuk Tooltip
                    const canvas = viewer.canvas;
                    canvas.addEventListener('mousemove', updateTooltip);
                    canvas.addEventListener('mouseout', () => tooltip.classList.add('hidden'));
                });
            }

            function onMappingSuccess(mapping) {
                // 1. Buat Mapping DBID <-> GUID
                for (const [guid, dbId] of Object.entries(mapping)) {
                    dbIdToGuid[dbId] = guid;
                }

                // 2. Persiapkan Data Filter (WorkCode -> DbIds)
                for (const [code, guids] of Object.entries(workCodeMap)) {
                    workCodeToDbIds[code] = guids.map(g => mapping[g]).filter(id => id !== undefined);
                }

                // 3. Terapkan Warna Awal
                resetFilter();
            }

            // --- FUNGSI GLOBAL (Dipanggil dari OnClick HTML) ---
            
            // Filter: Tampilkan hanya item tertentu (warna Biru)
            window.filterByWorkCode = function(code) {
                if (!viewer.model || !workCodeToDbIds[code]) return;

                const idsToIsolate = workCodeToDbIds[code];

                viewer.clearThemingColors();
                viewer.isolate(idsToIsolate); // Sembunyikan yang lain
                
                idsToIsolate.forEach(id => viewer.setThemingColor(parseInt(id), colorBlue));
                
                viewer.fitToView(idsToIsolate); // Zoom ke objek
            };

            // Reset: Tampilkan semua (warna Hijau/Merah)
            window.resetFilter = function() {
                if (!viewer.model) return;

                viewer.isolate(null); // Show all
                viewer.fitToView(); 
                viewer.clearThemingColors();
                
                // Ambil DBID dari Mapping ulang (atau simpan di variabel global agar lebih efisien)
                const mapping = viewer.model.getExternalIdMapping();
                
                matchedGuids.forEach(guid => {
                    if(mapping[guid]) viewer.setThemingColor(mapping[guid], colorGreen);
                });
                
                unassignedGuids.forEach(guid => {
                    if(mapping[guid]) viewer.setThemingColor(mapping[guid], colorRed);
                });
            };

            // Logic Tooltip
            function updateTooltip(event) {
                const rect = viewer.canvas.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                const res = viewer.impl.hitTest(x, y);

                if (res && res.dbId) {
                    const guid = dbIdToGuid[res.dbId];
                    if (guid && elementData[guid]) {
                        const data = elementData[guid];
                        
                        els.revitName.innerText = data.revit_name;
                        els.workName.innerText = data.work_name;
                        els.code.innerText = data.work_code;
                        els.division.innerText = data.division;
                        els.price.innerText = data.cost_formatted;
                        
                        if (data.status === 'matched') {
                            els.status.innerText = 'MATCHED';
                            els.status.className = 'text-xs font-bold text-emerald-400';
                            els.price.className = 'font-mono text-emerald-400 font-bold text-base';
                        } else {
                            els.status.innerText = 'UNASSIGNED';
                            els.status.className = 'text-xs font-bold text-red-400';
                            els.price.className = 'font-mono text-red-400 font-bold text-base';
                        }

                        tooltip.style.left = event.clientX + 'px';
                        tooltip.style.top = (event.clientY - 10) + 'px';
                        tooltip.classList.remove('hidden');
                    } else {
                        tooltip.classList.add('hidden');
                    }
                } else {
                    tooltip.classList.add('hidden');
                }
            }

            function onMappingFailure(err) { console.error(err); }
            function onDocumentLoadFailure(err) { console.error(err); }
        });
    </script>
</div>