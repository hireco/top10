@extends('home.layout',['menu' => '中心'])

@section('page_title', '用户首页')

@section('main')
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#me" role="tab" data-toggle="tab">我</a></li>
        <li role="presentation"><a href="#forum" role="tab" data-toggle="tab">帖子</a></li>
    </ul>

    <div class="tab-content">

        <?php $user = Auth::user(); ?>

        <div role="tabpanel" class="tab-pane active" id="me">
            <div class="userInfo">
                <ul class="list-group inforDetails">
                    <li class="list-group-item"><span class="item">用户名：</span><span>{{ $user->name }}</span></li>
                    <li class="list-group-item"><span class="item">电子邮件：</span><span>{{ $user->email }}</span></li>
                    <li class="list-group-item"><span class="item">用户级别：</span><span>{{ \App\Libs\MyCheck::roleTitle($user->right) }}</span></li>
                    <li class="list-group-item"><span class="item">注册时间：</span><span class=" timestamp collapse">{{ $user->created_at }}</span></li>
                    <li class="list-group-item"><span class="item">最新登录：</span><span class="timestamp collapse">{{ $user->updated_at }}</span></li>
                </ul>
                <div class="collapse whatToDo">
                    <div class="userInfo">
                        <p class="alert alert-warning">后悔吧？作为最初用户，您的权限目前如下</p>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <a href="{{ url('home?type=password') }}" class="btn btn-primary btn-block">你可以改密</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <form action="{{ url('home?type=suicide') }}" method="POST" accept-charset="UTF-8">
                                        {!! csrf_field() !!}
                                        <button href="{{ url('home?type=suicide') }}" class="btn btn-danger btn-block">此外还可以自杀</button>
                                    </form>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <a href="{{ url('home?type=posts') }}" class="btn btn-info btn-block">或者乖乖看帖，去吧</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <p class="form-control-static">什么？ 想当管理员？ 邮件联系我吧 </p>
                            <p class="form-control-static"><img src="http://top10.pub/images/email-address.gif" /></p>
                            <p class="form-control-static">别乱访问，否则是<a href="{{ url('shaDongXiYa?~-^鄙视你') }}">这个结果</a></p>
                        </div>
                    </div>
                </div>
                <button class="btn btn-info" id="whatToDo">干点什么？</button>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="forum">
            <div class="forum-list">
                <a class="btn btn-primary" href="{{ url('home').'?type=posts' }}">全部帖子</a>
                @foreach($forums as $index => $forum)
                    <a class="btn btn-success" href="{{ url('home') }}?type=posts&forum={{ $forum->brief }}" style="border-color:{{ $forum->color }}; background-color:{{ $forum->color }}">
                        <?php echo $forum->title;?>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

@stop

@section('fixedSection')
    @parent
@stop

@section('script')
    @parent
    <script>
        $(function(){
            $('#whatToDo').click(function(){
                $('.whatToDo,.inforDetails').toggle();
            })
        })
    </script>
@stop