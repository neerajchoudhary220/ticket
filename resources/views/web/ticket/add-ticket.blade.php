@extends('web.layouts.base')
@section('title', 'GameTicketHub')
@section('contents')
    <div class="card">
        <div class="card-header d-flex justify-content-start">
            @if ($ticket)
                <a href="{{ route('dashboard.draw.ticket.number.list', ['draw_id' => $ticket->draw->id, 'number' => $number]) }}"
                    class="btn btn-dark text-white">
                    <i class="fa fa-arrow-circle-left"></i> Ticket List
                </a>
            @else
                <h5 class="me-auto">New Ticket</h5>
            @endif
        </div>
        <div class="card-body">
            @if ($ticket)
                @livewire('add-ticket-form', ['ticket' => $ticket])
            @else
                @livewire('add-ticket-form')
            @endif
        </div>
    </div>
    @push('custom-js')
        <script>
            document.addEventListener('livewire:init', () => {
                console.log("working livewire/..")
            })

            $(document).ready(function() {
                $(document).on('input', '.zeroToNineNumber', function() {
                    let original = $(this).val();

                    // Remove non-digit characters
                    let cleaned = original.replace(/\D/g, '');

                    // Remove duplicates
                    let uniqueDigits = '';
                    for (let i = 0; i < cleaned.length; i++) {
                        if (!uniqueDigits.includes(cleaned[i])) {
                            uniqueDigits += cleaned[i];
                        }
                    }

                    // Update the input field
                    $(this).val(uniqueDigits);
                }).on('input', '.number_qty', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                })


            })
        </script>
    @endpush
@endsection
