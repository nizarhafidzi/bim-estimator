<div class="min-h-screen bg-gray-50 flex flex-col">
    
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Documentation
            </h1>
            
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button wire:click="setTab('user')" class="px-4 py-1.5 text-sm font-bold rounded-md transition {{ $activeTab === 'user' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    User Guide
                </button>
                <button wire:click="setTab('dev')" class="px-4 py-1.5 text-sm font-bold rounded-md transition {{ $activeTab === 'dev' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Developer Docs
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto w-full px-6 py-10">
        
        @if($activeTab === 'user')
            <div class="bg-white p-10 rounded-2xl shadow-sm border border-gray-200 prose max-w-none">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">User Guide: BIM Cost Estimator</h2>
                
                <div class="space-y-8">
                    <section>
                        <h3 class="text-xl font-bold text-blue-600 mb-2">1. Getting Started</h3>
                        <p class="text-gray-600">Selamat datang di aplikasi estimasi biaya berbasis BIM 5D. Aplikasi ini membantu Anda menghitung RAB secara otomatis dari model Revit.</p>
                        <ul class="list-disc list-inside text-gray-600 ml-4 space-y-1">
                            <li>Pastikan Anda memiliki akun <strong>Autodesk Construction Cloud (ACC)</strong>.</li>
                            <li>Pastikan file Revit Anda memiliki parameter <strong>Assembly Code</strong> yang terisi.</li>
                        </ul>
                    </section>

                    <hr class="border-gray-100">

                    <section>
                        <h3 class="text-xl font-bold text-blue-600 mb-2">2. Setup Master Data (Library)</h3>
                        <p class="text-gray-600">Sebelum membuat proyek, Anda wajib memiliki referensi harga.</p>
                        <ol class="list-decimal list-inside text-gray-600 ml-4 space-y-2">
                            <li>Masuk menu <strong>Cost Libraries</strong>.</li>
                            <li>Buat Library baru (misal: "SNI 2024").</li>
                            <li>Klik <strong>Import Excel</strong>. Gunakan template yang disediakan (Sheet Resources & Analysis).</li>
                            <li>Atau gunakan <strong>AHSP Builder</strong> untuk meracik harga secara manual.</li>
                        </ol>
                    </section>

                    <hr class="border-gray-100">

                    <section>
                        <h3 class="text-xl font-bold text-blue-600 mb-2">3. Creating a Project</h3>
                        <p class="text-gray-600">Setelah Library siap:</p>
                        <ol class="list-decimal list-inside text-gray-600 ml-4 space-y-2">
                            <li>Masuk ke <strong>Dashboard</strong> -> Klik <strong>+ New Project</strong>.</li>
                            <li>Isi nama dan pilih Library yang ingin digunakan.</li>
                            <li>Klik tombol <strong>Manage Files</strong> pada proyek tersebut.</li>
                            <li>Di bagian bawah, Login ke Autodesk dan pilih file Revit (.rvt) dari folder ACC Anda.</li>
                            <li>Tunggu hingga status file berubah menjadi <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-bold">Ready</span>.</li>
                        </ol>
                    </section>

                    <hr class="border-gray-100">

                    <section>
                        <h3 class="text-xl font-bold text-blue-600 mb-2">4. Calculating & Reporting</h3>
                        <p class="text-gray-600">Langkah terakhir adalah menghitung biaya:</p>
                        <ol class="list-decimal list-inside text-gray-600 ml-4 space-y-2">
                            <li>Masuk menu <strong>Estimasi Biaya</strong>.</li>
                            <li>Pilih Project Anda.</li>
                            <li>Klik <strong>Calculate Cost</strong>. Sistem akan mencocokkan Kode Revit vs Kode Library.</li>
                            <li>Lihat hasilnya di tabel atau klik <strong>Export Excel</strong> untuk download RAB.</li>
                            <li>Gunakan menu <strong>3D View</strong> di Dashboard untuk melihat visualisasi elemen yang terhitung (Hijau) dan belum (Merah).</li>
                        </ol>
                    </section>
                </div>
            </div>
        @endif

        @if($activeTab === 'dev')
            <div class="bg-slate-900 p-10 rounded-2xl shadow-2xl border border-slate-800 text-slate-300 font-mono text-sm">
                <h2 class="text-2xl font-bold text-white mb-6 border-b border-slate-700 pb-4">Developer Documentation (Technical)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-purple-400 font-bold mb-3 uppercase tracking-wider">Technology Stack</h3>
                        <ul class="space-y-2">
                            <li><span class="text-white font-bold">Framework:</span> Laravel 10</li>
                            <li><span class="text-white font-bold">Frontend:</span> Livewire 3 + Tailwind CSS</li>
                            <li><span class="text-white font-bold">3D Engine:</span> Autodesk Platform Services (APS) Viewer v7</li>
                            <li><span class="text-white font-bold">Queue:</span> Database Driver (Jobs table)</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-purple-400 font-bold mb-3 uppercase tracking-wider">Core Database Schema</h3>
                        <ul class="space-y-2">
                            <li><code class="bg-slate-800 px-1 rounded text-yellow-400">projects</code> : Header proyek (Parent).</li>
                            <li><code class="bg-slate-800 px-1 rounded text-yellow-400">project_files</code> : Menyimpan URN & Status Import.</li>
                            <li><code class="bg-slate-800 px-1 rounded text-yellow-400">model_elements</code> : Metadata (Volume, GUID, Assembly Code).</li>
                            <li><code class="bg-slate-800 px-1 rounded text-yellow-400">ahsp_masters</code> : Data harga satuan & rumus.</li>
                            <li><code class="bg-slate-800 px-1 rounded text-yellow-400">cost_results</code> : Hasil kali Volume x Harga.</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-purple-400 font-bold mb-3 uppercase tracking-wider">Key Workflows</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-white font-bold">1. Import Process (Background Job)</h4>
                            <p class="mb-2">Triggered via <code class="text-blue-400">FetchAccMetadata::dispatch($file)</code>.</p>
                            <pre class="bg-black p-4 rounded-lg overflow-x-auto text-xs text-green-400">
1. Auth Token Refresh (APS V2)
2. Check Manifest (Is Translated?)
   - If No: Trigger Translation (SVF) -> Release Job (Wait)
   - If Yes: Download Metadata JSON
3. Parse JSON -> Batch Insert to `model_elements`
                            </pre>
                        </div>

                        <div>
                            <h4 class="text-white font-bold">2. Cost Calculation Logic</h4>
                            <p class="mb-2">Located in <code class="text-blue-400">App\Services\CostEstimationService</code>.</p>
                            <pre class="bg-black p-4 rounded-lg overflow-x-auto text-xs text-green-400">
1. Load AHSP Library (Eager Loading Resources)
2. Loop all Model Elements (Volume > 0)
3. Match `element.assembly_code` == `ahsp.code`
4. If Match: Total = Volume * AHSP_Total_Price
5. Save to `cost_results` table
                            </pre>
                        </div>

                        <div>
                            <h4 class="text-white font-bold">3. 3D Coloring Strategy</h4>
                            <p class="mb-2">Located in <code class="text-blue-400">project-dashboard.blade.php</code>.</p>
                            <pre class="bg-black p-4 rounded-lg overflow-x-auto text-xs text-green-400">
1. Backend sends Array of GUIDs (Matched & Unassigned).
2. Viewer JS waits for `OBJECT_TREE_CREATED_EVENT`.
3. Call `viewer.model.getExternalIdMapping()` to convert GUID -> DbId.
4. `viewer.setThemingColor(dbId, color)`
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>