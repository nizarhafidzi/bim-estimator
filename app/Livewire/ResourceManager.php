<?php

namespace App\Livewire;

use App\Models\CostLibrary;
use App\Models\Resource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceManager extends Component
{
    use WithPagination;

    public $libraryId;
    public $library;
    public $search = '';
    
    // Form Data
    public $resId, $code, $name, $type = 'material', $unit, $price;
    public $isEdit = false;
    public $showModal = false;

    public function mount($libraryId)
    {
        $this->libraryId = $libraryId;
        $this->library = CostLibrary::findOrFail($libraryId);
    }

    public function render()
    {
        $resources = Resource::where('cost_library_id', $this->libraryId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('resource_code')
            ->paginate(20);

        return view('livewire.resource-manager', ['resources' => $resources])
            ->layout('layouts.app');
    }

    // CRUD Functions
    public function create()
    {
        $this->reset(['resId', 'code', 'name', 'unit', 'price']);
        $this->type = 'material';
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $res = Resource::find($id);
        $this->resId = $res->id;
        $this->code = $res->resource_code;
        $this->name = $res->name;
        $this->type = $res->type;
        $this->unit = $res->unit;
        $this->price = $res->price;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'code' => 'required',
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $data = [
            'resource_code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'unit' => $this->unit,
            'price' => $this->price,
            'cost_library_id' => $this->libraryId
        ];

        if ($this->isEdit) {
            Resource::find($this->resId)->update($data);
        } else {
            Resource::create($data);
        }

        $this->showModal = false;
        session()->flash('message', 'Resource saved successfully.');
    }

    public function delete($id)
    {
        Resource::find($id)->delete();
    }
}