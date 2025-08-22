<div>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5>Ticket List</h5>
        </div>
        <div class="card-body">
            <div class="ticket-list-box ticket-scroller-box" style="height:151px;">
                @foreach ($ticket_list as $ticket_number)
                    <div class="form-check" wire:key="draw-{{ $ticket_number }}">
                        <input class="form-check-input" type="radio" name="selected_ticket"
                            id="ticket_{{ $ticket_number }}" value="{{ $ticket_number }}" @checked($ticket_number == $selected_ticket_number)
                            wire:click="handleTicketSelect('{{ $ticket_number }}')">

                        <label class="form-check-label" for="ticket_{{ $ticket_number }}">
                            {{ $ticket_number }}
                        </label>
                    </div>
                @endforeach

            </div>
        </div>

    </div>
</div>
