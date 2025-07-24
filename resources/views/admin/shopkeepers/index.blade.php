@extends('admin.layouts.base')
@section('title','Shopkeeper')
@section('contents')
<div class="container-fluid">
    <h2>Dashboard</h2>
    <div class="row">
        <div class="col-12">
            <div class="card">
               <div class="card-header d-flex justify-content-end">
                    <a href="{{ route('admin.shopkeeper_form') }}" class="btn btn-primary">Add New Shopkeeper <i class="ti ti-plus"></i></a>
                </div>
                <div class="card-body">
                    This is card body
                </div>
            </div>
        </div>
    </div>
</div>
@endsection