<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase mb-1">Library: {{ $library->name }}</div>
            <h2 class="text-2xl font-bold text-gray-800">📦 Resource Manager</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('cost-libraries') }}" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">Back</a>
            <button wire:click="create" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">+ New Resource</button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <input type="text" wire:model.live="search" placeholder="Search resources..." class="text-sm border-gray-300 rounded w-64">
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Code</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3 text-center">Unit</th>
                    <th class="px-6 py-3 text-right">Price</th>
                    <th class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($resources as $res)
                    <tr class="hover:bg-blue-50">
                        <td class="px-6 py-3 font-mono text-xs">{{ $res->resource_code }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold {{ $res->type == 'material' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ $res->type }}</span></td>
                        <td class="px-6 py-3 font-medium">{{ $res->name }}</td>
                        <td class="px-6 py-3 text-center">{{ $res->unit }}</td>
                        <td class="px-6 py-3 text-right">Rp {{ number_format($res->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-center">
                            <button wire:click="edit({{ $res->id }})" class="text-blue-600 hover:underline text-xs mr-2">Edit</button>
                            <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this?" class="text-red-600 hover:underline text-xs">Del</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $resources->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-96">
                <h3 class="font-bold text-lg mb-4">{{ $isEdit ? 'Edit' : 'New' }} Resource</h3>
                <div class="space-y-3">
                    <input type="text" wire:model="code" placeholder="Code (e.g. M001)" class="w-full text-sm border-gray-300 rounded">
                    <select wire:model="type" class="w-full text-sm border-gray-300 rounded">
                        <option value="material">Material</option>
                        <option value="manpower">Manpower</option>
                        <option value="equipment">Equipment</option>
                    </select>
                    <input type="text" wire:model="name" placeholder="Name" class="w-full text-sm border-gray-300 rounded">
                    <input type="text" wire:model="unit" placeholder="Unit (m3, kg)" class="w-full text-sm border-gray-300 rounded">
                    <input type="number" wire:model="price" placeholder="Price" class="w-full text-sm border-gray-300 rounded">
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 text-sm bg-gray-100 rounded">Cancel</button>
                    <button wire:click="save" class="px-4 py-2 text-sm bg-blue-600 text-white rounded">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>