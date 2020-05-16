@extends('home.layout',['menu' => '修改密码'])

@section('page_title', '用户')

@section('main')

    <div class="page-header"><h3>注意保护好您的密码</h3></div>

    <form id="password-form" action="{{ url('home?type=password') }}" method="post" class="form-horizontal" accept-charset="UTF-8">
        {!! csrf_field() !!}

        <div class="form-group">
            <label  class="control-label col-lg-2 visible-lg" for="password">旧密码</label>
            <div class="input-block col-lg-6">
                <input type="password" name="old_password" class="form-control" placeholder="输入旧密码" required>
            </div>
        </div>

        <div class="form-group">
            <label  class="control-label col-lg-2 visible-lg"  for="password">新密码</label>
            <div class="input-block col-lg-6">
                <input type="password" name="password" class="form-control" placeholder="输入新密码" required>
            </div>
        </div>

        <div class="form-group">
            <label  class="control-label col-lg-2 visible-lg" for="password_confirmation"> 确认新密码</label>
            <div class="input-block col-lg-6">
                <input type="password" name="password_confirmation" class="form-control" placeholder="确认新密码" required>
            </div>
        </div>

        <div class="form-group">
            <div class="input-block col-lg-6 col-lg-offset-2">
                <button id="password_submit" type="submit" class="btn btn-primary btn-block">提交更改</button>
            </div>
        </div>

        @if($errors->any())
            <div class="form-group">
                <div class="col-lg-6 col-lg-offset-2">
                    <ul class="alert alert-danger list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ str_replace(' ','',$error) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

   </form>

@stop

@section('fixedSection')
    @parent
@stop

@section('script')
    @parent
@stop