<?php

namespace App\Traits\TicketForm;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait CrossAbcOperation
{
    public $cross_abc_input;

    public $cross_abc_amt;

    public $cross_combination = 3;

    public $activeTab = 'simple_abc'; // default tab

    public $cross_ab;

    public $cross_ab_amt;

    public $cross_ac;

    public $cross_ac_amt;

    public $cross_bc;

    public $cross_bc_amt;

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public $cross_attributes = [
        'cross_abc_input' => 'ABC',
        'cross_abc_amt' => 'Qty',
        'cross_combination' => 'Combination',
    ];

    public function resetCrossError()
    {
        $this->resetErrorBag(['cross_abc_input', 'cross_abc_amt', 'cross_combination']);
    }

    public function resetAbError()
    {
        $this->resetErrorBag(['cross_ab_amt', 'cross_ab']);

    }

    public function makeCombination(): array
    {

        $cross_abc = $this->cross_abc_input;
        $input_combination = $this->cross_combination;
        $chars = str_split($cross_abc);
        $keys = ['ab', 'ac', 'bc'];
        $result = [];

        if ($input_combination == 27 && strlen($cross_abc) == 3) {
            // Situation 1: generate full combinations with repetition
            foreach ($keys as $key) {
                $combis = [];
                foreach ($chars as $first) {
                    foreach ($chars as $second) {
                        $combis[] = (int) ($first.$second);
                    }
                }
                $result[$key] = $combis;
            }
        } elseif ($input_combination == 3 && strlen($cross_abc) == 3) {
            $result['ab'] = (int) ($chars[0].$chars[1]);
            $result['ac'] = (int) ($chars[0].$chars[2]);
            $result['bc'] = (int) ($chars[1].$chars[2]);

        } elseif ($input_combination == 27 && strlen($cross_abc) == 2) {
            // Situation 2: take only unique direct pairs
            $pairs = [
                'ab' => (int) ($chars[0].$chars[1]),
                'ac' => (int) ($chars[0].$chars[2]),
                'bc' => (int) ($chars[1].$chars[2]),
            ];
            foreach ($pairs as $key => $value) {
                $result[$key] = [$value];
            }
        } else {
            $result['ab'] = $result['ac'] = $result['bc'] = (int) ($chars[0].$chars[1]);
        }

        return [$result['ab'], $result['ac'], $result['bc']];
    }

    public function addCrossOptions($amt, $comb, $number = null, $ab = null, $ac = null, $bc = null, $option = null)
    {
        return [
            'number' => $number,
            'ab' => $ab,
            'ac' => $ac,
            'bc' => $bc,
            'amt' => $amt,
            'combination' => $comb,
            'option' => $option,
            // 'total' => $total,
            'created_at' => Carbon::now(),
            'draw_details_ids' => $this->selected_draw,
        ];

    }

    public function storeCrossAbcIntoCache($data)
    {
        $options = collect($this->getCrossOptions());
        if ($options) {
            $data = $options->merge($data);
        }

        return Cache::put('cross_abc', $data->values()->all(), 7200);
    }

    public function getCrossOptions()
    {
        return collect(Cache::get('cross_abc'))->sortByDesc('created_at');

    }

    public function deleteCrossAbc($index)
    {

        $data = collect($this->getCrossOptions())
            ->values();
        $data->forget($index);

        Cache::put('cross_abc', $data->values()->all());
        $this->loadAbcData(true);

    }

    public function clearAllCrossAbcIntoCache()
    {
        Cache::forget('cross_abc');
        $this->loadAbcData();

    }

