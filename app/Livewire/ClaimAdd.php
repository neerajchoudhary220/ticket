<?php

namespace App\Livewire;

use App\Models\DrawDetail;
use Livewire\Attributes\On;
use Livewire\Component;

class ClaimAdd extends Component
{
    public $claim_a = '';

    public $claim_b = '';

    public $claim_c = '';

    public int $draw_detail_id;

    public string $end_time = '';

    public $rules = [
        'claim_a' => ['required', 'regex:/^[0-9]$/'],
        'claim_b' => ['required', 'regex:/^[0-9]$/'],
        'claim_c' => ['required', 'regex:/^[0-9]$/'],
    ];

    #[On('claim-event')]
    public function claimHandler($draw_details_id)
    {
        $draw_details = DrawDetail::find($draw_details_id);
        $this->claim_a = $draw_details->claim_a;
        $this->claim_b = $draw_details->claim_b;
        $this->claim_c = $draw_details->claim_c;
        $this->draw_detail_id = $draw_details->id;
        $this->end_time = $draw_details->formatEndTime();
        $this->resetErrorBag(['claim_a', 'claim_b', 'claim_c']);

        $this->dispatch('show-claim-modal');

    }

    public function save()
    {
        $input = $this->validate($this->rules);
        $draw_details = DrawDetail::find($this->draw_detail_id);
        $a_claim = $draw_details->ticketOptions->where('number', $this->claim_a)->sum('a_qty');
        $b_claim = $draw_details->ticketOptions->where('number', $this->claim_b)->sum('b_qty');
        $c_claim = $draw_details->ticketOptions->where('number', $this->claim_c)->sum('c_qty');
        $total_qty = $a_claim + $b_claim + $c_claim;
        $input['claim'] = $total_qty;
        $draw_details->update($input);

        return redirect()->route('admin.dashboard');

    }

    public function render()
    {
        return view('livewire.claim-add');
    }
}
