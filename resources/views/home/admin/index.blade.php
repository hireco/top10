@extends('home.layout',['menu' => '中心'])

@section('page_title', '用户首页')

@section('main')
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#me" role="tab" data-toggle="tab">我</a></li>
        <li role="presentation"><a href="#forum" role="tab" data-toggle="tab">帖子</a></li>
        @if(\App\Libs\MyCheck::IhaveRight('super'))
            <li><a href="{{ url('admin?type=users') }}">用户</a></li>
        @endif
        <li role="presentation"><a href="#trash" role="tab" data-toggle="tab">回收站</a></li>
    </ul>

    <div class="tab-content">

        <?php $user = Auth::user(); ?>

        <div role="tabpanel" class="tab-pane active" id="me">
            <div class="userInfo">
                <ul class="list-group">
                    <li class="list-group-item"><span class="item">用户名：</span><span>{{ $user->name }}</span></li>
                    <li class="list-group-item"><span class="item">电子邮件：</span><span>{{ $user->email }}</span></li>
                    <li class="list-group-item"><span class="item">用户级别：</span><span>{{ \App\Libs\MyCheck::roleTitle($user->right) }}</span></li>
                    <li class="list-group-item"><span class="item">注册时间：</span><span class=" timestamp collapse">{{ $user->created_at }}</span></li>
                    <li class="list-group-item"><span class="item">最新登录：</span><span class="timestamp collapse">{{ $user->updated_at }}</span></li>
                </ul>
                <a href="{{ url('home?type=password') }}" class="btn btn-danger">修改密码</a>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="forum">
            <div class="forum-list">
                <a class="btn btn-primary" href="{{ url('admin').'?type=posts' }}">全部帖子</a>
                @foreach($forums as $index => $forum)
                    <a class="btn btn-success" href="{{ url('admin') }}?type=posts&forum={{ $forum->brief }}" style="border-color:{{ $forum->color }}; background-color:{{ $forum->color }}">
                        <?php echo $forum->title;?>
                    </a>
                @endforeach
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="trash">
            <div class="forum-list">
                <a class="btn btn-default" href="{{ url('admin').'?type=posts&trash=only' }}">全部帖子</a>
                @foreach($forums as $index => $forum)
                    <a class="btn btn-default" href="{{ url('admin') }}?type=posts&trash=only&forum={{ $forum->brief }}">
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
@stop