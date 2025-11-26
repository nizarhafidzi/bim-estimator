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
        
        <div class="absolute top-4 right-4 z-50">
            <div class="bg-white/90 backdrop-blur px-4 py-2 rounded-lg shadow-lg border border-white/50">
                <div class="text-[10px] font-bold text-gray-500 uppercase">Viewing File</div>
                <div class="text-sm font-bold text-blue-700">{{ $file->name }}</div>
            </div>
        </div>

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
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">File Cost</h2>
                <button onclick="resetFilter()" class="text-[10px] bg-gray-200 hover:bg-gray-300 text-gray-600 px-2 py-1 rounded transition font-bold cursor-pointer">Reset View</button>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 tracking-tight">
                Rp {{ number_format($totalCost, 0, ',', '.') }}
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @foreach($groupedBoq as $division => $items)
                <div class="sticky top-0 bg-gray-100/95 backdrop-blur px-5 py-2 border-y border-gray-200 z-10">
                    <h3 class="text-xs font-bold text-gray-700 uppercase flex items-center gap-2">{{ $division }}</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($items as $item)
                        <div onclick="filterByWorkCode('{{ $item['code'] }}')" class="px-5 py-3 hover:bg-blue-50 cursor-pointer transition-colors group border-l-4 border-transparent hover:border-blue-500">
                            <div class="flex justify-between items-start mb-1">
                                <div class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug group-hover:text-blue-700">{{ $item['name'] }}</div>
                                <div class="text-sm font-bold text-gray-900 whitespace-nowrap ml-2">{{ number_format($item['total'] / 1000000, 1) }} M</div>
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
        </div>
        
        <div class="p-4 border-t border-gray-200 bg-gray-50">
             <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-100 hover:text-black transition text-xs font-bold shadow-sm">
                &larr; Back to Projects
             </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
    <script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            var viewer;
            
            // Data dari Backend (Static untuk 1 File)
            const matchedGuids = @json($matchedIds);
            const unassignedGuids = @json($unassignedIds);
            const elementData = @json($elementData);
            const workCodeMap = @json($workCodeMap);

            var dbIdToGuid = {}; 
            var workCodeToDbIds = {}; 

            const colorGreen = new THREE.Vector4(0, 1, 0, 0.5); 
            const colorRed = new THREE.Vector4(1, 0, 0, 0.5);
            const colorBlue = new THREE.Vector4(0, 0, 1, 0.6);

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
                    viewer.addEventListener(Autodesk.Viewing.OBJECT_TREE_CREATED_EVENT, function() {
                        viewer.model.getExternalIdMapping(onMappingSuccess, onMappingFailure);
                    });
                    const canvas = viewer.canvas;
                    canvas.addEventListener('mousemove', updateTooltip);
                    canvas.addEventListener('mouseout', () => tooltip.classList.add('hidden'));
                });
            }

            function onMappingSuccess(mapping) {
                for (const [guid, dbId] of Object.entries(mapping)) {
                    dbIdToGuid[dbId] = guid;
                }
                for (const [code, guids] of Object.entries(workCodeMap)) {
                    workCodeToDbIds[code] = guids.map(g => mapping[g]).filter(id => id !== undefined);
                }
                resetFilter();
            }

            // GLOBAL FUNCTIONS
            window.filterByWorkCode = function(code) {
                if (!viewer.model || !workCodeToDbIds[code]) return;
                const idsToIsolate = workCodeToDbIds[code];
                viewer.clearThemingColors();
                viewer.isolate(idsToIsolate);
                idsToIsolate.forEach(id => viewer.setThemingColor(parseInt(id), colorBlue));
                viewer.fitToView(idsToIsolate);
            };

            window.resetFilter = function() {
                if (!viewer.model) return;
                viewer.isolate(null); 
                viewer.fitToView(); 
                viewer.clearThemingColors();
                
                const mapping = viewer.model.getExternalIdMapping();
                matchedGuids.forEach(guid => { if(mapping[guid]) viewer.setThemingColor(mapping[guid], colorGreen); });
                unassignedGuids.forEach(guid => { if(mapping[guid]) viewer.setThemingColor(mapping[guid], colorRed); });
            };

            function updateTooltip(event) {
                // ... (Isi sama dengan sebelumnya, tidak berubah) ...
                // Paste logic tooltip lama di sini
                try {
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
                        } else { tooltip.classList.add('hidden'); }
                    } else { tooltip.classList.add('hidden'); }
                } catch (e) {}
            }

            function onMappingFailure(err) { console.error(err); }
            function onDocumentLoadFailure(err) { console.error(err); }
        });
    </script>
</div>