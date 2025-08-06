<div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    @include('livewire.ticket-data-form')
                </div>
                <div class="col-12 mt-3">
                    @include('livewire.number-display-list')
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-12">
                    @include('livewire.ticket-list')
                </div>
                <div class="col-12 mt-3">
                    @include('livewire.draw-list')
                </div>
            </div>
        </div>
    </div>

</div>
@script
    <script>
        $(document).ready(function() {

            $(document).on('click', '.draw_checkbox', function() {
                const drawId = $(this).val();
                const isChecked = $(this).is(':checked') ? 1 : 0;
                $wire.dispatch('draw-selected', {
                    'drawId': drawId,
                    'isChecked': isChecked
                });
            })
            $wire.on('check-selected-draw', (event) => {
                const totalSelectedDraw = event.total_selected_draw;
                const drawId = event.drawId;

                if (totalSelectedDraw == 0) {
                    $(`#draw_${drawId}`).prop('checked', true);


                    $wire.dispatch('draw-selected', {
                        'drawId': drawId,
                        'isChecked': 1
                    });
                }
            })

            //checked




        })
    </script>
@endscript
