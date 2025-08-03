<div class="card">
    <div class="card-header bg-info text-white">
        <h5>Ticket List</h5>
    </div>
    <div class="card-body">
        <div class="ticket-list-box">
            @foreach ($ticket_list as $ticket)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="selected_ticket" id="ticket_{{ $ticket->id }}"
                        value="{{ $ticket->ticket_number }}">
                    <label class="form-check-label" for="ticket_{{ $ticket->id }}">
                        {{ $ticket->ticket_number }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
