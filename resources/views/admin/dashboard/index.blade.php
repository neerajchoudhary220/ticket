@extends('admin.layouts.base')
@section('title', 'Test Title')
@section('contents')
    <div class="container-fluid">
        <h2>Dashboard</h2>
        <div class="row mb-3">

            <div class="col-8">
                <div class="card">
                    <div class="card-header text-center bg-info">
                        <h4 class="text-white">Draw's Details</h4>

                    </div>
                    <div class="card-body">
                        <x-date-range-picker-filter />

                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card" role="button"
                            onclick="window.location.href='{{ route('admin.shopkeepers') }}'">
                            <div class="card-header bg-primary text-white d-flex justify-content-center">
                                <h4 class="text-white">ShopKeepers</h4>
                            </div>
                            <div class="card-body text-center">
                                <h5>{{ $data['total_shopkeepers'] }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card" role="button" onclick="window.location.href='{{ route('admin.draw') }}'">
                            <div class="card-header bg-info text-white d-flex justify-content-center">
                                <h4 class="text-white">Draw Overview</h4>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        Draws: <strong>{{ $data['total_draws'] }}</strong>

                                    </div>
                                    <div class="col-6">
                                        Claimed: <strong>{{ $data['claimed'] }}</strong>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
        @livewire('claim-add')
    </div>
    @push('custom-js')
        @include('admin.includes.datatable-js-plugins')
        {{ $dataTable->scripts() }}
        <script>
            $(document).ready(function() {
                $(".otp-input").on("input", function() {
                    // Allow only digits
                    this.value = this.value.replace(/[^0-9]/g, "");

                    // Auto focus next input if filled
                    if (this.value.length === 1) {
                        $(this).next(".otp-input").focus();
                    }
                }).on("keydown", function(e) {
                    // Move to previous input on backspace if empty
                    if (e.key === "Backspace" && this.value === "") {
                        $(this).prev(".otp-input").focus();
                    }
                });

                //click to claim button
                $(document).on('click', '.addClaim', function() {
                    const claimId = $(this).data('draw-detail-id');
                    Livewire.dispatch('claim-event', {
                        'draw_details_id': claimId
                    });
                    // alert(claimId);

                });
            });
        </script>
    @endpush
@endsection
