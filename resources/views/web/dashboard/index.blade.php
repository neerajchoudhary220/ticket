@extends('web.layouts.base')
@section('title', 'GameTicketHub')
@section('contents')
    @push('custom-css')
        @include('admin.includes.datatable-css-plugins')
    @endpush
    <div class="card">
        <div class="card-header">
            <h4>Dashboard</h4>

        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-start">
                                    <h5 class="me-auto">Draw List</h5>
                                    <a href="{{ route('ticket.add') }}" class="btn btn-primary">Add A New Ticket <i
                                            class="fa fa-ticket"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <x-date-range-picker-filter />

                            {{ $dataTable->table() }}
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
