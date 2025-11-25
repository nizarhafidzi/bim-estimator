<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100 mx-auto max-w-7xl">
    <h2 class="text-lg font-semibold text-gray-800 mb-6">Master Unit Prices (Harga Satuan)</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200 mx-auto max-w-7xl">
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Assembly Code</label>
            <input type="text" wire:model="work_code" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: C2010">
            <span class="text-[10px] text-gray-400">Harus sama dengan Revit</span>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Pekerjaan</label>
            <input type="text" wire:model="description" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: Dinding Bata Ringan 10cm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga (Rp)</label>
            <input type="number" wire:model="price" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="150000">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Satuan</label>
            <div class="flex gap-2">
                <input type="text" wire:model="unit" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="m2 / m3">
                <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-bold shadow-sm">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-hidden border rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Harga Satuan</th>
                    <th class="px-4 py-3">Satuan</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($prices as $item)
                    <tr class="hover:bg-blue-50">
                        <td class="px-4 py-3 font-mono font-medium text-blue-600">{{ $item->work_code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->description }}</td>
                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->unit }}</td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="edit({{ $item->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-bold mr-3 border border-blue-200 px-2 py-1 rounded">Edit</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus harga ini?" class="text-red-600 hover:text-red-800 text-xs font-bold border border-red-200 px-2 py-1 rounded">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">Belum ada data harga. Silakan input di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>