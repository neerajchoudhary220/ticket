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
                                <h4 class="text-white">Completed Draws</h4>
                            </div>
                            <div class="card-body text-center">
                                <h5>{{ $data['total_draws'] }}</h5>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
    @push('custom-js')
        @include('admin.includes.datatable-js-plugins')
        {{ $dataTable->scripts() }}
    @endpush
@endsection
