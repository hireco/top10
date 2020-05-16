@extends('auth.layout',['menu' => '登录'])

@section('main')

    <div class="page-header"><h3>欢迎登录</h3></div>

    <form class="form-horizontal" role="form" method="POST" action="{{ url('auth/login') }}">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="email" class="control-label hidden-xs col-lg-2 text-right">邮箱账号</label>

            <div class="col-lg-6">
                <input class="form-control" type="text" name="email" value="{{ old('email') }}" placeholder="输入邮件地址">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="control-label hidden-xs col-lg-2 text-right">密码</label>

            <div class="col-lg-6">
                <input class="form-control" type="password" name="password" id="password" placeholder="输入密码" value="" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
                <p class="form-control-static"><a href="{{ url('password/email') }}">忘记密码？</a></p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
                <button class="btn btn-block btn-primary" type="submit">点击登录</button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
                <input  type="checkbox" name="remember" checked="checked"> 记住我
            </div>
        </div>

    </form>

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

    @if (session('status'))
        <div class="alert alert-warning collapse suicideInfo">
            {{ session('status') }}
        </div>
    @endif

    <script>
        $(function(){
            if($('.suicideInfo').length) {
                toastr.options={"positionClass": "toast-top-full-width"};
                toastr['warning']($('.suicideInfo').text(), '特大喜讯');
            }
        })
    </script>

@stop

@section('script')
    @parent
    <link href="{{ url() }}/bower/toastr/toastr.min.css" rel="stylesheet">
    <script src="{{ url() }}/bower/toastr/toastr.min.js"></script>
@stop