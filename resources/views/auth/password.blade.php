@extends('auth.layout',['menu' => '重设密码'])

@section('main')

    <div class="page-header"><h3>发送邮件到您的注册邮箱</h3></div>

    @if($errors->any())
        <ul class="alert alert-danger list-unstyled">
            @foreach($errors->all() as $error)
                <li>{{ str_replace(' ','',$error) }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ url('password/email') }}" method="post" class="form-horizontal">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="email" class="control-label  hidden-xs col-lg-2 text-right">电子邮件</label>
            <div class="col-lg-6">
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="请填写您注册时填写的邮箱">
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
               <button type="submit" class="btn btn-primary btn-block">发送</button>
            </div>
        </div>
    </form>
@stop