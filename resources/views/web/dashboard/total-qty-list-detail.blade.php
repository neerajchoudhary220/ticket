@extends('web.layouts.base')
@section('title', 'GameTicketHub')
@section('contents')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('dashboard') }}" class="btn btn-dark text-white">
                <i class="fa fa-arrow-circle-left"></i> Draw List
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary d-flex justify-content-start">
                            <h4 class="text-white">Details Of Total Qty (Time: {{ $drawDetail->formatEndTime() }})
                            </h4>
                        </div>
                        <div class="card-body">
                            @php
                                $indices = range(0, 9); // 0,1,2,...,9
                            @endphp
                            @php
                                $tq_a = $tq_b = $tq_c = 0;
                                $a_amounts = $b_amounts = $c_amounts = [];
                                foreach ($indices as $i) {
                                    $tq_a += $drawDetail->totalAqty($i);
                                    $a_amounts[] = $drawDetail->totalAqty($i);
                                }
                                foreach ($indices as $i) {
                                    $tq_b += $drawDetail->totalBqty($i);
                                    $b_amounts[] = $drawDetail->totalBqty($i);
                                }
                                foreach ($indices as $i) {
                                    $tq_c += $drawDetail->totalCqty($i);
                                    $c_amounts[] = $drawDetail->totalCqty($i);
                                }

                                $claim_a_amt = $drawDetail->claim_a ? $a_amounts[$drawDetail->claim_a] : 0;
                                $claim_b_amt = $drawDetail->claim_b ? $a_amounts[$drawDetail->claim_b] : 0;
                                $claim_c_amt = $drawDetail->claim_c ? $c_amounts[$drawDetail->claim_c] : 0;

                                $a_pl = $tq_a * 11 - $claim_a_amt * 100;
                                $b_pl = $tq_b * 11 - $claim_b_amt * 100;
                                $c_pl = $tq_c * 11 - $claim_c_amt * 100;

                            @endphp
                            <table class="table table-bordered ">
                                <thead class="">
                                    <tr>
                                        <th>Option</th>
                                        <th>0</th>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                        <th>9</th>
                                        <th>TQ</th>
                                        <th>Claim Q</th>
                                        <th>P & L</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A</td>
                                        @foreach ($indices as $i)
                                            <td>{{ $drawDetail->totalAqty($i) }}</td>
                                        @endforeach
                                        <td>{{ $tq_a }}</td>
                                        <td>{{ $claim_a_amt }}</td>
                                        <td @class([
                                            'bg-danger text-white' => $a_pl < 0,
                                            'bg-success text-white' => $a_pl > 0,
                                        ])>{{ $a_pl }}</td>
                                        <td>{{ $drawDetail->claim_a ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        @foreach ($indices as $i)
                                            <td>{{ $drawDetail->totalBqty($i) }}</td>
                                        @endforeach
                                        <td>{{ $tq_b }}</td>
                                        <td>{{ $claim_b_amt }}</td>
                                        <td @class([
                                            'bg-danger text-white' => $b_pl < 0,
                                            'bg-success text-white' => $b_pl > 0,
                                        ])>{{ $b_pl }}</td>
                                        <td>{{ $drawDetail->claim_b ?? 'N/A' }}</td>

                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        @foreach ($indices as $i)
                                            <td>{{ $drawDetail->totalCqty($i) }}</td>
                                        @endforeach
                                        <td>{{ $tq_c }}</td>
                                        <td>{{ $claim_c_amt }}</td>
                                        <td @class([
                                            'bg-danger text-white' => $c_pl < 0,
                                            'bg-success text-white' => $c_pl > 0,
                                        ])>{{ $c_pl }}</td>
                                        <td>{{ $drawDetail->claim_c ?? 'N/A' }}</td>

                                    </tr>
                                    <tr>
                                        <td colspan="11"><b>Total</b></td>
                                        <td class="text-success"><b>{{ $tq_a + $tq_b + $tq_c }}</b></td>
                                        <td class="text-warning"><b>{{ $claim_a_amt + $claim_b_amt + $claim_c_amt }}</b>
                                        </td>
                                        <td><b>{{ $a_pl + $b_pl + $c_pl }}</b></td>

                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('custom-js')
    @endpush

@endsection
