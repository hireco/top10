@extends('layout',['menu' => '全站'])

@section('page_title', '全站十大头条')

@section('main')

    @if(!count($posts))
        <p class="alert alert-warning no-item">今日十大尚未获取，请稍候。</p>
    @else 
		@foreach ($posts as $post)
			<a class="top-list" href="{{ url('post/'.$post->uid) }}">
				<h4>{{ $post->title }}</h4>
				<p class="introduction word">{{ mb_substr($post->content,0,100) }}</p>
				<p class="about">
					<span class="timestamp collapse">{{ $post->post_time }}</span>
					<span>来自{{ App\Models\Forum::where('brief',$post->forum)->first()->title }}</span>
				</p>
			</a>
		@endforeach
	@endif
@stop

@section('script')
    @parent
@stop
