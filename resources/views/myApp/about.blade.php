<div class="page-header">
	<h1>{{ $about->title }}</h1>
</div>
<p>{!! $about->content !!}</p>

<script>
$(function(){
  $('.curMenu').text('{{ $about->title }}');
});
</script>