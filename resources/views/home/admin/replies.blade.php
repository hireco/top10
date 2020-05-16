@extends('home.layout',['menu' => '回帖管理'])

@section('page_title', '管理')

@section('main')

	<?php $forum = App\Models\Forum::where('brief',$post->forum)->first();?>

		<div class="thread" data-id="{{ $post->uid }}">
			<div class="thread-about">
				<a class="title" href="{{ url() }}/post/{{ $post->uid }}">
					<h4 class="{{ $post->trashed()?'trashed':'' }}">
						{{ $post->title }}
						@if($post->trashed())
							<small class="text-danger trashed"> 删除人：{{ App\User::userName($post->deleted_by) }}</small>
						@endif
					</h4>
				</a>
				<p class="author">
					<span class="author">{{ $post->username.'@' }}{{ $forum->title }}</span>
					<span class="timestamp collapse">{{ $post->post_time }}</span>
				</p>
			</div>

			<?php
			$perPage = $replies ->perPage();
			$currentPage = $replies ->currentPage();
			?>

			@if(!count($replies))

				<p class="alert alert-warning no-item">该贴暂无评论，请稍后再来。</p>

			@else
				<form id="admin_form" method="POST" action="{{ url('admin') }}?action=remove&type=replies" accept-charset="UTF-8">

					{!!csrf_field() !!}
                    <input type="hidden" value="{{ $post->uid }}"  name="post_id" />

					@foreach ($replies as $reply)
						<div class="reply items-list" id="reply_{{ $reply ->uid }}">
							<div class="reply-header">
							@if($reply->trashed())
								<span class="text-danger trashed"> 删除人：{{ App\User::userName($reply->deleted_by) }}</span>
							@endif
							</div>
							<div class="content word invisible {{ $reply->trashed()?'trashed':'' }}">{{ $reply -> content }}</div>
							<div class="reply-about">
								<span class="timestamp collapse">{{ $reply->post_time }}</span>
								<span class="author"> {{ $reply->username }}</span>
							</div>
							<p class="admin">
								<label title="选择" class="select_this glyphicon glyphicon-check" for="{{ $reply->uid }}"></label>
								<input type="checkbox"   class="hidden" name="reply_id[]" id="{{ $reply->uid }}" value="{{ $reply->uid }}"/>
								<label title="{{ $reply->trashed()?'彻底删除':'加入回收站' }}" class="remove_this glyphicon glyphicon-remove-sign"></label>
							</p>
						</div>
					@endforeach

					<div class="text-center pages">
						{!! $replies->appends(['type' => 'replies','uid' => $post->uid])->render() !!}
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
						$('.submit,.remove_this').click(function(){

							$('.btn-list').hide();

							if($(this).is('label')) {
								$('.reply :checkbox').removeAttr('checked');
								$(this).siblings(':checkbox').prop('checked','checked');
							}

							setTimeout(function(){
								$('#admin_form').ajaxSubmit({
									dataType:'json',
									beforeSubmit: function() {
										$('.ajaxLoader').show();
									},
									success: function(responseText) {
										if(responseText.status == 'success') {
											$.each(responseText.items,function(index,item){
												$('#reply_' + item).remove();
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

			<div class="post_source"  data-forum="{{ $post->forum }}">
				<a target="_blank" href="{{ $post->original_url }}">查看来自{{ $forum->affiliated.$forum->title }}的原文</a>
			</div>

		</div>

@stop

@section('fixedSection')
    @parent
	<div class="btn-list collapse">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><button class="submit btn btn-danger btn-block">{{ $post->trashed()?'彻底删除':'加入回收站' }}</button></div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><button type="button" class="cancel btn btn-default btn-block">取消</button></div>
			</div>
		</div>
    </div>
@stop

@section('script')
    @parent
@stop