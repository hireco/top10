@extends('home.layout',['menu' => $menu])

@section('page_title', '管理')

@section('main')
	@if(!count($posts))
		<p class="alert alert-warning no-item">目前没有帖子</p>
	@else
		@foreach ($posts as $post)
			<?php
			$topped = $post->top_time > $post->post_time ? 'topped':'';
			?>
			<div class="top-list items-list" id="post_{{ $post->uid }}">

				<h4>
					<a  href="{{ url('home') }}?type=post&uid={{ $post->uid }}">{{ $post->title }}</a>
				</h4>
				<p class="introduction word">{{ mb_substr($post->content,0,100) }}</p>
				<p class="about">
					<span class="timestamp collapse">{{ $post->post_time }}</span>
					<span>来自{{ App\Models\Forum::where('brief',$post->forum)->first()->title }}</span>
				</p>
			</div>
		@endforeach

		<div class="text-center pages">
			{!! $posts->appends(['type' => 'posts'])->render() !!}
		</div>

	@endif

@stop

@section('fixedSection')
    @parent
@stop

@section('script')
    @parent
@stop