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

            //checked draws
            $wire.on('checked-draws', (event) => {
                const drawIds = event.drawIds;
                $(document).find(".draw_checkbox").prop('checked', false);
                if (Array.isArray(drawIds) && drawIds.length > 0) {
                    drawIds.forEach(drawId => {
                        $(`#draw_${drawId}`).prop('checked', true);
                    });
                } else if (typeof drawIds === 'object' && Object.keys(drawIds).length > 0) {
                    Object.values(drawIds).forEach(drawId => {
                        $(`#draw_${drawId}`).prop('checked', true);
                    });
                }

            });




            //ticket scrollbar
            $('.ticket-scroller-box').on('scroll', function() {
                let box = $(this);
                let scrollTop = box.scrollTop();
                let innerHeight = box.innerHeight();
                let scrollHeight = box[0].scrollHeight;
                let ticket_page = $wire.get('ticket_page');

                if (scrollTop + innerHeight >= scrollHeight - 10) {
                    ticket_page++;
                    $wire.set('ticket_page', ticket_page);
                }
            });

            //draw scrollbar
            $('.draw-box').on('scroll', function() {
                let box = $(this);
                let scrollTop = box.scrollTop();
                let innerHeight = box.innerHeight();
                let scrollHeight = box[0].scrollHeight;
                let draw_page = $wire.get('draw_page');

                if (scrollTop + innerHeight >= scrollHeight - 10) {
                    draw_page++;
                    $wire.set('draw_page', draw_page);
                }
            });

            //option list scrollbar
            $('.option-list').on('scroll', function() {
                let box = $(this);
                let scrollTop = box.scrollTop();
                let innerHeight = box.innerHeight();
                let scrollHeight = box[0].scrollHeight;
                let option_page = $wire.get('option_page');

                if (scrollTop + innerHeight >= scrollHeight - 10) {
                    option_page++;
                    $wire.set('option_page', option_page);
                }
            });

            //refresh window
            $wire.on('refresh-window', () => {
                window.location.reload();
            })



        })
    </script>
@endscript
