<?php

namespace App\Livewire\Admin;

use App\Models\Draw;
use Livewire\Component;

class AddDraw extends Component
{
    public $start_time;

    public $end_time;

    public $draw;

    public $rules = [
        'start_time' => 'required',
        'end_time' => 'required',
    ];

    public function mount($draw_id)
    {
        if ($draw_id) {
            $this->draw = Draw::findOrFail($draw_id);

            $this->start_time = $this->draw->start_time;
            $this->end_time = $this->draw->end_time;
        }
    }

    public function save()
    {
        $input_data = $this->validate($this->rules);
        $input_data['price'] = 11;
        if ($this->draw) {
            $this->draw->update($input_data);
        } else {
            Draw::create($input_data);
        }

        return redirect()->route('admin.draw');
    }

    public function render()
    {
        return view('livewire.admin.add-draw');
    }
}
