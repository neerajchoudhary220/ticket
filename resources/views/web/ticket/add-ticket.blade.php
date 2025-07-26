@extends('web.layouts.base')
@section('title','GameTicketHub')
@section('contents')
<div class="card">
    <div class="card-header d-flex justify-content-start">
        <h5 class="me-auto">New Ticket</h5>
    </div>
    <div class="card-body">
        @livewire('add-ticket-form')
    </div>
</div>
@endsection