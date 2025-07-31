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
                        <div class="card-header d-flex justify-content-center">
                            <h5>Draw List</h5>
                        </div>
                        <div class="card-body">
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
