<?php

namespace App\Traits\TicketForm;

trait CrossAbcOperation
{
    public $cross_abc_input;

    public $cross_abc_qty;

    public $cross_combination = 3;

    public $activeTab = 'simple_abc'; // default tab

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public $cross_attributes = [
        'cross_abc_input' => 'ABC',
        'cross_abc_qty' => 'Qty',
        'cross_combination' => 'Combination',
    ];

    public function resetCrossError()
    {
        $this->resetErrorBag(['cross_abc_input', 'cross_abc_qty', 'cross_combination']);
    }

    public function crossSubmit()
    {
        try {
            $rules = [
                'cross_abc_input' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{1,3}$/',
                ],

                // ✅ must be numeric, divisible by 5 (e.g., 5, 10, 15, 20, 25 …)
                'cross_abc_qty' => [
                    'required',
                    'integer',
                    'multiple_of:5',
                ],

                // ✅ must be either 6 or 27
                'cross_combination' => [
                    'required',
                    'in:3,27',
                ],
            ];
            // $this->activeTab = 'cross_abc';

            $cross_data = $this->validate($rules);
            dd($cross_data);

        } catch (\Exception $e) {
            dd($e);
            // $this->activeTab = 'cross_abc';

        }

    }
}
