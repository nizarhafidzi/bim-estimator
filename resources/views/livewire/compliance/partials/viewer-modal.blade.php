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