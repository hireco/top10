@extends('home.layout',['menu' => $menu])

@section('page_title', '管理')

@section('main')
	@if(!count($posts))
		<p class="alert alert-warning no-item">目前没有帖子</p>
	@else
		<form id="admin_form" method="POST" action="" accept-charset="UTF-8">
			{!! csrf_field() !!}
			@foreach ($posts as $post)
				<?php
				$topped = $post->top_time > $post->get_time ? 'topped':'';
				?>
				<div class="top-list items-list" id="post_{{ $post->uid }}">

					<h4>
						<a class="{{ $post->trashed()?'trashed':'' }}" href="{{ url('admin') }}?type=post&uid={{ $post->uid }}">{{ $post->title }}</a>
						@if($post->trashed())
							<small class="text-danger trashed"> 删除人：{{ App\User::userName($post->deleted_by) }}</small>
						@endif
					</h4>
					<p class="introduction word {{ $post->trashed()?'trashed':'' }}">{{ mb_substr($post->content,0,100) }}</p>
					<p class="about">
						<span class="timestamp collapse">{{ $post->post_time }}</span>
						<span>来自{{ App\Models\Forum::where('brief',$post->forum)->first()->title }}</span>
					</p>
					<p class="admin">
						<label  title="选择"  class="select_this glyphicon glyphicon-check" for="{{ $post->uid }}"></label>
						<input type="checkbox"   class="hidden" name="post_id[]" id="{{ $post->uid }}" value="{{ $post->uid }}"/>
						<label title="{{ $post->trashed()?'彻底删除':'加入回收站' }}" data-action="{{ url('admin') }}?action=remove&type=posts" class="remove_this glyphicon glyphicon-remove-sign"></label>
						@if(!$trashed)
						     <label title="设置置顶"  data-action="{{ url('admin') }}?action={{ $topped?'down':'top' }}&type=posts" class="top_this glyphicon glyphicon-thumbs-up {{ $topped }}"></label>
						@else
							<label title="回收"  data-action="{{ url('admin') }}?action=restore&type=posts" class="restore_this glyphicon glyphicon-repeat"></label>
						@endif
						<a href="{{ url('admin') }}?type=replies&uid={{ $post->uid }}"><label class="badge">{{ $post->replies }}</label></a>
					</p>
				</div>
			@endforeach

			<div class="text-center pages">
				{!! $posts->appends(['type' => 'posts'])->render() !!}
			</div>

		</form>

		<script>
			$(function(){

				$('.trashed').wrap('<del></del>');

				$('.select_this').click(function(){
					$(this).toggleClass('selected');
					if($('.select_this.selected').length) {
						$('.pages').hide();
						$('.btn-list').show();
					}

					else {
						$('.btn-list').hide();
						$('.pages').show();
					}

				})
				$('.submit,.top_this,.remove_this,.restore_this').click(function(){

					$('.btn-list').hide();

					if($(this).is('label')) {
						$('.top_list :checkbox').removeAttr('checked');
						$(this).siblings(':checkbox').prop('checked','checked');
					}

					$('#admin_form').attr('action',$(this).data('action'));

					setTimeout(function(){
						$('#admin_form').ajaxSubmit({
							dataType:'json',
							beforeSubmit: function() {
								$('.ajaxLoader').show();
							},
							success: function(responseText) {
								if(responseText.status == 'success') {
									$.each(responseText.items,function(index,item){
										if(responseText.action == 'remove' || responseText.action == 'restore')
											$('#post_' + item).remove();
										else if(responseText.action == 'top') {
											$('#post_' + item + ' .top_this').data('action', $('#post_' + item + ' .top_this').data('action').replace('action=top','action=down'));
											$('#post_' + item + ' .top_this').addClass('topped');
											$('#post_' + item + ' .selected').removeClass('selected');
											$('#post_' + item + ' :checkbox').removeAttr('checked');
										}
										else {
											$('#post_' + item + ' .top_this').data('action', $('#post_' + item + ' .top_this').data('action').replace('action=down','action=top'));
											$('#post_' + item + ' .top_this').removeClass('topped');
											$('#post_' + item + ' .selected').removeClass('selected');
											$('#post_' + item + ' :checkbox').removeAttr('checked');
										}
									});
								}
							},
							complete:function() {
								$('.ajaxLoader').hide();
								if(!$('.items-list').length)  $('#admin_form').html('<p class="alert alert-warning no-item">目前没有帖子</p>');
							}
						});
					},250);
					event.preventDefault();
				})

				$('.cancel').click(function(){
					$('.select_this.selected').click();
				});
			})
		</script>

	@endif

@stop

@section('fixedSection')
    @parent
	<div class="btn-list collapse">
		<div class="container">
			<div class="row">
				@if(!$trashed)
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><button data-action="{{ url('admin') }}?action=top&type=posts" class="submit btn btn-primary btn-block">推荐</button></div>
				@else
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><button data-action="{{ url('admin') }}?action=restore&type=posts" class="submit btn btn-primary btn-block">回收</button></div>
				@endif
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><button data-action="{{ url('admin') }}?action=remove&type=posts" class="submit btn btn-danger btn-block">{{ $trashed?'彻底删除':'加入回收站' }}</button></div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"><button type="button" class="cancel btn btn-default btn-block">取消</button></div>
			</div>
		</div>
    </div>
@stop

@section('script')
    @parent
@stop