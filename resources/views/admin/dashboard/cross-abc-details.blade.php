@extends('admin.layouts.base')
@section('title', 'Cross ABC Details')
@section('contents')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

                <li class="breadcrumb-item active">Details of {{ $drawDetail->formatEndTime() }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary d-flex justify-content-start">
                        <h4 class="text-white">Details Of (Time: {{ $drawDetail->formatEndTime() }})
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            @include('admin.dashboard.cross-abc-details-list')
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h6 class="text-white">Details Of AB (Time: {{ $drawDetail->formatEndTime() }})
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        {{ $dataTable->table() }}

                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header bg-warning">
                                        <h6 class="text-white">Details Of AC (Time: {{ $drawDetail->formatEndTime() }})</h6>
                                    </div>
                                    <div class="card-body">
                                        {!! $crossAcDataTable->table() !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header bg-info">
                                        <h6 class="text-white">Details Of BC (Time: {{ $drawDetail->formatEndTime() }})</h6>
                                    </div>
                                    <div class="card-body">
                                        {!! $crossBcDataTable->table() !!}
                                    </div>
                                </div>
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
        {!! $crossAcDataTable->scripts() !!}
        {!! $crossBcDataTable->scripts() !!}
    @endpush

@endsection
