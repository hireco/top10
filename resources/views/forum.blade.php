<?php
  $forum = App\Models\Forum::where('brief',$forum)->first();
  $menu = $forum -> title;
?>
@extends('layout',['menu' => $menu])

@section('page_title', $menu)

@section('main')
    @if(!count($posts))
        <p class="alert alert-warning no-item">该站暂无热帖，请稍后再来。</p>
    @else
		@foreach ($posts as $post)
			<a class="top-list" href="{{ url('post/'.$post->uid) }}">
				<h4>{{ $post->title }}</h4>
				<p class="introduction word">{{ mb_substr($post->content,0,100) }}</p>
				<p class="about">
					<span class="timestamp collapse">{{ $post->post_time }}</span>
					<span>来自{{ $menu }}</span>
				</p>
			</a>
		@endforeach
    @endif
@stop

@section('script')
    @parent
@stop
