@extends('auth.layout',['menu' => '重设密码'])

@section('main')

    <div class="page-header"><h3>填写表单，设置新密码</h3></div>

    @if($errors->any())
        <ul class="alert alert-danger list-unstyled">
            @foreach($errors->all() as $error)
                <li>{{ str_replace(' ','',$error) }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ url('password/reset') }}" method="post" class="form-horizontal">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="email" class="hidden-xs col-lg-2 text-right">电子邮件</label>
            <div class="col-lg-6">
                <input type="email" id="email" name="email" value="" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label for="passowrd" class="hidden-xs col-lg-2 text-right">新密码</label>
            <div class="col-lg-6">
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="hidden-xs col-lg-2 text-right">确认密码</label>
            <div class="col-lg-6">
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
               <button type="submit" class="btn btn-primary btn-block">点击提交</button>
            </div>
        </div>
    </form>

@stop