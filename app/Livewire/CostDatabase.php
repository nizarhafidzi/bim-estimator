<?php

namespace App\Livewire;

use App\Models\MasterUnitPrice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CostDatabase extends Component
{
    public $prices;
    
    // Variabel untuk Form Input
    public $work_code, $description, $price, $unit;
    public $isEdit = false;
    public $editId = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Ambil data milik user yang sedang login
        $this->prices = MasterUnitPrice::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    // Reset form jadi kosong
    public function resetForm()
    {
        $this->work_code = '';
        $this->description = '';
        $this->price = '';
        $this->unit = '';
        $this->isEdit = false;
        $this->editId = null;
    }

    public function save()
    {
        $this->validate([
            'work_code' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'unit' => 'required',
        ]);

        if ($this->isEdit) {
            // Logic Update
            MasterUnitPrice::find($this->editId)->update([
                'work_code' => $this->work_code,
                'description' => $this->description,
                'price' => $this->price,
                'unit' => $this->unit,
            ]);
        } else {
            // Logic Simpan Baru (UpdateOrCreate untuk hindari duplikat)
            MasterUnitPrice::updateOrCreate(
                ['user_id' => Auth::id(), 'work_code' => $this->work_code],
                [
                    'description' => $this->description,
                    'price' => $this->price,
                    'unit' => $this->unit,
                ]
            );
        }

        $this->resetForm();
        $this->loadData();
        session()->flash('message', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $data = MasterUnitPrice::find($id);
        $this->editId = $data->id;
        $this->work_code = $data->work_code;
        $this->description = $data->description;
        $this->price = $data->price;
        $this->unit = $data->unit;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        MasterUnitPrice::find($id)->delete();
        $this->loadData();
    }

    public function render()
    {
        // Penting: layout('layouts.app') agar sidebar muncul
        return view('livewire.cost-database')->layout('layouts.app');
    }
}