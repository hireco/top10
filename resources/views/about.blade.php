@extends('layout',['menu' => $about->title])

@section('page_title', $about->title)

@section('main')
    <div class="page-header">
        <h1>{{ $about->title }}</h1>
    </div>
    <p>{!! $about->content !!}</p>
@stop

@section('script')
    @parent
@stop
