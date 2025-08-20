<div class="card">
    <div class="card-header bg-primary text-white">
        <h4>Draw Details</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive latest-draw-list" style="max-height: 250px; overflow-y: auto; ">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-light position-sticky top-0" style="z-index: 1;">
                    <tr>
                        <th>#</th>
                        <th>Time</th>
                        <th>TQ</th>
                        <th>Cross Amt.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latest_draw_list as $draw_detail)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $draw_detail->formatEndTime() }}</td>
                            <td>{{ $draw_detail->totalAqty(user_id: auth()->user()->id) + $draw_detail->totalBqty(user_id: auth()->user()->id) + $draw_detail->totalCqty(user_id: auth()->user()->id) }}
                            </td>
                            <td>{{ $draw_detail->totalAbAmt(auth()->user()->id) + $draw_detail->totalAcAmt(auth()->user()->id) + $draw_detail->totalBcAmt(auth()->user()->id) }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No records found.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>
