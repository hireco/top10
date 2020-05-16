<div class="ds-share" data-thread-key="{{ $thread_id }}" data-title="{{ $thread_title }}" data-url="{{ $thread_url }}" data-images="{{ url() }}/images/webIcon-128.png">
	<div class="ds-share-inline">
		<ul  class="ds-share-icons-16 list-unstyled">
			<li><a class="ds-weibo" href="javascript:void(0);" data-service="weibo">微博</a></li>
			<li><a class="ds-qzone" href="javascript:void(0);" data-service="qzone">QQ空间</a></li>
			<li><a class="ds-qq" href="javascript:void(0);" data-service="qq">QQ好友</a></li>
			<li><a class="ds-wechat" href="javascript:void(0);" data-service="wechat">微信</a></li>
		</ul>
	</div>
</div>
<!-- 多说评论框 start -->
<div class="ds-thread" data-thread-key="{{ $thread_id }}" data-title="{{ $thread_title }}" data-url="{{ $thread_url }}"></div>
<!-- 多说评论框 end -->
<!-- 多说公共JS代码 start (一个网页只需插入一次) -->
<script type="text/javascript">
var duoshuoQuery = {short_name:"top10pub"};
	(function() {
		var ds = document.createElement('script');
		ds.type = 'text/javascript';ds.async = true;
		ds.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//static.duoshuo.com/embed.js';
		ds.charset = 'UTF-8';
		(document.getElementsByTagName('head')[0]
		 || document.getElementsByTagName('body')[0]).appendChild(ds);
	})();
</script>
<!-- 多说公共JS代码 end -->

