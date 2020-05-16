<!DOCTYPE html>
<html lang="zh-CN" manifest="/appcache.manifest" type="text/cache-manifest">
<head>
    @include('myApp.head')
</head>
<body id="bodyScroll">

    <div class="header">
		@include('myApp.header',['menu' => '稍候...'])
	</div>

	<div class="body" id="wrapper"></div>
    
	<div class="ajaxLoader collapse"></div>
	
	<div class="modal fade" tabindex="-1" role="dialog" id="shareDiv">
	  <div class="modal-dialog">
		<div class="modal-content">
		   <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">社交分享</h4>
		   </div>
		   <div class="modal-body">
			    <div class="container"></div>
		   </div>
		</div>
	  </div>
	</div>

	@include('myApp.footer')

	<script type="text/html" id="page_content">
	  <div class="container myContainer"></div>
	  <div class="bodyMask collapse"></div>
	</script>

	<script type="text/html" id="page_list">
	  <div id="scroller">
		<div id="pullDown">
		  <span class="pullDownIcon"></span>
		  <span class="pullDownLabel"></span>
		</div>
		<div class="container myContainer"></div>
		<div id="pullUp">
		  <span class="pullUpIcon"></span>
		  <span class="pullUpLabel"></span>
		</div>
	  </div>
	  <div class="bodyMask collapse"></div>
	</script>

	<script type="text/html" id="forum_menu">
		<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4">
			<div class="box_linker" data-template-bind='[{"attribute": "style", "value": "color", "formatter": "forumColor"}]'>
				<a class="boxHref" data-href="brief" data-format="forumLink" data-format-target="href">
					<h5 data-content="title"></h5>
				</a>
			</div>
		</div>
	</script>

	<script type="text/html" id="list_items">
		<a class="top-list" data-href="uid" data-format="postUrl" data-format-target="href">
			<h4 data-content="title"></h4>
			<p class="introduction word" data-content="content"></p>
			<p class="about">
				<span class="timestamp" data-content="post_time" data-format="timestamp"></span>
				<span data-content-append="forum_title">来自</span>
			</p>
		</a>
	</script>

	<script type="text/script" id="post_item">
		<div class="post" data-template-bind='{"attribute": "data-id", "value": "uid"}'>
			<h4 data-content-prepend="title"> <small data-content="replies" data-format="replies"></small></h4>
			<div class="post-about">
			    <span class="shareLinker" data-toggle="modal" data-target="#shareDiv">分享</span> 
				<span class="timestamp" data-content="post_time" data-format="timestamp"></span>
				<span class="author" data-content="username"></span>
				<a class="forum" data-href="forum" data-format="forumLink" data-format-target="href" data-content="forum_title"></a>
			</div>
			<div class="content word" data-content="content"></div>
			<div class="container">
				<div class="row my-attitude"></div>
			</div>
			<ul class="nav nav-pills">
				<li><a data-href="uid" data-format="replyLink" data-format-target="href">原文评论 <span class="badge" data-content="replies"></span></a></li>
			</ul>

			<div class="post_source"  data-template-bind='{"attribute": "data-forum", "value": "forum"}'>
			   <a target="_blank" data-href="original_url" data-content="forum_title" data-format="source_link"></a>
			</div>
			
		</div>
	</script>
	
	<script  type="text/html" id="shareTemplate">
	    <div class="ds-share" 
			data-images="http://top10.pub/images/webIcon-128.png"
			data-template-bind='[
				 {"attribute": "data-thread-key", "value": "uid"},
				 {"attribute": "data-title", "value": "title"},
				 {"attribute": "data-url", "value": "uid","formatter": "shareUrl"}
			 ]' >
			<div class="ds-share-inline">
				<ul  class="ds-share-icons-32 list-unstyled">
					<li><a class="ds-weibo" href="javascript:void(0);" data-service="weibo"></a></li>
					<li><a class="ds-qzone" href="javascript:void(0);" data-service="qzone"></a></li>
					<li><a class="ds-qq" href="javascript:void(0);" data-service="qq"></a></li>
					<li><a class="ds-wechat" href="javascript:void(0);" data-service="wechat"></a></li>
				</ul>
			</div>
	   </div>
	</script>

	<script  type="text/html" id="attitude">
	   <div data-class="class">
			<span class="num" data-content="number" data-template-bind='{"attribute" : "data-for","value":"attitude"}'></span>
			<span data-content="title"></span>
	   </div>
	</script>

	<script type="text/html" id="thread-with-reply">
		<div class="thread" data-template-bind='{"attribute": "data-id", "value": "uid"}'>
			<div class="thread-about">
				<a class="title" data-href="uid" data-format="postUrl" data-format-target="href">
					<h4 data-content="title"></h4>
				</a>
				<p class="author">
					<span class="author" data-content="username"></span>
					<span class="author" data-content-append="forum_title">@</span>
					<span class="timestamp" data-content="post_time" data-format="timestamp"></span>
				</p>
			</div>
			<div class="replies"></div>
		</div>
		<ul class="pagination hidden" data-content="pages" data-format="pages"></ul>
		<div class="post_source"  data-template-bind='{"attribute": "data-forum", "value": "forum"}'>
			<a target="_blank" data-href="original_url" data-content="forum_title" data-format="source_link"></a>
		</div>
		
	</script>

	<script type="text/html" id="reply-item">
		<div class="reply collapse" data-template-bind='{"attribute":"data-id","value":"uid"}'>
			<div class="reply-header" data-thumb="">
				<span class="floor collapse"></span>
				<span class="thumb support" data-for="support" data-content="support"></span>
				<span class="thumb oppose" data-for="oppose" data-content="oppose"></span>
			</div>
			<div class="content word" data-content="content" data-format="content"></div>
			<div class="reply-about">
				<span class="author" data-content="username"></span>
				<span class="timestamp" data-content="post_time" data-format="timestamp"></span>
			</div>
		</div>
	</script>
	
	<script type="text/html" id="html-page">
		<div class="page-header">
		  <h1 data-content="title"></h1>
		</div>
		<p data-content="content"></p>
    </script>
	
	<script src="{{ url() }}/bower/jquery/dist/jquery.min.js"></script>
	<script src="{{ url() }}/bower/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="{{ url() }}/bower/moment/min/moment.min.js"></script>
	<script src="{{ url() }}/bower/moment/locale/zh-cn.js"></script>
	<script src="{{ url() }}/bower/jshashes-master/hashes.min.js"></script>
	<script src="{{ url() }}/bower/jquery-template/dist/jquery.loadTemplate-noWrapper-1.5.7.min.js"></script>
	<script src="{{ url() }}/bower/jquery_lazyload/jquery.lazyload.min.js"></script>
	<script src="{{ url() }}/bower/infinite-scroll-master/jquery.infinitescroll.min.js"></script>
	<script src="{{ url() }}/bower/swipebox-master/src/js/jquery.swipebox.zoom.min.js"></script>
	<script src="{{ url() }}/bower/iscroll/iscroll-4.2.min.js"></script>
    
	<script src="{{ url() }}/js/myApp.js"></script>

	<script>
		$(function(){
			$.ajax({
				url: siteJson.get_url('logged'),
				dataType: 'html',
				success:   function(data) {
					if(data) $('.footer').html(data);
				},
				complete: function() {
					$('.footer').show();
				}
			})
		})
	</script>

</body>

</html>