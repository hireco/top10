<?php $menu = App\Models\Forum::where('brief', $post->forum)->first()->title;?>
@extends('home.layout',['menu' => $menu])

@section('page_title', $post->title)

@section('main')
    <?php
    $forum = App\Models\Forum::where('brief',$post->forum)->first();
    ?>
    <div class="thread" data-id="{{ $post->uid }}">
        {!!csrf_field() !!}
        <div class="thread-about">
            <a class="title" href="{{ url('admin') }}?type=post&uid={{ $post->uid }}">
                <h4 class="{{ $post->trashed()?'trashed':'' }}">
                    {{ $post->title }}
                    @if($post->trashed())
                        <small class="text-danger"> 删除人：{{ App\User::userName($post->deleted_by) }}</small>
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
             $floor = $perPage * ($currentPage - 1) + 1;
        ?>

        @if(!count($replies))
            <p class="alert alert-warning no-item">该贴暂无评论，请稍后再来。</p>
        @endif

        @foreach ($replies as $reply)
            <div class="reply" data-id="{{ $reply ->uid }}">
                <div class="reply-header" data-thumb="">
                    @if($reply->trashed())
                        <span class="text-danger trashed"> 删除人：{{ App\User::userName($reply->deleted_by) }}</span>
                    @endif
                    <span>{{ $floor }}楼</span>
                    @if(!$post->trashed())
                        <span class="thumb support" data-for="support">{{ $reply->support }}</span>
                        <span class="thumb oppose" data-for="oppose">{{ $reply->oppose }}</span>
                    @endif
                </div>
                <div class="content word invisible {{ $reply->trashed()?'trashed':'' }}">{{ $reply -> content }}</div>
                <div class="reply-about">
                    <span class="timestamp collapse">{{ $reply->post_time }}</span>
                    <span class="author"> {{ $reply->username }}</span>
                </div>
            </div>
            <?php $floor++; ?>
        @endforeach

        {!! $replies->appends(['type' => 'reply','uid' => $post->uid])->render() !!}

        <input type="hidden" id="currentPage" value="{{ $currentPage }}">

        <div class="post_source"  data-forum="{{ $post->forum }}">
            <a target="_blank" href="{{ $post->original_url }}">查看来自{{ $forum->affiliated.$forum->title }}的原文</a>
        </div>

    </div>

    <script>
        $(function(){
            $('.trashed').wrap('<del></del>');
        })
    </script>

@stop
