<div class="p-6 max-w-7xl mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Compliance Report</h2>
            <div class="flex gap-3">
                <a href="{{ route('compliance.report.print', $file->id) }}" class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-50 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Full Report
                </a>
            </div>
            <p class="text-sm text-gray-500">File: {{ $file->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">Back to Dashboard</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="text-xs font-bold text-gray-400 uppercase mb-1">Elements Checked</div>
            <div class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-green-50 p-6 rounded-xl border border-green-100">
            <div class="text-xs font-bold text-green-600 uppercase mb-1">Compliant (Pass)</div>
            <div class="text-3xl font-extrabold text-green-700">{{ number_format($stats['pass']) }}</div>
        </div>
        <div class="bg-red-50 p-6 rounded-xl border border-red-100">
            <div class="text-xs font-bold text-red-600 uppercase mb-1">Issues Found (Fail)</div>
            <div class="text-3xl font-extrabold text-red-700">{{ number_format($stats['fail']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex bg-white p-1 rounded-lg border border-gray-300">
                <button wire:click="$set('filterStatus', 'fail')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'fail' ? 'bg-red-100 text-red-700' : 'text-gray-500 hover:text-gray-700' }}">Fail Only</button>
                <button wire:click="$set('filterStatus', 'pass')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'pass' ? 'bg-green-100 text-green-700' : 'text-gray-500 hover:text-gray-700' }}">Pass Only</button>
                <button wire:click="$set('filterStatus', 'all')" class="px-4 py-1.5 text-xs font-bold rounded-md transition {{ $filterStatus == 'all' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">Show All</button>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchRule" placeholder="Search Category or Parameter..." class="text-xs border-gray-300 rounded-lg w-full md:w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Element Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Rule Check</th>
                        <th class="px-6 py-3">Actual Value</th>
                        <th class="px-6 py-3">Message</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($results as $res)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $res->status == 'fail' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-green-100 text-green-800 border-green-200' }}">
                                    {{ strtoupper($res->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $res->element->name ?? 'Unknown' }}
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $res->element->external_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $res->rule->category_target ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-gray-700">{{ $res->rule->parameter }}</div>
                                <div class="text-[10px] text-gray-500 font-mono">Target: {{ $res->rule->operator }} {{ $res->rule->value }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-bold {{ $res->status == 'fail' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $res->actual_value }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate" title="{{ $res->message }}">{{ $res->message }}</td>
                            
                            <td class="px-6 py-4 text-center">
                                <button onclick="open3DModal('{{ $res->element->external_id }}', '{{ $res->status }}')" 
                                        class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 font-bold shadow-sm flex items-center gap-1 mx-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View 3D
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No results found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">{{ $results->links() }}</div>
    </div>

    <div id="modal3D" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-80 backdrop-blur-sm">
        <div class="bg-white w-full max-w-5xl h-[80vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden relative">
            
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-lg text-gray-800">Element Inspector</h3>
                <button onclick="close3DModal()" class="text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            </div>

            <div class="flex-1 relative bg-gray-900">
                <div id="forgeViewer" class="w-full h-full"></div>
                
                <div id="viewerLoading" class="absolute inset-0 flex items-center justify-center bg-gray-900 text-white z-10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm font-mono">Loading Model...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
    <script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>

    <script>
        // Variables Global
        var viewer = null;
        var urn = '{{ $file->urn }}';
        var token = '{{ $viewerToken }}';
        var targetGuid = null; // GUID elemen yang mau dilihat
        var targetStatus = null; // 'pass' or 'fail'

        // Fungsi Buka Modal
        window.open3DModal = function(guid, status) {
            targetGuid = guid;
            targetStatus = status;
            document.getElementById('modal3D').classList.remove('hidden');
            
            // Init Viewer hanya sekali (Lazy Load)
            if (!viewer) {
                initViewer();
            } else {
                // Jika sudah ada, langsung isolasi elemen baru
                isolateTarget();
            }
        };

        window.close3DModal = function() {
            document.getElementById('modal3D').classList.add('hidden');
            // Optional: viewer.finish() jika ingin hemat memori saat modal tutup
        };

        function initViewer() {
            var options = {
                env: 'AutodeskProduction',
                accessToken: token,
                api: 'derivativeV2' + (atob(urn).indexOf('urn:adsk.objects') === 0 ? '_EU' : '') 
            };
            
            Autodesk.Viewing.Initializer(options, function() {
                var htmlDiv = document.getElementById('forgeViewer');
                viewer = new Autodesk.Viewing.GuiViewer3D(htmlDiv);
                var startedCode = viewer.start();
                
                if (startedCode > 0) {
                    console.error('Viewer error');
                    return;
                }

                Autodesk.Viewing.Document.load('urn:' + urn, onDocumentLoadSuccess, onDocumentLoadFailure);
            });
        }

        function onDocumentLoadSuccess(doc) {
            var viewables = doc.getRoot().getDefaultGeometry();
            viewer.loadDocumentNode(doc, viewables).then(i => {
                document.getElementById('viewerLoading').classList.add('hidden'); // Hide Loading
                
                viewer.addEventListener(Autodesk.Viewing.OBJECT_TREE_CREATED_EVENT, function() {
                    isolateTarget(); // Langsung isolasi saat tree siap
                });
            });
        }

        function isolateTarget() {
            if (!viewer.model) return;
            
            // Minta Mapping ID (GUID -> DbId)
            viewer.model.getExternalIdMapping(function(mapping) {
                const dbId = mapping[targetGuid];
                
                if (dbId) {
                    // 1. Reset Tampilan
                    viewer.clearThemingColors();
                    viewer.isolate([dbId]); // Sembunyikan yang lain (FOKUS UTAMA)
                    viewer.fitToView([dbId]); // Zoom ke objek
                    
                    // 2. Beri Warna sesuai Status
                    const color = targetStatus == 'fail' 
                        ? new THREE.Vector4(1, 0, 0, 0.5) // Merah
                        : new THREE.Vector4(0, 1, 0, 0.5); // Hijau
                        
                    viewer.setThemingColor(dbId, color);
                } else {
                    console.warn("GUID not found in model: " + targetGuid);
                }
            });
        }

        function onDocumentLoadFailure(err) { console.error(err); }
    </script>
</div>