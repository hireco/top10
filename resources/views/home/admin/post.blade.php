<?php $menu = App\Models\Forum::where('brief', $post->forum)->first()->title;?>
@extends('home.layout',['menu' => $menu])

@section('page_title', $post->title)

@section('main')
    <?php
       $post->emotion = json_decode($post->emotion);
       $forum = App\Models\Forum::where('brief',$post->forum)->first();
    ?>
    <div class="post" data-id="{{ $post->uid }}">
        {!!csrf_field() !!}
        <h4 class="{{ $post->trashed()?'trashed':'' }}">
            {{ $post->title }}
            <small>{{ $post->replies }}条评论</small>
            @if($post->trashed())
               <small class="text-danger"> 删除人：{{ App\User::userName($post->deleted_by) }}</small>
            @endif
        </h4>
        <div class="post-about">
            <span class="timestamp collapse">{{ $post->post_time }}</span>
            <span class="author">{{ $post->username }}</span>
            <a class="forum" href="{{ url('admin') }}?type=posts&forum={{ $post->forum }}">{{ $forum->title }}</a>
        </div>
        <div class="content word invisible {{ $post->trashed()?'trashed':'' }}">
            {{ $post->content }}
        </div>
        @if(!$post->trashed())
            <div class="container">
                <div class="row my-attitude" data-attitude="">
                    @foreach($emotions as $index => $value)
                        <div class="attitude {{ $index }} col-xs-4">
                            <span class="num" data-for="{{ $index }}">{{ $post->emotion->$index }}</span>
                            <span>{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <ul class="nav nav-pills">
            <li><a href="{{ url('admin') }}?type=reply&uid={{ $post->uid }}">原文评论 <span class="badge">{{ $post->replies }}</span></a></li>
        </ul>

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
