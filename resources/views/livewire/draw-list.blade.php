<div class="card">
    <div class="card-header bg-success text-white">
        <h5>Draw List</h5>
    </div>
    <div class="card-body">
        <div class="ticket-list-box draw-box" style="height:270px;">
            @foreach ($draw_list as $draw)
                <div class="form-check" wire:key="draw-{{ $draw->id }}">
                    <input class="form-check-input draw_checkbox" type="checkbox" id="draw_{{ $draw->id }}"
                        value="{{ $draw->id }}" @checked($draw->id == $selected_draw_id)>
                    <label class="form-check-label" for="draw_{{ $draw->id }}">
                        {{ $draw->formatEndTime() }}
                        (TC:{{ $draw->totalCollection($draw->id) }},
                        TD:{{ $draw->totalDistributions($draw->id) }})
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
