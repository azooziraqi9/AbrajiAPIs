@extends('card::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('card.name') !!}</p>
@endsection
