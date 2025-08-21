<div class="col-12">
    @php
        $ab_claim = (int) $drawDetail->claim_ab ?? 0;
        $ac_claim = (int) $drawDetail->claim_ac ?? 0;
        $bc_claim = (int) $drawDetail->claim_bc ?? 0;

        $ab_pl = (int) $drawDetail->totalAbAmt() - $ab_claim * 100;
        $ac_pl = (int) $drawDetail->totalAcAmt() - $ac_claim * 100;
        $bc_pl = (int) $drawDetail->totalBcAmt() - $bc_claim * 100;

    @endphp
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
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- admin.draw.details.shopkeeper --}}
                    <tr>
                        <td class="bg-success text-white">AB</td>
                        <td>{{ $drawDetail->totalAbAmt() }}</td>
                        <td>
                            @if ($ab_claim != 0)
                                <a href="{{ route('admin.draw.details.shopkeeper', ['drawDetail' => $drawDetail->id, 'claim' => 1]) }}"
                                    class="text-primary">{{ $ab_claim }}</a>
                            @else
                                {{ $ab_claim }}
                            @endif
                        </td>
                        <td @class([
                            'text-white bg-danger' => $ab_pl < 0,
                            'text-white bg-success' => $ab_pl > 0,
                        ])>{{ $ab_pl }}</td>
                        <td>{{ $drawDetail->ab ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-warning text-white">AC</td>
                        <td>{{ $drawDetail->totalAcAmt() }}</td>

                        <td>
                            @if ($ac_claim != 0)
                                <a href="{{ route('admin.draw.details.shopkeeper', ['drawDetail' => $drawDetail->id, 'claim' => 1]) }}"
                                    class="text-primary">{{ $ac_claim }}</a>
                            @else
                                {{ $ac_claim }}
                            @endif
                        </td>

                        <td @class([
                            'text-white bg-danger' => $ac_pl < 0,
                            'text-white bg-success' => $ac_pl > 0,
                        ])>{{ $ac_pl }}</td>
                        <td>{{ $drawDetail->ac ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-info text-white">BC</td>
                        <td>{{ $drawDetail->totalBcAmt() }}</td>

                        <td>
                            @if ($bc_claim != 0)
                                <a href="{{ route('admin.draw.details.shopkeeper', ['drawDetail' => $drawDetail->id, 'claim' => 1]) }}"
                                    class="text-primary">{{ $bc_claim }}</a>
                            @else
                                {{ $bc_claim }}
                            @endif
                        </td>

                        <td @class([
                            'text-white bg-danger' => $bc_pl < 0,
                            'text-white bg-success' => $bc_pl > 0,
                        ])>{{ $bc_pl }}</td>
                        <td>{{ $drawDetail->bc ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td><b>{{ (int) $drawDetail->totalAbAmt() + (int) $drawDetail->totalAcAmt() + (int) $drawDetail->totalBcAmt() }}</b>
                        </td>
                        <td><b>{{ $ab_claim + $ac_claim + $bc_claim }}
                            </b></td>
                        <td><b>{{ $ab_pl + $ac_pl + $bc_pl }}
                            </b></td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
