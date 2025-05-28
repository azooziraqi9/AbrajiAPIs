@extends('manager::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('manager.name') !!}</p>
@endsection
