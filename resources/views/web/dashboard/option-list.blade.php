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
                        <div class="card-header d-flex justify-content-end">
                            <div class="me-auto">
                                <div class="d-flex justify-content-start">
                                    <h5>
                                        <a href="{{ route('dashboard') }}" class="btn btn-dark text-white">
                                            <i class="fa fa-arrow-circle-left"></i> Draw List
                                        </a>
                                    </h5>
                                    <h5 class="ms-2">
                                        Draw No:{{ $draw->id }}
                                    </h5>

                                </div>
                            </div>

                            <h5>Draw Details</h5>
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
