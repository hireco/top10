@if(!count($posts))
        <p class="alert alert-warning no-item">今日十大尚未获取，请稍候。</p>
    @endif

    <div class="top-list">
        @foreach ($posts as $post)
            <a href="{{ url('myApp#post/'.$post->uid) }}">
                <h4>{{ $post->title }}</h4>
                <p class="introduction word">{{ $post->content }}</p>
                <p class="about">
                    <span class="timestamp collapse">{{ $post->post_time }}</span>
                    <span>来自{{ $post->forum_title }}</span>
                </p>
            </a>
        @endforeach
    </div>
<script>
$(function(){
	$('.curMenu').text('全站');
	showTime();
});
</script>