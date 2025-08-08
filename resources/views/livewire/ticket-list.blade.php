<div>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5>Ticket List</h5>
        </div>
        <div class="card-body">
            <div class="ticket-list-box ticket-scroller-box" style="height:151px;">
                @foreach ($ticket_list as $ticket)
                    <div class="form-check" wire:key="draw-{{ $ticket->id }}">
                        <input class="form-check-input" type="radio" name="selected_ticket"
                            id="ticket_{{ $ticket->id }}" value="{{ $ticket->ticket_number }}"
                            @checked($ticket->id == $current_ticket_id) wire:click="handleTicketSelect({{ $ticket->id }})">

                        <label class="form-check-label" for="ticket_{{ $ticket->id }}">
                            {{ $ticket->ticket_number }}
                        </label>
                    </div>
                @endforeach

            </div>
        </div>

    </div>
</div>
