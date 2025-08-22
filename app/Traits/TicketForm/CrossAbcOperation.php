<?php

namespace App\Traits\TicketForm;

use App\Models\CrossAbc;
use App\Models\CrossAbcDetail;
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

    public $cross_a;

    public $cross_b;

    public $cross_c;

    public $cross_single_amount;

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
                $this->AddTicketAndDrawDetailsIntoCrossAbc($this->selected_draw);
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
            $this->AddTicketAndDrawDetailsIntoCrossAbc($this->selected_draw);
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
            $this->AddTicketAndDrawDetailsIntoCrossAbc($this->selected_draw);

            $this->dispatch($focus);

        }

    }

    public function generateRegularCombinations()
    {
        $a = $this->cross_a;
        $b = $this->cross_b;
        $c = $this->cross_c;

        // Convert each into array of digits only if not null/empty
        $aDigits = $a ? str_split((string) $a) : [];
        $bDigits = $b ? str_split((string) $b) : [];
        $cDigits = $c ? str_split((string) $c) : [];

        // Helper closure to generate combinations
        $makePairs = function ($x, $y) {
            $pairs = [];
            foreach ($x as $dx) {
                foreach ($y as $dy) {
                    $pairs[] = (int) ($dx.$dy);
                }
            }

            return $pairs;
        };

        // Generate only if both inputs exist
        $ab = (! empty($aDigits) && ! empty($bDigits)) ? $makePairs($aDigits, $bDigits) : [];
        $ac = (! empty($aDigits) && ! empty($cDigits)) ? $makePairs($aDigits, $cDigits) : [];
        $bc = (! empty($bDigits) && ! empty($cDigits)) ? $makePairs($bDigits, $cDigits) : [];

        // Total count
        $total = count($ab) + count($ac) + count($bc);

        return [
            $ab,
            $ac,
            $bc,
            $total,
        ];
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
            $this->AddTicketAndDrawDetailsIntoCrossAbc($this->selected_draw);
            $this->dispatch($focus);

        }

    }

    // Enter A B and C
    public function enterKeyPressOnCrossA($focus, $value)
    {
        switch ($value) {
            case 'cross_a':
                // $this->validate(['cross_a' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_a' => 'A']);
                $this->dispatch($focus);
                break;
            case 'cross_b':
                // $this->validate(['cross_a' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_a' => 'A']);
                // $this->validate(['cross_b' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_b' => 'B']);
                $this->dispatch($focus);
                break;
            case 'cross_c':
                // $this->validate(['cross_a' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_a' => 'A']);
                // $this->validate(['cross_b' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_b' => 'B']);
                // $this->validate(['cross_c' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_c' => 'C']);
                $this->dispatch($focus);
                break;
            case 'cross_single_amount':
                // $this->validate(['cross_a' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_a' => 'A']);
                // $this->validate(['cross_b' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_b' => 'B']);
                // $this->validate(['cross_c' => [
                //     'sometimes', 'integer', 'min:1',
                // ]], [], ['cross_c' => 'C']);

                // $this->validate(['cross_single_amount' => [
                //     'required', 'integer', 'min:1',
                // ]], [], ['cross_single_amount' => 'Amount']);

                [$ab,$ac,$bc,$comb] = $this->generateRegularCombinations();
                $data[] = $this->addCrossOptions(
                    ab: $ab,
                    ac: $ac,
                    bc: $bc,
                    amt: $this->cross_single_amount,
                    comb: $comb,
                    number: $this->cross_a.'-'.$this->cross_b.'-'.$this->cross_c,
                    option: 'A-B-C'
                );
                $this->storeCrossAbcIntoCache($data);
                $this->cross_a = $this->cross_b = $this->cross_c = $this->cross_single_amount = '';
                $this->AddTicketAndDrawDetailsIntoCrossAbc($this->selected_draw);
                $this->dispatch($focus);
                break;
        }
    }

    public function AddTicketAndDrawDetailsIntoCrossAbc(array $selected_draw_ids): void
    {
        $selected_ticket_id = $this->current_ticket_id;
        $crossAbc = $this->getCrossOptions()
            ->map(function ($crossData) use ($selected_draw_ids, $selected_ticket_id) {
                $crossData['draw_details_ids'] = $selected_draw_ids;
                $crossData['ticket_id'] = $selected_ticket_id;

                return $crossData;
            })
            ->values()
            ->all();
        Cache::put('cross_abc', $crossAbc, 7200);
        $this->loadAbcData(true);
        $this->calculateCrossFinalTotal();

    }

    public function saveCrossAbc()
    {
        $cross_data = $this->getCrossOptions();
        foreach ($cross_data as $data) {
            $cross_input_data = [
                'number' => $data['number'],
                'combination' => $data['combination'],
                'option' => $data['option'],
                'draw_details_ids' => array_map('intval', array_values($data['draw_details_ids'])),
                'ticket_id' => $this->current_ticket_id,
                'ab' => $data['ab'],
                'ac' => $data['ac'],
                'bc' => $data['bc'],
                'amt' => $data['amt'],
                'user_id' => $this->auth_user->id,
            ];
            CrossAbc::create($cross_input_data);
        }

    }

    public function saveCrossAbcDetail()
    {
        $cross_data = $this->getCrossOptions();
        $cross_abc = collect(['ab', 'ac', 'bc'])->mapWithKeys(function ($key) use ($cross_data) {
            $items = $cross_data->flatMap(function ($row) use ($key) {
                return collect($row[$key])->map(function ($number) use ($row) {
                    return [
                        'number' => $number,
                        'amount' => $row['amt'],
                        'combination' => $row['combination'],
                        'option' => $row['option'],
                        'ticket_id' => $this->current_ticket_id,
                        'user_id' => $this->auth_user->id,
                    ];
                });
            })->values();

            return [$key => $items];
        })->toArray();

        $bulkData = [];

        // collect ab
        $ab_data = $cross_abc['ab'];
        $ac_data = $cross_abc['ac'];
        $bc_data = $cross_abc['bc'];
        foreach ($this->selected_draw as $draw_id) {
            // ab
            if (count($ab_data) > 0) {
                foreach ($ab_data as $d) {
                    $bulkData[] = [
                        'number' => $d['number'],
                        'amount' => $d['amount'],
                        'combination' => $d['combination'],
                        'option' => $d['option'],
                        'ticket_id' => $this->current_ticket_id,
                        'user_id' => $this->auth_user->id,
                        'draw_detail_id' => $draw_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'type' => 'AB',
                    ];
                }
            }
            // ac
            if (count($ac_data) > 0) {
                foreach ($ac_data as $d) {
                    $bulkData[] = [
                        'number' => $d['number'],
                        'amount' => $d['amount'],
                        'combination' => $d['combination'],
                        'option' => $d['option'],
                        'ticket_id' => $this->current_ticket_id,
                        'user_id' => $this->auth_user->id,
                        'draw_detail_id' => $draw_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'type' => 'AC',
                    ];
                }
            }
            // bc
            if (count($bc_data) > 0) {
                foreach ($bc_data as $d) {
                    $bulkData[] = [
                        'number' => $d['number'],
                        'amount' => $d['amount'],
                        'combination' => $d['combination'],
                        'option' => $d['option'],
                        'ticket_id' => $this->current_ticket_id,
                        'user_id' => $this->auth_user->id,
                        'draw_detail_id' => $draw_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'type' => 'BC',
                    ];
                }
            }
        }
        // Insert in chunks of 500 (or whatever size you prefer)
        collect($bulkData)->chunk(500)->each(function ($chunk) {
            CrossAbcDetail::insert($chunk->toArray());
        });

    }
}
