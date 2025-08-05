<div>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5>Ticket List</h5>
        </div>
        <div class="card-body">
            <div x-data="{
                page: 0,
                loading: false,
                init() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.loading) {
                                this.page++;
                                this.loading = true;
                                $wire.set('ticket_page', this.page).then(() => {
                                    this.loading = false;
                                });
                            }
                        });
                    }, {
                        threshold: 0
                    });
            
                    this.$nextTick(() => {
                        if (this.$refs.loader) {
                            observer.observe(this.$refs.loader);
                        }
                    });
                }
            }" x-init="init" class="ticket-list-box">
                @foreach ($ticket_list as $ticket)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="selected_ticket"
                            id="ticket_{{ $ticket->id }}" value="{{ $ticket->ticket_number }}">

                        <label class="form-check-label" for="ticket_{{ $ticket->id }}">
                            {{ $ticket->ticket_number }}
                        </label>
                    </div>
                @endforeach
                <div x-ref="loader" class="text-center mt-3">
                    <div x-show="loading" x-cloak></div>
                </div>
            </div>
        </div>

    </div>
</div>