    // Enter Abc of cross
    public function enterKeyPressOnCrossAbc($focus, $value)
    {
        $rules_and_attributes = [
            ['cross_abc_input' => [
                'required',
                'regex:/^(?!.*(.).*\\1)[0-9]{1,3}$/',
            ]],

            ['cross_abc_amt' => [
                'required',
                'integer',
                'multiple_of:5',
            ]],

            ['cross_combination' => strlen($this->cross_abc_input) == 3 ? [
                'required',
                'in:3,27',
            ] : [
                'required',
                'in:3',
            ],
            ],
        ];
        $attributes = [
            ['cross_abc_input' => 'ABC'],
            ['cross_abc_amt' => 'Amount'],
            ['cross_combination' => 'Combination'],
        ];

        switch ($value) {
            case 'cross_abc_input':
                $this->validate($rules_and_attributes[0], [], $attributes[0]);
                $this->dispatch($focus);
                break;
            case 'cross_abc_amt':
                $this->validate($rules_and_attributes[0], [], $attributes[0]);
                $this->validate($rules_and_attributes[1], [], $attributes[1]);
                $this->dispatch($focus);
                break;
            case 'cross_combination':
                $this->validate($rules_and_attributes[0], [], $attributes[0]);
                $this->validate($rules_and_attributes[1], [], $attributes[1]);
                $this->validate($rules_and_attributes[2], [], $attributes[2]);
                [$ab,$ac,$bc] = $this->makeCombination();
                // $data[] = $this->addCrossOptions($this->cross_abc_input, $ab, $ac, $bc, $this->cross_abc_amt, $this->cross_combination);
                $data[] = $this->addCrossOptions(number: $this->cross_abc_input,
                    ab: $ab, ac: $ac, bc: $bc,
                    amt: $this->cross_abc_amt, comb: $this->cross_combination, option: 'ABC');

                $this->storeCrossAbcIntoCache($data);
                $this->cross_abc_input = $this->cross_abc_amt = $this->cross_combination = '';
                $this->resetCrossError();
                $this->loadAbcData(true);
                $this->dispatch($focus);

                break;

        }
    }

    // Enter AB
    public function enterKeyPressOnCrossAb($focus, $value)
    {
        if ($value == 'cross_ab') {
            $this->validate([
                'cross_ab' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_ab' => 'AB']);
            $this->dispatch($focus);

        } elseif ($value == 'cross_ab_amt') {
            $this->validate([
                'cross_ab' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_ab' => 'AB']);

            $this->validate([
                'cross_ab_amt' => [
                    'required',
                    'min:1', 'integer',
                ]],
                [], ['cross_ab_amt' => 'Amount'],
            );

            $data[] = $this->addCrossOptions(
                ab: $this->cross_ab,
                amt: $this->cross_ab_amt,
                comb: 1,
                number: $this->cross_ab,
                option: 'AB'
            );
            $this->storeCrossAbcIntoCache($data);
            $this->cross_ab = $this->cross_ab_amt = '';
            $this->loadAbcData(true);
            $this->dispatch($focus);

        }

    }

    // Entey AC
    public function enterKeyPressOnCrossAc($focus, $value)
    {
        if ($value == 'cross_ac') {
            $this->validate([
                'cross_ac' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_ac' => 'AC']);
            $this->dispatch($focus);

        } elseif ($value == 'cross_ac_amt') {
            $this->validate([
                'cross_ac' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_ac' => 'AC']);

            $this->validate([
                'cross_ac_amt' => [
                    'required',
                    'min:1', 'integer',
                ]],
                [], ['cross_ac_amt' => 'Amount'],
            );

            $data[] = $this->addCrossOptions(
                ab: $this->cross_ac,
                amt: $this->cross_ac_amt,
                comb: 1,
                number: $this->cross_ac,
                option: 'AC'
            );
            $this->storeCrossAbcIntoCache($data);
            $this->cross_ac = $this->cross_ac_amt = '';
            $this->loadAbcData(true);
            $this->dispatch($focus);

        }

    }

    // Enter BC
    public function enterKeyPressOnCrossBc($focus, $value)
    {
        if ($value == 'cross_bc') {
            $this->validate([
                'cross_bc' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_bc' => 'BC']);
            $this->dispatch($focus);

        } elseif ($value == 'cross_bc_amt') {
            $this->validate([
                'cross_bc' => [
                    'required',
                    'regex:/^(?!.*(.).*\\1)[0-9]{2}$/',
                ],
            ], [], ['cross_bc' => 'BC']);

            $this->validate([
                'cross_bc_amt' => [
                    'required',
                    'min:1', 'integer',
                ]],
                [], ['cross_bc_amt' => 'Amount'],
            );

            $data[] = $this->addCrossOptions(
                ab: $this->cross_bc,
                amt: $this->cross_bc_amt,
                comb: 1,
                number: $this->cross_bc,
                option: 'BC'

            );
            $this->storeCrossAbcIntoCache($data);
            $this->cross_bc = $this->cross_bc_amt = '';
            $this->loadAbcData(true);
            $this->dispatch($focus);

        }

    }
}
