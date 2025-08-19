@extends('admin.layouts.base')
@section('title', 'Cross ABC Details')
@section('contents')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Details of Total Qty: {{ $drawDetail->total_qty }}
                    {{ $drawDetail->formatEndTime() }}</li>
            </ol>
        </nav>
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

                            $a_pl = $tq_a * 11 - $a_amounts[$drawDetail->claim_a] * 100;
                            $b_pl = $tq_b * 11 - $b_amounts[$drawDetail->claim_b] * 100;
                            $c_pl = $tq_c * 11 - $c_amounts[$drawDetail->claim_c] * 100;

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
                                    <th>tq</th>
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
                                    <td>{{ $a_amounts[$drawDetail->claim_a] }}</td>
                                    <td @class([
                                        'bg-danger text-white' => $a_pl < 0,
                                        'bg-success text-white' => $a_pl > 0,
                                    ])>{{ $a_pl }}</td>
                                    <td>{{ $drawDetail->claim_a }}</td>
                                </tr>
                                <tr>
                                    <td>B</td>
                                    @foreach ($indices as $i)
                                        <td>{{ $drawDetail->totalBqty($i) }}</td>
                                    @endforeach
                                    <td>{{ $tq_b }}</td>
                                    <td>{{ $b_amounts[$drawDetail->claim_b] }}</td>
                                    <td @class([
                                        'bg-danger text-white' => $b_pl < 0,
                                        'bg-success text-white' => $b_pl > 0,
                                    ])>{{ $b_pl }}</td>
                                    <td>{{ $drawDetail->claim_b }}</td>

                                </tr>
                                <tr>
                                    <td>C</td>
                                    @foreach ($indices as $i)
                                        <td>{{ $drawDetail->totalCqty($i) }}</td>
                                    @endforeach
                                    <td>{{ $tq_c }}</td>
                                    <td>{{ $c_amounts[$drawDetail->claim_c] }}</td>
                                    <td @class([
                                        'bg-danger text-white' => $c_pl < 0,
                                        'bg-success text-white' => $c_pl > 0,
                                    ])>{{ $c_pl }}</td>
                                    <td>{{ $drawDetail->claim_c }}</td>

                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('custom-js')
    @endpush

@endsection
