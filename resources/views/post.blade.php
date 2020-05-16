<?php $menu = App\Models\Forum::where('brief', $post->forum)->first()->title;?>
@extends('layout',['menu' => $menu])

@section('page_title', $post->title)

@section('main')
    <?php
       $post->emotion = json_decode($post->emotion);
       $forum = App\Models\Forum::where('brief',$post->forum)->first();
    ?>
    <div class="post" data-id="{{ $post->uid }}">
        {!!csrf_field() !!}
        <h4>{{ $post->title }} <small>{{ $post->replies }}条评论</small></h4>
        <div class="post-about">
            <span class="timestamp collapse">{{ $post->post_time }}</span>
            <span class="author">{{ $post->username }}</span>
            <a class="forum" href="{{ url() }}/forum/{{ $post->forum }}">{{ $forum->title }}</a>
        </div>
        <div class="content word invisible">
            {{ $post->content }}
        </div>
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

        <ul class="nav nav-pills">
            <li><a href="{{ url('reply/'.$post->uid) }}">原文评论 <span class="badge">{{ $post->replies }}</span></a></li>
        </ul>

        <div class="post_source"  data-forum="{{ $post->forum }}">
            <a target="_blank" href="{{ $post->original_url }}">查看来自{{ $forum->affiliated.$forum->title }}的原文</a>
        </div>
		
		@include('duoshuo',['thread_id' => $post->uid, 'thread_title' => $post->title, 'thread_url' => url('post/'.$post->uid)])

    </div>
@stop
