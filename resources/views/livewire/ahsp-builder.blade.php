<div class="h-[calc(100vh-64px)] flex bg-gray-100 overflow-hidden relative">
    
    <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">Library Editor</div>
                    <h2 class="font-bold text-gray-800 truncate w-48" title="{{ $library->name }}">{{ $library->name }}</h2>
                </div>
                <a href="{{ route('cost-libraries') }}" class="text-xs text-blue-600 hover:underline">Back to Library</a>
            </div>
            
            <div class="mt-4 flex gap-2">
                <input type="text" wire:model.live="searchAhsp" placeholder="Cari AHSP..." class="w-full text-sm border-gray-300 rounded-md px-3 py-2">
                <button wire:click="createNewMode" class="bg-blue-600 text-white px-3 rounded-md hover:bg-blue-700 font-bold text-xl" title="Buat AHSP Baru">+</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            @foreach($ahsps as $item)
                <div class="p-4 border-b border-gray-100 hover:bg-blue-50 transition group {{ $activeAhspId == $item->id ? 'bg-blue-50 border-l-4 border-l-blue-600' : '' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 cursor-pointer" wire:click="selectAhsp({{ $item->id }})">
                            <div class="flex justify-between items-start">
                                <span class="text-[10px] font-mono bg-gray-200 text-gray-600 px-1.5 rounded">{{ $item->code }}</span>
                                <span class="text-[10px] text-gray-400">{{ $item->division }}</span>
                            </div>
                            <div class="font-medium text-sm text-gray-800 mt-1">{{ $item->name }}</div>
                            <div class="text-xs text-right font-bold text-green-600 mt-2">
                                Rp {{ number_format($item->total_price, 0, ',', '.') }} / {{ $item->unit }}
                            </div>
                        </div>

                        <button wire:click="deleteAhsp({{ $item->id }})" wire:confirm="Yakin hapus analisa ini?" 
                                class="ml-2 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-2/3 flex flex-col bg-gray-50">
        
        @if (session()->has('message'))
            <div class="absolute top-4 right-4 z-50 bg-green-600 text-white px-4 py-2 rounded shadow-lg text-sm animate-fade-in-down">
                {{ session('message') }}
            </div>
        @endif

        @if($activeAhspId || $isCreating)
            <div class="p-6 bg-white shadow-sm z-10 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide">
                    {{ $isCreating ? 'Create New Analysis' : 'Edit Analysis Header' }}
                </h3>
                <div class="grid grid-cols-6 gap-4">
                    <div class="col-span-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Code</label>
                        <input type="text" wire:model="ahspCode" class="w-full font-mono text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-span-3">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Work Item Name</label>
                        <input type="text" wire:model="ahspName" class="w-full font-bold text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-span-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Unit</label>
                        <input type="text" wire:model="ahspUnit" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="col-span-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">&nbsp;</label>
                        <button wire:click="saveHeader" class="w-full bg-gray-900 text-white text-sm py-2 rounded hover:bg-black transition">Save</button>
                    </div>
                </div>
            </div>

            @if(!$isCreating && $activeData)
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-visible"> <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700">Analysis Breakdown</h3>
                            
                            <div class="relative w-72">
                                <input type="text" wire:model.live="searchResource" placeholder="Add Material/Labor..." 
                                       class="w-full text-xs border-blue-300 rounded-full px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                                
                                @if(strlen($searchResource) > 0)
                                    <div class="absolute top-full right-0 w-full bg-white border shadow-xl rounded-lg mt-1 z-50 max-h-60 overflow-y-auto">
                                        @foreach($availableResources as $res)
                                            <div wire:click="addIngredient({{ $res->id }})" class="p-2 hover:bg-blue-50 cursor-pointer text-xs border-b">
                                                <div class="font-bold">{{ $res->name }}</div>
                                                <div class="text-gray-500 flex justify-between">
                                                    <span>{{ $res->resource_code }}</span>
                                                    <span>Rp {{ number_format($res->price) }}</span>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div wire:click="openResourceModal" class="p-3 bg-blue-50 hover:bg-blue-100 cursor-pointer text-xs text-blue-700 font-bold text-center border-t">
                                            + Create New Resource "{{ $searchResource }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2">Code</th>
                                    <th class="px-4 py-2">Resource Name</th>
                                    <th class="px-4 py-2 text-center">Unit</th>
                                    <th class="px-4 py-2 text-right">Basic Price</th>
                                    <th class="px-4 py-2 text-center" width="100">Coef.</th>
                                    <th class="px-4 py-2 text-right">Sub Total</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php $grandTotal = 0; @endphp
                                @foreach($activeData->coefficients as $coef)
                                    @if($coef->resource)
                                        @php 
                                            $subTotal = $coef->coefficient * $coef->resource->price;
                                            $grandTotal += $subTotal;
                                        @endphp
                                        <tr class="hover:bg-gray-50 group">
                                            <td class="px-4 py-2 text-xs font-mono text-gray-500">{{ $coef->resource->resource_code }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-800">{{ $coef->resource->name }}</td>
                                            <td class="px-4 py-2 text-center text-gray-500 text-xs">{{ $coef->resource->unit }}</td>
                                            <td class="px-4 py-2 text-right text-gray-500 text-xs">
                                                {{ number_format($coef->resource->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="number" step="0.0001" value="{{ $coef->coefficient }}"
                                                       wire:change="updateCoefficient({{ $coef->id }}, $event.target.value)"
                                                       class="w-20 text-center text-sm border-gray-200 rounded bg-blue-50 focus:bg-white focus:ring-blue-500 p-1 font-bold text-blue-700">
                                            </td>
                                            <td class="px-4 py-2 text-right font-bold text-gray-800">
                                                {{ number_format($subTotal, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button wire:click="removeIngredient({{ $coef->id }})" class="text-gray-300 hover:text-red-500 transition">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-600 uppercase text-xs">Harga Satuan Pekerjaan (HSP)</td>
                                    <td class="px-4 py-3 text-right font-extrabold text-blue-600 text-lg">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <p>Pilih AHSP di kiri atau Buat Baru</p>
            </div>
        @endif
    </div>

    @if($showResourceModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white w-96 rounded-xl shadow-2xl p-6 animate-fade-in-up">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Resource</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Code</label>
                        <input type="text" wire:model="newResCode" class="w-full border-gray-300 rounded text-sm" placeholder="e.g. M-005">
                        @error('newResCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Name</label>
                        <input type="text" wire:model="newResName" class="w-full border-gray-300 rounded text-sm" placeholder="e.g. Cat Tembok Premium">
                        @error('newResName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Type</label>
                            <select wire:model="newResType" class="w-full border-gray-300 rounded text-sm">
                                <option value="material">Material</option>
                                <option value="manpower">Manpower</option>
                                <option value="equipment">Equipment</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Unit</label>
                            <input type="text" wire:model="newResUnit" class="w-full border-gray-300 rounded text-sm" placeholder="kg/pail/m">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Basic Price (Rp)</label>
                        <input type="number" wire:model="newResPrice" class="w-full border-gray-300 rounded text-sm" placeholder="0">
                        @error('newResPrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="$set('showResourceModal', false)" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded">Cancel</button>
                    <button wire:click="saveNewResource" class="px-4 py-2 text-sm bg-blue-600 text-white font-bold rounded hover:bg-blue-700">Save & Add</button>
                </div>
            </div>
        </div>
    @endif

</div>