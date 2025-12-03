<?php

namespace App\Livewire;

use Livewire\Component;

class Documentation extends Component
{
    public $activeTab = 'user'; // 'user' atau 'dev'

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.documentation')->layout('layouts.app');
    }
}