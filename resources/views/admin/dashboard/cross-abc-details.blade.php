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
                        <h4 class="text-white">Details Of {{ $type }} (Time: {{ $drawDetail->formatEndTime() }})
                        </h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active h5 cross_abc_tab text-success" role="button"role="button"
                                    data-bs-toggle="tab" data-type='AB'>AB</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link h5 cross_abc_tab text-warning" data-type='AC' id="ac_tab"
                                    role="button" data-bs-toggle="tab">AC</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link h5 cross_abc_tab text-info" data-type='BC' id="bc_tab" role="button"
                                    data-bs-toggle="tab">BC</a>
                            </li>
                        </ul>

                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('custom-js')
        @include('admin.includes.datatable-js-plugins')
        {{ $dataTable->scripts() }}
        <script>
            $(document).ready(function() {
                let url = new URL(window.location.href);
                let currentType = url.searchParams.get("type");

                if (currentType) {
                    $(".cross_abc_tab").removeClass("active");
                    $(".cross_abc_tab[data-type='" + currentType + "']").addClass("active");
                }
            })
            $(document).on("click", ".cross_abc_tab", function(e) {
                e.preventDefault();

                let type = $(this).data("type"); // get clicked tab type
                let url = new URL(window.location.href);

                // update or set type query param
                url.searchParams.set("type", type);

                // reload page with updated query
                window.location.href = url.toString();
            });
        </script>
    @endpush

@endsection
