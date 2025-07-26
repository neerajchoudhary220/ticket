<?php

namespace App\Livewire\Admin;

use App\Models\Draw;
use Livewire\Component;

class AddDraw extends Component
{
    public $start_time;

    public $end_time;

    public $rules = [
        'start_time' => 'required',
        'end_time' => 'required',
    ];

    public function save()
    {
        $input_data = $this->validate($this->rules);
        $input_data['price'] = 11;
        Draw::create($input_data);

        return redirect()->route('admin.draw');
    }

    public function render()
    {
        return view('livewire.admin.add-draw');
    }
}
