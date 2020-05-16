@extends('home.layout',['menu' => '用户管理'])

@section('page_title', '管理')

@section('main')
	@if(!count($users))
		<p class="alert alert-warning no-item">目前没有用户</p>
	@else
		<form id="admin_form" method="POST" action="" accept-charset="UTF-8">
			{!! csrf_field() !!}

			@foreach($users as $user)
				<?php $uid = Vinkla\Hashids\Facades\Hashids::encode($user->id); ?>
				<div class="user-list" id="user_{{ $uid }}">
					@if(\App\Libs\MyCheck::IhaveRight('super'))
						<div class="admin">
							<label class="select_this glyphicon glyphicon-check" for="{{ $uid }}"></label>
							<input type="checkbox"  class="hidden" name="user_id[]" id="{{ $uid }}" value="{{ $uid }}"/>
							<label data-action="{{ url('admin') }}?action=remove&type=users" class="remove_this glyphicon glyphicon-remove-sign"></label>
							<label data-toggle="modal" data-target=".roleSelect" title="{{ \App\Libs\MyCheck::roleTitle($user->right) }}" style="color:{{ \App\Libs\MyCheck::roleColor($user->right) }}"  class="admin_this glyphicon glyphicon-user"></label>
						</div>
					@endif
					<div>{{ $user->name }}</div>
					<div class="hidden-xs">{{ $user->email }}</div>
					<div class="timestamp collapse">{{ $user->created_at }}</div>
					<div class="clearfix"></div>
				</div>
			@endforeach

			<div class="modal fade roleSelect bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">为用户选择角色</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="role_user"><span class="glyphicon glyphicon-check"></span> <span>普通用户</span></label>
								<input type="radio"  class="hidden" name="right" value="0" id="role_user" />
							</div>
							<div class="form-group">
								<label for="role_admin"><span class="glyphicon glyphicon-check"></span> <span>管理员</span></label>
								<input type="radio"  class="hidden" name="right" value="1" id="role_admin" />
							</div>

							<div class="form-group">
								<button class="submit btn btn-primary" id="roleSetting" data-action="{{ url('admin') }}?action=admin&type=users">设 置</button>
								<button class="btn btn-default" data-dismiss="modal">取 消</button>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="text-center pages">
				{!! $users->appends(['type' => 'users'])->render() !!}
			</div>

		</form>

	@endif

@stop

@section('fixedSection')
    @parent
	@if(\App\Libs\MyCheck::IhaveRight('super'))
		<div class="btn-list collapse">
			<div class="container">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><button data-action="{{ url('admin') }}?action=remove&type=users" class="submit btn btn-danger btn-block">删除</button></div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><button type="button" class="cancel btn btn-default btn-block">取消</button></div>
				</div>
			</div>
		</div>
	@endif
@stop

@section('script')
    @parent
	@if(\App\Libs\MyCheck::IhaveRight('super'))
		<link href="{{ url() }}/bower/sweetalert/dist/sweetalert.css" rel="stylesheet">
		<script src="{{ url() }}/bower/sweetalert/dist/sweetalert.min.js"></script>
		<link href="{{ url() }}/bower/toastr/toastr.min.css" rel="stylesheet">
		<script src="{{ url() }}/bower/toastr/toastr.min.js"></script>
		<script>
			$(function(){

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

				$('.modal-body label').click(function(){
					$('.modal-body label .glyphicon').removeClass('selected');
					$(this).children('.glyphicon').addClass('selected');
				})

				$('.remove_this,.admin_this').click(function(){
					$('.user-list .admin :checkbox').removeAttr('checked');
					$(this).siblings('.admin :checkbox').prop('checked','checked');
				})


				$('.submit,.remove_this').click(function(event){

					if($(this).is('#roleSetting')) {
						if(!$(':radio:checked').length) {
							toastr.options={"positionClass": "toast-top-full-width"};
							toastr['error']('您没有选择角色类型', '表单错误');
							return false;
						}
					}
					$('.btn-list').hide();

					$('#admin_form').attr('action',$(this).data('action'));

					if($(this).data('action').indexOf('remove') > 0) {
						swal({
									title: "确定删除选定用户吗？？",
									text: "注意：删除后，该用户的所有信息都会被删除!",
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "继续该操作！",
									closeOnConfirm: true
								},
								userSetting

						);
					}
					else
						userSetting();

					event.preventDefault();
				})

				function  userSetting() {
					setTimeout(function(){
						$('#admin_form').ajaxSubmit({
							dataType:'json',
							beforeSubmit: function() {
								$('.ajaxLoader').show();
							},
							success: function(responseText) {
								if(responseText.status == 'success') {
									$.each(responseText.items,function(index,item){
										if(responseText.action == 'remove')
											$('#user_' + item).remove();
										else  {
											$('#user_' + item + ' .admin_this').css('color',responseText.color);
											$('#user_' + item + ' .admin_this').attr('title',$('label[for="role_'+responseText.role+'"]').text())
											$('#user_' + item + ' .selected').removeClass('selected');
											$('#user_' + item + ' :checkbox').removeAttr('checked');
										}
									});
								}
							},
							complete:function() {
								$('.ajaxLoader').hide();
								$('.roleSelect').modal('hide');
								if(!$('.user-list').length)  $('#admin_form').html('<p class="alert alert-warning no-item">目前没有帖子</p>');
							}
						});
					},250);
				}

				$('.cancel').click(function(){
					$('.select_this.selected').click();
				});
			})
		</script>
    @endif
@stop