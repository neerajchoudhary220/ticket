@extends('admin.layouts.base')
@section('title','Shopkeeper')
@section('contents')
<div class="container-fluid">
    <h2>Add Shopkeeper</h2>
<div>
    @livewire('admin.shop-keeper-form')
</div>
</div>
@endsection