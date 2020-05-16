@extends('auth.layout',['menu' => '注册'])

@section('main')

    <div class="page-header collapse"><h3>您真要注册？咱可都是有故事的人呢</h3></div>

    <div class="form-horizontal helloDiv collapse">
        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
                <span>真要，那就点击开始吧！</span> <button id="beginIt" class="btn btn-info">点击这里 </button>
            </div>
        </div>
    </div>

    <form class="form-horizontal collapse" id="regForm" role="form" method="POST" action="{{ url('auth/register') }}">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="name" class="control-label hidden-xs col-lg-2 text-right">用户名</label>
            <div class="col-lg-6">
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="输入用户名">
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="control-label hidden-xs col-lg-2 text-right">邮箱地址</label>
            <div class="col-lg-6">
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="输入邮箱地址">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="control-label  hidden-xs col-lg-2 text-right">密码</label>
            <div class="col-lg-6">
                <input class="form-control" type="password" name="password" placeholder="输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="password_cfm" class="control-label hidden-xs col-lg-2 text-right">确认密码</label>
            <div class="col-lg-6">
                <input class="form-control" type="password" name="password_confirmation" placeholder="确认密码">
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-6 col-lg-offset-2">
                <button class="btn btn-block btn-primary subRegister" type="submit">提交注册</button>
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

    <script>
        $(function(){
            if(document.referrer != window.location.href) {
                $('.page-header').show();
                $('.helloDiv').show();
                $('#beginIt').click(function(){
                    $('.helloDiv').fadeOut('fast',function(){
                        $('.page-header h3').text('欢迎注册');
                        $('#regForm').show();
                    })
                })
            }
            else {
                $('.page-header h3').text('欢迎注册');
                $('.page-header').show();
                $('#regForm').show();
            }

        })
    </script>

@stop