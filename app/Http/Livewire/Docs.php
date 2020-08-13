<?php

namespace App\Http\Livewire;

use App\Facades\Repositories;
use Livewire\Component;

class Docs extends Component
{
    public function render()
    {
        return view('livewire.docs', [
            'categories' => Repositories::byCategory()
        ]);
    }
}
