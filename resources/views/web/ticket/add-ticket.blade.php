@extends('web.layouts.base')
@section('title', 'GameTicketHub')
@section('contents')
    @push('custom-css')
        {{-- <style>
            [x-cloak] {
                display: none !important;
            }
        </style> --}}
    @endpush
    <div class="card">
        <div class="card-header d-flex justify-content-start">
            @if ($ticket)
                <a href="{{ route('dashboard') }}" class="btn btn-dark text-white">
                    <i class="fa fa-arrow-circle-left"></i> Ticket List
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-dark text-white">
                    <i class="fa fa-arrow-circle-left"></i> Dashboard
                </a>
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
            document.addEventListener('livewire:init', () => {})

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

                //
            })

            document.addEventListener('checked-draws', e => {
                const drawIds = e.detail.drawIds;
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
            //   $wire.on('checked-draws', (event) => {


            // });
        </script>
    @endpush
@endsection
