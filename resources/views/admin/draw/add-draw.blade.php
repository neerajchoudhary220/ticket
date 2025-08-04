@extends('admin.layouts.base')
@section('title', 'Draw')
@section('contents')
    <div class="container-fluid">
        @if ($draw)
            <h2>Edit Draw</h2>
        @else
            <h2>Add Draw</h2>
        @endif

        <div>
            <div>
                @livewire('admin.add-draw', ['draw_id' => $draw->id])
            </div>
        @endsection
