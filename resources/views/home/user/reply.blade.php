@extends('home.layout',['menu' => '回帖管理'])

@section('page_title', '管理')

@section('main')
	<?php
	$forum = App\Models\Forum::where('brief',$post->forum)->first();
	?>
	<div class="thread" data-id="{{ $post->uid }}">
		{!!csrf_field() !!}
		<div class="thread-about">
			<a class="title" href="{{ url('home') }}?type=post&uid={{ $post->uid }}">
				<h4>{{ $post->title }}</h4>
			</a>
			<p class="author">
				<span class="author">{{ $post->username.'@' }}{{ $forum->title }}</span>
				<span class="timestamp collapse">{{ $post->post_time }}</span>
			</p>
		</div>

		<?php
		$perPage = $replies ->perPage();
		$currentPage = $replies ->currentPage();
		$floor = $perPage * ($currentPage - 1) + 1;
		?>

		@if(!count($replies))
			<p class="alert alert-warning no-item">该贴暂无评论，请稍后再来。</p>
		@endif

		@foreach ($replies as $reply)
			<div class="reply" data-id="{{ $reply ->uid }}">
				<div class="reply-header" data-thumb="">
					<span>{{ $floor }}楼</span>
					<span class="thumb support" data-for="support">{{ $reply->support }}</span>
					<span class="thumb oppose" data-for="oppose">{{ $reply->oppose }}</span>
				</div>
				<div class="content word invisible">{{ $reply -> content }}</div>
				<div class="reply-about">
					<span class="timestamp collapse">{{ $reply->post_time }}</span>
					<span class="author"> {{ $reply->username }}</span>
				</div>
			</div>
			<?php $floor++; ?>
		@endforeach

		{!! $replies->appends(['type' => 'reply','uid' => $post->uid])->render() !!}

		<input type="hidden" id="currentPage" value="{{ $currentPage }}">

		<div class="post_source"  data-forum="{{ $post->forum }}">
			<a target="_blank" href="{{ $post->original_url }}">查看来自{{ $forum->affiliated.$forum->title }}的原文</a>
		</div>

		@include('duoshuo',['thread_id' => $post->uid, 'thread_title' => $post->title, 'thread_url' => url('post/'.$post->uid)])

	</div>

@stop

@section('fixedSection')
    @parent
@stop

@section('script')
    @parent
@stop