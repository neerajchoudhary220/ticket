@extends('admin.layouts.base')
@section('title', 'Test Title')
@section('contents')
    <div class="container-fluid">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-3">
                <div class="card" role="button" onclick="window.location.href='{{ route('admin.shopkeepers') }}'">
                    <div class="card-header bg-primary text-white d-flex justify-content-center">
                        <h4 class="text-white">ShopKeepers</h4>
                    </div>
                    <div class="card-body text-center">
                        <h5>{{ $data['total_shopkeepers'] }}</h5>
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card" role="button" onclick="window.location.href='{{ route('admin.draw') }}'">
                    <div class="card-header bg-info text-white d-flex justify-content-center">
                        <h4 class="text-white">Draws</h4>
                    </div>
                    <div class="card-body text-center">
                        <h5>{{ $data['total_draws'] }}</h5>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
