@extends('web.layouts.base')
@section('title', 'GameTicketHub')
@section('contents')
    @push('custom-css')
        @include('admin.includes.datatable-css-plugins')
    @endpush
    <div class="card">
        <div class="card-header">
            <a href="{{ route('dashboard') }}" class="btn btn-dark text-white">
                <i class="fa fa-arrow-circle-left"></i> Draw List
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="text-white">Details Of Cross ABC</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered ">
                                <thead class="bg-dark">
                                    <tr>
                                        <th style="width: 50px;">Option</th>
                                        <th>Cross Amt</th>
                                        <th>Claim</th>
                                        <th class="w-25">P&L <small>(Cross Amt. - Claim &times; 100)</small>
                                        </th>
                                        <th style="width: 50px;">Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-success text-white">AB</td>
                                        <td>{{ $totalAbCrossAmt }}</td>
                                        <td>
                                            @if ($totalAbCrossClaimAmt != 0)
                                                <a href="{{ route('dashboard.draw.details.list', ['draw_detail_id' => $drawDetail->id, 'claim' => 1]) }}"
                                                    class="text-primary">{{ $totalAbCrossClaimAmt }}</a>
                                            @else
                                                {{ $totalAbCrossClaimAmt }}
                                            @endif
                                        </td>
                                        <td @class([
                                            'text-white bg-danger' => $ab_pl < 0,
                                            'text-white bg-success' => $ab_pl > 0,
                                        ])>{{ $ab_pl }}</td>
                                        <td class="bg-success text-white">{{ $drawDetail->ab ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-warning text-white">AC</td>
                                        <td>{{ $totalAcCrossAmt }}</td>

                                        <td>
                                            @if ($totalAcCrossClaimAmt != 0)
                                                <a href="{{ route('dashboard.draw.details.list', ['draw_detail_id' => $drawDetail->id, 'claim' => 1]) }}"
                                                    class="text-primary">{{ $totalAcCrossClaimAmt }}</a>
                                            @else
                                                {{ $totalAcCrossClaimAmt }}
                                            @endif
                                        </td>

                                        <td @class([
                                            'text-white bg-danger' => $ac_pl < 0,
                                            'text-white bg-success' => $ac_pl > 0,
                                        ])>{{ $ac_pl }}</td>
                                        <td class="bg-warning text-white">{{ $drawDetail->ac ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-info text-white">BC</td>
                                        <td>{{ $totalBcCrossAmt }}</td>

                                        <td>
                                            @if ($totalBcCrossClaimAmt != 0)
                                                <a href="{{ route('dashboard.draw.details.list', ['draw_detail_id' => $drawDetail->id, 'claim' => 1]) }}"
                                                    class="text-primary">{{ $totalBcCrossClaimAmt }}</a>
                                            @else
                                                {{ $totalBcCrossClaimAmt }}
                                            @endif
                                        </td>

                                        <td @class([
                                            'text-white bg-danger' => $bc_pl < 0,
                                            'text-white bg-success' => $bc_pl > 0,
                                        ])>{{ $bc_pl }}</td>
                                        <td class="bg-info text-white">{{ $drawDetail->bc ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td><b>{{ $totalAbCrossAmt + $totalBcCrossAmt + $totalAcCrossAmt }}</b>
                                        </td>
                                        <td><b>{{ $totalAbCrossClaimAmt + $totalBcCrossClaimAmt + $totalAcCrossClaimAmt }}
                                            </b></td>
                                        <td><b>{{ $ab_pl + $ac_pl + $bc_pl }}
                                            </b></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
            <hr>
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h6 class="text-white">Details Of AB (Time: {{ $drawDetail->formatEndTime() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            {{ $dataTable->table() }}

                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h6 class="text-white">Details Of AC (Time: {{ $drawDetail->formatEndTime() }})</h6>
                        </div>
                        <div class="card-body">
                            {!! $crossAcDataTable->table() !!}
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h6 class="text-white">Details Of BC (Time: {{ $drawDetail->formatEndTime() }})</h6>
                        </div>
                        <div class="card-body">
                            {!! $crossBcDataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('custom-js')
        @include('admin.includes.datatable-js-plugins')
        {{ $dataTable->scripts() }}
        {!! $crossAcDataTable->scripts() !!}
        {!! $crossBcDataTable->scripts() !!}
    @endpush

@endsection
