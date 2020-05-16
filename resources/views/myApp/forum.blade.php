<?php
  $forum = App\Models\Forum::where('brief',$forum)->first();
  $menu = $forum -> title;
?>

@if(!count($posts))
	<p class="alert alert-warning no-item">该站暂无热帖，请稍后再来。</p>
@else
<div class="top-list">
	@foreach ($posts as $post)
		<a href="{{ url('myApp#post/'.$post->uid) }}">
			<h4>{{ $post->title }}</h4>
			<p class="introduction word">{{ $post->content }}</p>
			<p class="about">
				<span class="timestamp collapse">{{ $post->post_time }}</span>
				<span>来自{{ $menu }}</span>
			</p>
		</a>
	@endforeach
</div>
@endif

<script>
$(function(){
  $('.curMenu').text('{{ $menu }}');
  showTime();
});
</script>
	
	