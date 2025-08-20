<div class="col-12">
    @php
        $ab_claim = $drawDetail->claim_ab ?? 0;
        $ac_claim = $drawDetail->claim_ac ?? 0;
        $bc_claim = $drawDetail->claim_bc ?? 0;

        $ab_pl = $drawDetail->totalAbAmt() - $ab_claim * 100;
        $ac_pl = $drawDetail->totalAcAmt() - $ac_claim * 100;
        $bc_pl = $drawDetail->totalBcAmt() - $bc_claim * 100;

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
                    <tr>
                        <td class="bg-success text-white">AB</td>
                        <td>{{ $drawDetail->totalAbAmt() }}</td>
                        <td>{{ $ab_claim }}</td>
                        <td @class([
                            'text-white bg-danger' => $ab_pl < 0,
                            'text-white bg-success' => $ab_pl > 0,
                        ])>{{ $ab_pl }}</td>
                        <td>{{ $drawDetail->ab ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-warning text-white">AC</td>
                        <td>{{ $drawDetail->totalAcAmt() }}</td>

                        <td>{{ $ac_claim }}</td>

                        <td @class([
                            'text-white bg-danger' => $ac_pl < 0,
                            'text-white bg-success' => $ac_pl > 0,
                        ])>{{ $ac_pl }}</td>
                        <td>{{ $drawDetail->ac ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-info text-white">BC</td>
                        <td>{{ $drawDetail->totalBcAmt() }}</td>

                        <td>{{ $bc_claim }}</td>

                        <td @class([
                            'text-white bg-danger' => $bc_pl < 0,
                            'text-white bg-success' => $bc_pl > 0,
                        ])>{{ $bc_pl }}</td>
                        <td>{{ $drawDetail->bc ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td><b>{{ $drawDetail->totalAbAmt() + $drawDetail->totalAcAmt() + $drawDetail->totalBcAmt() }}</b>
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
