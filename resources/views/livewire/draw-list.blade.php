<div class="card">
    <div class="card-header bg-success text-white">
        <h5>Draw List</h5>
    </div>
    <div class="card-body">
        <div class="ticket-list-box draw-box" style="height:270px;">
            @foreach ($draw_list as $draw_detail)
                <div class="form-check" wire:key="draw-{{ $draw_detail->id }}">
                    <input class="form-check-input draw_checkbox" type="checkbox" id="draw_{{ $draw_detail->id }}"
                        value="{{ $draw_detail->id }}" @checked($draw_detail->id == $selected_draw_id)>
                    <label class="form-check-label" for="draw_{{ $draw_detail->id }}">
                        {{ $draw_detail->formatEndTime() }}
                        (<strong>Cross Amt:</strong>
                        {{ $draw_detail->totalAbAmt(auth()->user()->id) + $draw_detail->totalAcAmt(auth()->user()->id) + $draw_detail->totalBcAmt(auth()->user()->id) }}
                        <strong>TQ: </strong>
                        {{ $draw_detail->totalAqty(user_id: auth()->user()->id) + $draw_detail->totalBqty(user_id: auth()->user()->id) + $draw_detail->totalCqty(user_id: auth()->user()->id) }})
                    </label>
                </div>
            @endforeach


        </div>


    </div>
    {{-- <div class="card-footer">
        <div>
            Selected Draw IDs: @json($selected_draw)
        </div>
    </div> --}}
</div>
