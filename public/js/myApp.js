//all functions for localstorage
var myLocalStorage = {

	version : '20160601503X2',

	//ios's browser's private browsing mode will make localstorage failed
	isSupported : function () {
		var testKey = 'test', storage = window.localStorage;
		try {
			storage.setItem(testKey, '1');
			storage.removeItem(testKey);
			return true;
		}
		catch (error) {
			return false;
		}
	},
	myJsonGet: function(jsonObj,item){
		var jsonVal = localStorage.getItem(jsonObj);
		    jsonVal = JSON.parse(jsonVal);
		try {
			return eval('jsonVal.'+item);
		}catch(err) {
			return null;
		}
	},
	myJsonSet: function(jsonObj,item,value){
		var jsonVal = localStorage.getItem(jsonObj);
		    jsonVal = JSON.parse(jsonVal);
		if(!jsonVal)
			eval('jsonVal = {'+ item +': value}');
		else
			eval('jsonVal.' + item +' = value');

		localStorage.setItem(jsonObj,JSON.stringify(jsonVal));
	},
	myJsonRemove: function(jsonObj,item) {
		var jsonVal = localStorage.getItem(jsonObj);
		    jsonVal = JSON.parse(jsonVal);
		if(!jsonVal)
			return false;
		else
			eval('delete jsonVal.' + item);

		localStorage.setItem(jsonObj,JSON.stringify(jsonVal));
	},
    refresh: function() {
		var cacheTime = myLocalStorage.myJsonGet('cache','time');
		var cacheVersion = myLocalStorage.myJsonGet('cache','version');
		var curTime = moment().format('X');

		if(cacheTime - curTime > 30*24*3600 || cacheVersion!= this.version) {

			localStorage.clear();
			sessionStorage.clear();

			myDBClass.clearDB();
			myDBClass.initialDB();

			myLocalStorage.myJsonSet('cache','time',curTime);
			myLocalStorage.myJsonSet('cache','version',this.version);

		}
	}
}

//doushuo
var duoshuoQuery = {short_name:"top10pub"};

var myDuoshuo = {

	loaded: false,

	loadScript: function(callback) {
	    jQuery.getScript('http://static.duoshuo.com/embed.js',callback);
	},

	showDuoshuo: function(uid) {

	    var el = document.createElement('div');
	    el.setAttribute('data-thread-key', uid);
	    el.setAttribute('data-url', siteJson.get_url('myApp#post/'+uid));

	    DUOSHUO.EmbedThread(el);
		DUOSHUO.initSelector('.ds-share',{type:'ShareWidget'});

		$('.post').append(el);

	},

	loadDuoshuo: function(uid) {
	    var that = this;
	    if(!this.loaded)
		   this.loadScript(function(){
			   that.showDuoshuo(uid);
			   that.loaded = true;
		   });
	    else {
		   that.showDuoshuo(uid);
	    }
	}
};

var layoutData={

	  noItems     : '暂无内容，请稍后重试。',
	  notFound    : '对不起，内容不存在。',
	  error       : '服务器或网络无应答，稍后重试。',
	  noRefresh   : false,
	  pageMode    : '',
	  cacheOffset : 0,
	  forum       : 'all',
	  lastRefresh : 0,
	  refreshTimes: 0
}

/*-----------------------functions part begin---------------------------------*/
//timestamp function
function showTime() {
	$('.timestamp.collapse').each(function(){
		$(this).text( moment($(this).text(), "YYYY-MM-DD HH:mm:ss").fromNow().split(' ').join(''));
		$(this).removeClass('collapse');
	});
}

//message function
function showMsg(msg,type,fadeOut) {

	var style="alert ";
	style = !arguments[1]? style+'alert-warning':style+'alert-'+type;

	if(arguments[2] || $('.myContainer').html()) {
		//$('.myContainer').prepend('<p class="'+ style +' my-info">'+msg+'</p>');
		//$('.my-info').fadeOut(2000,function(){
		//	$(this).remove();
		//});
		return false;
	}

	layoutData.pageMode = '';
	$('#wrapper').css({"left": "0"});

	$("#wrapper").loadTemplate($("#page_content"),{},{noDivWrapper: true,success: function(){
		$('.myContainer').html('<p class="'+ style +' my-info">'+msg+'</p>');
		$('.curMenu').text('提示信息');
		scrollTo(0,0);
		$('.ajaxLoader').hide();
	}});
}

//get the forums menu
var myInitCache = {

	//binding the event for the menu
	menuEvent : function (){
		$('#menu-clicker').click(function(){
			$('.menuNav').slideToggle();
			$('.bodyMask').addClass('sureMask').toggle();

			$('a:not(#menu-clicker),.bodyMask').click(function(){
				$('.menuNav').hide();
				$('.bodyMask').hide();
			});
		});
	},

	loadMenuTemplate : function(forums) {
		var that = this;
		 $(".my_box_nav").loadTemplate($("#forum_menu"), forums, {
			   success: that.menuEvent,
			   noDivWrapper: true //surpress the additional div wrappers
			   //afterInsert: function($elem) {$elem.find('.col-xs-4').unwrap().unwrap();}
		 });
	},

	getMenu : function(){
		 var that = this;
		 var menuRemote = function() {
			 $.ajax({
				  type:  'GET',
				  url :  siteJson.get_url('myApp/forums'),
				  dataType: 'json',
				  success: function(forums) { //datatype is json,no need to use eval or JSON.parse
					  that.loadMenuTemplate(forums);
					  that.setForumMenuCache(forums);
				  }
			 });
		 };

		 if(!localStorage.getItem('forums')) menuRemote();
		 else that.getForumMenuCache();

	},
	getEmotion : function(){
		 var emotionRemote = function() {
			 $.ajax({
				  type:  'GET',
				  url :  siteJson.get_url('myApp/emotions'),
				  dataType: 'json',
				  success: function(emotions) { //datatype is json,no need to use eval or JSON.parse
					  myLocalStorage.myJsonSet('settings','emotions',emotions)
				  }
			 });
		 };
		 if(!myLocalStorage.myJsonGet('settings','emotions')) emotionRemote();
	},
	setForumMenuCache: function(forumsData) {
		var forums = {all :'全站'};
		var menu = [];

		$.each(forumsData,function(index,value) {
			eval('forums.' + value.brief +' = "'+ value.title+'";');
			menu.push({brief: value.brief, color: value.color, title: value.title});
		});

		localStorage.setItem('forums',JSON.stringify(forums));
		localStorage.setItem('menu',JSON.stringify(menu));
	},
	getForumMenuCache : function() {
		var menu = localStorage.getItem('menu');
		menu = JSON.parse(menu);
		this.loadMenuTemplate(menu);
	},
	getForumTitle : function(forum) {
		var forums = localStorage.getItem('forums');
		forums = JSON.parse(forums);
		return eval('forums.'+forum);
	},
    initial: function()	{
		this.getMenu();
		this.getEmotion();
	}
}

//all functions used for webDB
var myDBClass = {
	//open the webDB
	openDB: function() {
		var db = openDatabase('mydatabase', '2.0', 'Database for TOP10', 3 * 1024 * 1024);
		return db;
	},
	//initial webDB for the app
    initialDB: function() {
		var db = this.openDB();
		db.transaction(function (tx) {
		   tx.executeSql('CREATE TABLE IF NOT EXISTS lists (uid unique,title,content,post_time, top_time,forum,forum_title,replies,timestamp)');
		   tx.executeSql('CREATE TABLE IF NOT EXISTS posts (uid unique, title,content,username,forum,forum_title,post_time,replies,original_url,hits,emotion,timestamp)');
		   tx.executeSql('CREATE TABLE IF NOT EXISTS replies (uid unique,tid,content,username,post_time,support,oppose,timestamp)');
		   tx.executeSql('CREATE TABLE IF NOT EXISTS htmls (hashid unique,content,timestamp)');
		});
    },
	//clear the webDB
	clearDB: function() {
		var db = this.openDB();
		db.transaction(function (tx) {
		   tx.executeSql('DROP TABLE lists');
		   tx.executeSql('DROP TABLE posts');
		   tx.executeSql('DROP TABLE replies');
		   tx.executeSql('DROP TABLE htmls');
		});
	},
	//delete item from DB
	deleteItem: function(table,id,value,callback) {
		var db = this.openDB();
		db.transaction(function (tx) {
		   tx.executeSql("DELETE FROM "+ table +" where "+ id +"='"+ value +"'",[],function(tx,result){
			   callback();
		   });
		});
	},
	//save post list into webDB
	setPostListCache: function (lists) {
		var db = this.openDB();
		var timestamp = moment().format('X');
		$.each(lists,function(index,list) {
		   db.transaction(function (tx) {
			  tx.executeSql("INSERT INTO lists (uid,title,content,post_time,top_time,forum,forum_title,replies,timestamp) values(?,?,?,?,?,?,?,?,?)",
			  [list.uid,list.title,list.content,list.post_time,list.top_time,list.forum,list.forum_title,list.replies,timestamp],
				  function(tx,result) {
					 console.log('data cached!');
				  },
				  function(tx,error) {
					  tx.executeSql('UPDATE lists set top_time ='+list.top_time+',timestamp ='+timestamp+' where uid="'+list.uid+'"');
				  });
		   });
		});

	},
	//get post list  from webDB
	getPostListCache: function(forum) {
		var db = this.openDB();
		var sql = forum!='all'?"SELECT * FROM lists where forum = '"+forum+"' order by datetime(top_time) desc, replies+1 desc limit 10 offset " + layoutData.cacheOffset : "SELECT * FROM lists order by date(top_time) desc, replies+1 desc,post_time desc limit 10 offset " + layoutData.cacheOffset;

		db.transaction(function (tx) {
		   tx.executeSql(sql,[],
		     function(tx,result) {
				if(result.rows.length) {
				   var lists=[];
				   for(var i = 0; i < result.rows.length; i++)	{
                      lists.push(result.rows.item(i));
				   }
				   showPageClass.showPostList({posts: lists,forum: forum},'append',true);
				   layoutData.cacheOffset += 10;
				   if(!sessionStorage.getItem(forum+'Loaded') || moment().format('X')-parseInt(sessionStorage.getItem(forum+'Loaded')) > 600) {
					   setTimeout(function(){
						   ajaxLoad();
					   },300);
				   }
				}
				else
				   if(!layoutData.cacheOffset) ajaxLoad();
		     },
			 function(tx,error) {
				console.log(error.message);
				ajaxLoad();
			 }
		   );
		});
	},
	//save post content data into webDB
	setPostContentCache: function (post) {
		var db = this.openDB();
		var timestamp = moment().format('X');
		db.transaction(function (tx) {
		   tx.executeSql("INSERT INTO posts (uid, title,content,username,forum,forum_title,post_time,replies,original_url,hits,emotion,timestamp) values(?,?,?,?,?,?,?,?,?,?,?,?)",
		   [post.uid, post.title, post.content, post.username, post.forum,post.forum_title,post.post_time,post.replies,post.original_url,post.hits,post.emotion,timestamp],
		   function(tx,result) {
			  tx.executeSql('UPDATE lists set replies ='+post.replies+',timestamp ='+timestamp+' where uid="'+post.uid+'"');
		   });
		});
	},
	//refresh post content data
	updatePostContentCache: function (post,uid) {
		var that = this;
		this.deleteItem('posts','uid',uid,function(){
			that.setPostContentCache(post);
		});
	},
	//get post content  from webDB
	getPostContentCache: function(uid) {
		var db = this.openDB();
		var that = this;
		db.transaction(function (tx) {
		   tx.executeSql("SELECT * FROM posts where uid = '"+uid+"' limit 1",[],
		     function(tx,result) {
				if(result.rows.length) {
					showPageClass.showPostContent({post : result.rows.item(0)},true);

					if(moment().format('X')-result.rows.item(0).timestamp > 24*3600 )
					  setTimeout(function(){myAjaxClass.refreshPostContentCache(uid);},1000);
				}
				else
					ajaxLoad();
		     },
			 function(tx,error) {
				console.log(error.message);
				ajaxLoad();
			 }
		   );
		});
	},
	//save replies for post into webDB
	setRepliesCache: function (replies) {
		var db = this.openDB();
		var timestamp = moment().format('X');
		$.each(replies,function(index,reply) {
		   db.transaction(function (tx) {
			  tx.executeSql("INSERT INTO replies (uid,tid,content,username,post_time,support,oppose,timestamp) values(?,?,?,?,?,?,?,?)",
			  [reply.uid, reply.tid, reply.content, reply.username, reply.post_time,reply.support,reply.oppose,timestamp]);
		   });
		});
	},
	//refresh post content data
	updateRepliesCache: function (replies,uid) {
		var that = this;
		this.deleteItem('replies','tid',uid,function(){
			that.setRepliesCache(replies);
		});
	},
	//get post list  from webDB
	getRepliesCache: function(uid) {
		var db = this.openDB();
		db.transaction(function (tx) {
		   tx.executeSql("SELECT * FROM posts where uid = '"+uid+"' limit 1",[],
		     function(tx,result) {
				if(result.rows.length) {
				   var post = result.rows.item(0);
				       post.perPage = 20;
					   post.pages = Math.ceil(result.rows.item(0)['replies']/20.0);

				   db.transaction(function (tx) {
					  tx.executeSql("SELECT * FROM replies where tid = '"+ uid +"' order by datetime(post_time) asc",[],function(tx,result){
						  if(result.rows.length) {
							   var replies=[];
							   for(var i = 0; i < result.rows.length; i++)	{
								  replies.push(result.rows.item(i));
							   }
							   showPageClass.showReplies({post:post,replies:{data:replies}},true);

							   if(moment().format('X')-result.rows.item(0).timestamp > 24*3600 )
							       setTimeout(function(){myAjaxClass.refreshRepliesCache(uid);},1000);
						  }
						  else
							  ajaxLoad();
					  },
					  function(tx,error) {
				        console.log(error.message);
						ajaxLoad();
			          });
				   });
				}
				else
                    ajaxLoad();
		     },
			 function(tx,error) {
				console.log(error.message);
				ajaxLoad();
			 }
		   );
		});
	},
	//save html page content into webDB
	setHtmlCache: function (hashid,html) {
		var db = this.openDB();
		var timestamp = moment().format('X');
		html = JSON.stringify(html);
		db.transaction(function (tx) {
			  tx.executeSql("INSERT INTO htmls (hashid, content,timestamp) values(?,?,?)",[hashid, html,timestamp]);
		});
	},
	getHtmlCache: function(hashid) {
		var db = this.openDB();
		db.transaction(function (tx) {
			  tx.executeSql("SELECT * FROM htmls where hashid='"+hashid+"' limit 1",[],function(tx,result){
				  if(result.rows.length) {
					  var html = JSON.parse(result.rows.item(0)['content']);
					  showPageClass.showOtherContent(html,true);
				  }
				  else
					  ajaxLoad();
			  });
		});
	}
};

//Reload validation
var myReload = {

	interval: 20,
	maxTimes: 5,
	getInterval: function() {
		var interval = myLocalStorage.myJsonGet('reload','interval');
		if(interval) return parseInt(interval);
		else {
			myLocalStorage.myJsonSet('reload','interval',this.interval);
			return this.interval;
		}
	},
	addInterval: function() {
		var interval = this.getInterval();
		myLocalStorage.myJsonSet('reload','interval',interval+20);
	},
	getLast: function() {
		var lastTime = myLocalStorage.myJsonGet('reload','lastTime');
		return lastTime?parseInt(lastTime):0;
	},
	setLast: function() {
		myLocalStorage.myJsonSet('reload','lastTime',moment().format('X'));
		return false;
	},
	getTimes: function() {
		var times = myLocalStorage.myJsonGet('reload','times');
		if(times) return parseInt(times);
		else {
			myLocalStorage.myJsonSet('reload','times',0);
			return 0;
		}
	},
	addTimes : function() {
		var times = this.getTimes();
		myLocalStorage.myJsonSet('reload','times',times+1);
	},
	clearTimes :function() {
		myLocalStorage.myJsonSet('reload','times',0);
	},
	validate: function() {
		if(parseInt(moment().format('X')) - this.getLast() < this.getInterval() ) {
			if(this.getTimes() > this.maxTimes) {
			   this.addInterval();
			   this.addTimes();
			   this.setLast();
			   return false;
			}
			else  {
			   this.addTimes();
			   this.setLast();
			   return true;
			}
		}
		else {
			this.clearTimes();
			this.setLast();
			return true;
		}
	}
};

//Jquery template setting
$.addTemplateFormatter({
    timestamp : function(value, template) {
        return moment(value, "YYYY-MM-DD HH:mm:ss").fromNow().split(' ').join('');
    },
    postUrl: function(value,template) {
       return '#post/' + value;
    },
	shareUrl: function(value,template) {
       return siteJson.get_url('post/' + value);
    },
    forumLink: function(value,template) {
       return '#forum/'+ value;
    },
    forumColor: function(value,template) {
       return 'background-color: ' + value;
    },
	replies: function(value,template) {
        return value + '条评论';
    },
    source_link: function(value,template) {
       return '查看来自' + value + '的原文';
    },
	commentForm: function(value,template){
       return get_url('duoshuo/') + value;
    },
	replyLink: function(value,template){
	   return '#reply/' + value;
	},
	pages: function(value,template) {
       var str = '<li class="active"><span>1</span></li>';
	   for(var i=2; i <= parseInt(value);i++)
		 str= str+ '<li><a href=?page='+i+'></a></li>';
	   return str;
	}
});

/*---------------Content part begin--------------------*/

function clickPage() {

	$('.pagination a').each(function(){
		var href = $(this).attr('href').replace(siteJson.get_url('myApp/'),'#');
		$(this).attr('href',href);
	});

	scrollTo(0,0);

}

function scrollPage() {

	$('.pagination').hide();

	$('.replies').infinitescroll({
	loading: {
        finishedMsg: '',
        msgText: ''
    },
	navSelector  : "ul.pagination",
	nextSelector : "ul.pagination a:first",
	itemSelector : "div.reply",
    animate	     : false
	},function(arrayOfNewElems){

		$.each(arrayOfNewElems,function(){
			$(this).textFormat();
			$(this).myLazyLoad();
			$(this).repliesRender();
		});

		showTime();

	});

}

function scrollPageJson(perPage,pages) {

	$('.replies').infinitescroll({
		loading: {
        finishedMsg: '',
        msgText: ''
    },
	maxPage       : pages,
	navSelector   : "ul.pagination",
	nextSelector  : "ul.pagination a:first",
	animate	      : false,
	dataType      : 'json',
	appendCallback: false
	}, function(contentData,opts) {
		var replies = contentData.replies.data;
		repliesInsert(replies,opts.state.currPage,perPage);
	});
}

$.fn.extend({

    //lazyload the images
	myLazyLoad:function(){

	    $(this).find("img.lazyload").lazyload({
            placeholder : siteJson.get_url('images/loading.gif'),
            event :  "scroll",
            effect : "fadeIn"
        });

		$('.swipebox').swipebox({
			beforeOpen:function() {
				var hash = window.location.hash;
				window.onhashchange= function(){
					 layoutData.noRefresh=true;
					 $('#swipebox-close').trigger('click');
					 window.location.hash=hash;
					 window.onhashchange=webSqlLoad;
				}
			},
			afterClose: function() {
				window.onhashchange=webSqlLoad;
			}
		});

	},

	//common text string handling for all bbs
    textFormat: function (){
		var forum = $('.post_source').data('forum');
		var contents = $(this).find('.content');

		contents.each(function(){
			var str = $(this).html();
			str = str.replace(/(^|[^\"\'\]])(http|ftp|mms|rstp|news|https)\:\/\/([^\s\033\[\]\"\'\(\)（）。，]+)/gi,"$1[url]$2://$3[/url]");
			str = str.replace(/\[url\]http\:\/\/(\S+\.)(gif|jpg|png|jpeg|bmp|GIF|JPG|PNG|JPEG|BMP)\[\/url\]/gi,"[img]http://$1$2[/img]");

			str = str.replace(/\[url\](.+?)\[\/url\]/gi,"<a href=$1 target=\"_blank\">$1</a>");
			str = str.replace(/\[img\]http\:\/\/(\S+\.)(gif|jpg|png|jpeg|bmp|GIF|JPG|PNG|JPEG|BMP)\[\/img\]/gi,"<a href=\"http://$1$2\" class=\"swipebox\"><img src=\""+siteJson.get_url('images/image.png')+"\" class=\"img-responsive lazyload\" data-original=\"http://$1$2\"></a>");

			switch (forum) {
				case 'xjtu':
				case 'sysu':
					str = str.replace(/\n/g ,'</p><p>');
					break;
				case 'nju':
					str = nju_str(str);
					break;
				case 'whnet':
					str = whnet_str(str);
					break;
				case 'newsmth':
				    str = newsmth_str(str);
					break;
				case 'sjtu':
				    str = sjtu_str(str);
				    break;
				default :
					break;
			}

			str = str.replace(/\n\n/g ,'</p><p>');
			str = str.replace(/\n\s\s/g ,'</p><p>');
			str = str.replace(/\s\s/g ,'');

			str = str.split('</p><p>');
			for(var i=0; i<str.length; i++) {
				if(str[i].length>300) {
					str[i] = str[i].replace(/。\n/g ,'。</p><p>');
					if(str[i].indexOf('</p><p>') < 0) {
						str[i] = str[i].replace(/。/g ,'。</p><p>');
					}
				}

				str[i]= str[i].replace(/\n/g ,'nnn');
				str[i]= str[i].split('nnn');
				for(var j=0; j<str[i].length; j++){
					if(str[i][j].length<30){
						str[i][j]+='</p><p>';
					}
				}

				str[i] = str[i].join('');

			}

			str = str.join('</p><p>');

			str = '<p>' + str + '</p>';

			$(this).html(str);
		});

		contents.removeClass("invisible");

    },

	//Render for the replies after they have been inserted into the DOM
	repliesRender: function (page){
		   var replies = $(this).find('.reply-header');
		   var page = arguments[0]?arguments[0]:$('#currentPage').val();

		   replies.each(function(){
			   var thumb = myLocalStorage.myJsonGet('thumb','r' + $(this).parent().data('id'));
			   if(thumb)  {
				   $(this).addClass('done');
				   $(this).find('.'+thumb).addClass('selected');
			   }
			   $(this).data('thumb',thumb);
			});

			//set the thumb data according to the action
			replies = $(this).find('.reply-header:not(".done") .thumb');
			replies.one('click',function(){
			   var value = parseInt($(this).text());
			   var replyId = $(this).closest('.reply').data('id');
			   var postId  = $(this).closest('.thread').data('id');

			   $(this).text(value + 1);
			   $(this).addClass('selected');
			   $(this).parent().addClass('done');
			   $(this).siblings('.thumb').unbind();

			   $(this).parent().data('thumb',$(this).data('for'));
		       myLocalStorage.myJsonSet('thumb','r'+ replyId, $(this).parent().data('thumb'));

			   $.ajax({
				   type:"GET",
				   url:  siteJson.get_url('myApp/thumb/reply'),
				   data: 'uid='+ replyId + '&thumb=' +$(this).parent().data('thumb'),
				   success:function(){
					   setTimeout(function(){myAjaxClass.refreshRepliesCache(postId);},300);
				   },
				   error: function() {
					   myLocalStorage.myJsonRemove('thumb','r'+ replyId);
					   showMsg(layoutData.error);
				   }
			   });
			});

			replies = $(this).find('.reply-header');

			if(replies.length && ! myLocalStorage.myJsonGet('hit','p'+ $('.thread').data('id') + '_' + page)) {
					var uid = [];

					replies.each(function(){
						uid.push($(this).data('id'));
					});

					uid = uid.join('|');

					$.ajax({
						type:"GET",
						url:  siteJson.get_url('myApp/hit/reply'),
						data: 'uid='+ uid,
						success:function(data){
							myLocalStorage.myJsonSet('hit','p'+ $('.thread').data('id') + '_' + page,1);
						}
					 });
			}
	}
});

function repliesInsert(replies,page,perPage){
	$(".replies").loadTemplate($("#reply-item"),replies,{
		append:true,
		noDivWrapper: true,
		success:function(){
			$('.reply.collapse').textFormat();
			$('.collapse .content').myLazyLoad();
			$('.reply.collapse').repliesRender(page);
			showTime();
			$('.floor.collapse').each(function(i){
				$(this).html((page-1)*perPage+1+i+'楼').removeClass('collapse');
			});
			$('.reply.collapse').removeClass('collapse');
		}
	});
}

function attitudeInsert(post) {
	 var attitudes=[];
	 var values = eval("(" + post.emotion + ")");
	 var emotions = myLocalStorage.myJsonGet('settings','emotions');

	 $.each(emotions,function(index,value){
		attitudes.push({class: 'attitude '+ index +' col-xs-4', attitude: index, title: value, number: eval('values.'+index)});
	 });
	 $(".my-attitude").loadTemplate($("#attitude"),attitudes,{
		 noDivWrapper: true,
		 success: attitudeRender
	 });
}

//the render action for the attitude after it has been inserted into the DOM
function attitudeRender() {

	 var my_attitude = myLocalStorage.myJsonGet('attitude','p'+ $('.post').data('id'));

	 if(my_attitude) {
		 $('.' + my_attitude).addClass('selected');
		 $('.my-attitude').addClass('done');
	 }

	 $('.my-attitude').data('attitude',my_attitude);

	 $('.my-attitude:not(".done") .attitude').one('click',function(){
		   var num = $(this).find('.num');
		   var attitude = num.data('for');
		   var value = parseInt(num.text());

		   num.text(value + 1);
		   $(this).addClass('selected');
		   $('.my-attitude').addClass('done');
		   $('.my-attitude').data('attitude',attitude);

		   $('.attitude').unbind();

		   myLocalStorage.myJsonSet('attitude','p'+ $('.post').data('id'), $('.my-attitude').data('attitude'));

		   $.ajax({
				type:"GET",
				url:  siteJson.get_url('myApp/attitude/post'),
				data: 'uid='+ $('.post').data('id') + '&attitude=' + $('.my-attitude').data('attitude'),
				success:function() {
					setTimeout(function(){myAjaxClass.refreshPostContentCache($('.post').data('id'));},300);
				},
				error: function() {
					myLocalStorage.myJsonRemove('attitude','p'+ $('.post').data('id'));
					showMsg(layoutData.error);
				}
		   });
	 })
}

//Render the hitting action after the post content has been insert into the DOM
function postHitRender() {

   if(!myLocalStorage.myJsonGet('hit','p'+ $('.post').data('id'))) {

		 $.ajax({
				type:"GET",
				url:  siteJson.get_url('myApp/hit/post'),
				data: 'uid='+ $('.post').data('id'),
				success:function(data){
					myLocalStorage.myJsonSet('hit','p'+ $('.post').data('id'),1);
				}
		 });

	}
}

function sjtu_str(str) {
	return str.replace(/http:\/\/bbs\.sjtu/g,'https://bbs.sjtu');
}

function newsmth_str(str) {
	return str.replace(/fakeImg\.jpg/g ,'');  //without suffix for smth images url
}

//text string handling for nju bbs
function nju_str(str) {

    //nju smile
    var bbs_smile = ["[:T]","[;P]","[;-D]","[:!]","[:L]","[:?]","[:Q]","[:@]","[:-|]","[:(]","[:)]","[:D]","[:P]","[:'(]","[:O]","[:s]","[:|]","[:$]","[:X]","[:U]","[:K]","[:C-]","[;X]","[:H]","[;bye]","[;cool]","[:-b]","[:-8]","[;PT]","[;-C]","[:hx]","[;K]","[:E]","[:-(]","[;hx]","[:B]","[:-v]","[;xx]"];
    var bbs_reg = [/\[\:T\]/g,/\[;P\]/g,/\[;-D\]/g,/\[\:!\]/g,/\[\:L\]/g,/\[\:\?\]/g,/\[\:Q\]/g,/\[\:@\]/g,/\[\:-\|\]/g,/\[\:\(\]/g,/\[\:\)\]/g,/\[\:D\]/g,/\[\:\P\]/g,/\[\:\'\(\]/g,/\[\:O\]/g,/\[\:s\]/g,/\[\:\|\]/g,/\[\:\$\]/g,/\[\:X\]/g,/\[\:U\]/g,/\[\:K\]/g,/\[\:C-\]/g,/\[;X\]/g,/\[\:H\]/g,/\[;bye\]/gi,/\[;cool\]/gi,/\[\:-b\]/g,/\[\:-8\]/g,/\[;PT\]/gi,/\[;-C\]/g,/\[\:hx\]/g,/\[;K\]/g,/\[\:E\]/g,/\[\:-\(\]/g,/\[;hx\]/g,/\[\:B\]/g,/\[\:-v\]/g,/\[;xx\]/gi];
    var bbs_pic = [19,20,21,26,27,32,18,11,10,15,14,13,12,9,0,2,3,6,7,16,25,29,34,36,39,4,40,41,42,43,44,47,49,50,51,52,53,54];

    for (i = 0; i < bbs_smile.length; i++) {
        var smileStr = "<img src=http://bbs.nju.edu.cn/images/blank.gif width=1><img src='http://bbs.nju.edu.cn/images/face/"+bbs_pic[i]+".gif' alt='"+bbs_smile[i]+"'><img src=http://bbs.nju.edu.cn/images/blank.gif width=1>";
        str = str.replace(bbs_reg[i], smileStr);
    }

    return str;
}

//text string handling for whNet bbs
function whnet_str(str) {

    // hust smiles
    var bbs_smiles = new Array("anger", "arrogant", "bad", "baoquan", "beat", "berserk", "bs", "byebye", "cahan", "cake", "chajin", "clap", "coldsweat", "cool", "cry", "curse", "dive", "dog", "embarrassed", "envy", "fade", "faint", "fear", "fighting", "fist", "flash", "grievance", "grin", "hand", "heartbroken", "hug", "hungry", "insidious", "jump", "kawayi", "kill", "knife", "koubi", "love", "loveyou", "mua", "nap", "naughty", "no", "ok", "petrify", "piezui", "pig", "pitiful", "proud", "puke", "qiu", "question", "rose", "sad", "salute", "seduce", "shake", "shiai", "shock", "shuai", "shutup", "shy", "simper", "skeleton", "sleepy", "smile", "smoke", "snigger", "stupid", "sun", "sweat", "tear", "thumbdown", "torment", "unhappy", "uplook", "winer", "wv", "xia", "xu", "yawn", "yhh", "zan", "zhh");
    for (i = 0; i < bbs_smiles.length; i++) {
        var reg = new RegExp("\\[" + bbs_smiles[i] + "\\]", "g");
        var smileStr = "<img src=\"http://bbs.whnet.edu.cn/style/emotion/" + bbs_smiles[i] + ".gif\" alt=\"[" + bbs_smiles[i] + "]\" width=\"20\" height=\"20\" border=\"0\" />";
        str = str.replace(reg, smileStr);
    }

    return  str;
}

/*----------Scroll part begin----------------*/
var myScroll,
	pullDownEl, pullDownOffset,
	pullUpEl, pullUpOffset,
	onPulling = false;

function checkPullDown() {

	if(parseInt(moment().format('X'))-layoutData.lastRefresh < 30 ) {
		if(layoutData.refreshTimes > 5)
		   return false;
	    else
		   return true;
	}
	else {
		layoutData.refreshTimes = 0;
		return true;
	}
}

function pullDownAction () {
	onPulling = true;
	setTimeout(function(){
		ajaxLoad();
		layoutData.refreshTimes++;
		layoutData.lastRefresh = parseInt(moment().format('X'));
		onPulling = false;
	},200);
}

function pullUpAction () {
	onPulling = true;
	setTimeout(function(){
		myDBClass.getPostListCache(layoutData.forum);
		onPulling = false;
	    setTimeout(function(){myScroll.refresh();},200);
	},200);
}

function loaded() {

	pullDownEl = $('#pullDown');
	pullUpEl = $('#pullUp');

	$('.pullDownLabel').html('下拉刷新');
	$('.pullUpLabel').html('上拉加载更多');

	if(!pullDownEl.length || !pullUpEl.length) return false;

	pullDownOffset = pullDownEl.get(0).offsetHeight;
	pullUpOffset = pullUpEl.get(0).offsetHeight;

	myScroll = new iScroll('wrapper', {
		useTransition: true,
		topOffset: pullDownOffset,
		bottomOffset: pullUpOffset,
		vScrollbar: false,
		onRefresh: function () {
			if (pullDownEl.hasClass('loading')) {
				pullDownEl.attr('class','');
				$('.pullDownLabel').html('下拉刷新');
			} else if (pullUpEl.hasClass('loading')) {
				pullUpEl.attr('class','');
				$('.pullUpLabel').html('上拉加载更多');
			}
		},
		onScrollMove: function () {
			if (this.y > 5 && !pullDownEl.hasClass('flip')) {
				pullDownEl.attr('class','flip');
				$('.pullDownLabel').html('松开即获取');
				this.minScrollY = 0;
			} else if (this.y < 5 && pullDownEl.hasClass('flip')) {
				pullDownEl.attr('class','');
				$('.pullDownLabel').html('下拉刷新');
				this.minScrollY = -pullDownOffset;
			} else if (this.y < (this.maxScrollY - 5) && !pullUpEl.hasClass('flip')) {
				pullUpEl.attr('class','flip');
				$('.pullUpLabel').html('松开即加载');
				this.maxScrollY = this.maxScrollY;
			} else if (this.y > (this.maxScrollY + 5) && pullUpEl.hasClass('flip')) {
				pullUpEl.attr('class','');
				$('.pullUpLabel').html('上拉加载更多');
				this.maxScrollY = pullUpOffset;
			}
		},
		onScrollEnd: function () {
			if (pullDownEl.hasClass('flip')) {
				if(checkPullDown()) {
					pullDownEl.attr('class','loading');
				    $('.pullDownLabel').html('正在获取...');
				    pullDownAction();
				}else {
				    $('.pullDownLabel').html('刷新太频繁啦！');
				    setTimeout(function(){myScroll.refresh();},500);
                }
			} else if (pullUpEl.hasClass('flip')) {
				pullUpEl.attr('class','loading');
				$('.pullUpLabel').html('加载中...');
				pullUpAction();
			}
		}
	});

	setTimeout(function(){ $('#wrapper').css({"left": "0"}); },200);
}

var myAjaxClass= {
	refreshPostContentCache: function(uid) {
		$.ajax({
			type:"GET",
			url:  siteJson.get_url('myApp/post/' + uid),
			dataType: 'json',
			success:function(data){
				myDBClass.updatePostContentCache(data.post,uid);
			}
		});
	},
	refreshRepliesCache: function(uid) {
		$.ajax({
			type:"GET",
			url:  siteJson.get_url('myApp/reply/' + uid),
			dataType: 'json',
			success:function(data){
				myDBClass.updateRepliesCache(data.replies.data,uid);
			}
		});
	}
};

var showPageClass = {

    //insert data into page
	showPostList: function (listData,order,cache) {

		var curMenu = myInitCache.getForumTitle(listData.forum);
		var prepend = order=='prepend'?true:false;
		var append  = order=='append'?true:false;

		var insertData=[];

		if(layoutData.pageMode!='scroll' || $('.curMenu').text() !=curMenu) {
			layoutData.pageMode = 'scroll';
			layoutData.cacheOffset = 0;
			layoutData.forum = listData.forum;
			pageViewer(); //pageViewer must run after forum changed,otherwise, some short forum list will disapear when scrolling fast upwards!
			$('.curMenu').text(curMenu);
		}

		$.each(listData.posts,function(index,post){
			if(!$('.top-list[href="#post/'+ post.uid +'"]').length)
				insertData.push(post);
		});

		if(!insertData.length) {
			showMsg(layoutData.noItems);
		}
		else {
			if(!$('.top-list').length) {
			    prepend = false;
				append = false;
			}

			$(".myContainer").loadTemplate($("#list_items"),insertData,{
				prepend     : prepend,
				append      : append,
				//noDivWrapper: true //surpress the additional div wrappers
				//this option above conflicts with prepend option!!!
				afterInsert : function($elem) {$elem.find('a').unwrap().unwrap();}
		    });
		}

		if(!cache) {
			sessionStorage.setItem(listData.forum+'Loaded',moment().format('X'));

			if(insertData.length)
				myDBClass.setPostListCache(insertData);
		}

	},
	showPostContent : function (contentData,cache) {

		if(layoutData.pageMode!='common') {
			layoutData.pageMode = 'common';
			pageViewer();
		}

		$('.myContainer').loadTemplate($('#post_item'),contentData.post,{
			noDivWrapper: true,
			success:function(){
				$('.post').textFormat();
				showTime();
				$('.curMenu').text(contentData.post.forum_title);
				$('.content').myLazyLoad();
				attitudeInsert(contentData.post);
				scrollTo(0,0);

				setTimeout(function(){
					postHitRender();
					$('#shareDiv .container').loadTemplate($('#shareTemplate'),contentData.post,{
						noDivWrapper: true,
						success:function(){
							myDuoshuo.loadDuoshuo(contentData.post.uid);
						}
					});
				},300);
			}
		});

		if(!cache) {
			myDBClass.setPostContentCache(contentData.post);
		}
	},
	showReplies : function (contentData,cache) {
		if(layoutData.pageMode!='common') {
			layoutData.pageMode = 'common';
			pageViewer();
		}

		$('.myContainer').loadTemplate($('#thread-with-reply'),contentData.post,{
			noDivWrapper: true,
			success:function(){
				$('.curMenu').text(contentData.post.forum_title);
                repliesInsert(contentData.replies.data,1,contentData.post.perPage);
				scrollTo(0,0);
				$('.pagination li a').each(function(){
					 $(this).attr('href',siteJson.get_url('myApp/reply/'+contentData.post.uid+$(this).attr('href')));
				});
				scrollPageJson(contentData.post.perPage,contentData.post.pages);
			}
		});

		if(!cache) {  //only first page of replies cached, Coz replies not need to cache so much!!!!
			myDBClass.setRepliesCache(contentData.replies.data);
		}
	},
	showOtherContent : function (contentData,cache) {
		if(layoutData.pageMode!='common') {
			layoutData.pageMode = 'common';
			pageViewer();
		}

		$('.myContainer').loadTemplate($('#html-page'),contentData.html,{noDivWrapper: true });
		if(!cache) {
			myDBClass.setHtmlCache(window.location.hash,contentData);
		}
	}
}

//get data from local websql
function webSqlLoad() {

	  $('.menuNav').hide();
	  $('.bodyMask').hide();

	  var hash = window.location.hash;
	  var path = hash.split('/');

	  if(layoutData.noRefresh) {
		 layoutData.noRefresh = false;
		 return false;
	  }

	  layoutData.cacheOffset = 0;

	  $('.myContainer').html('');
	  $('#shareDiv').modal('hide');

	  switch(path[0]) {
		  case '#post':
			myDBClass.getPostContentCache(path[1]);
			break;
		  case '':
		  case '#index':
			myDBClass.getPostListCache('all');
			break;
		  case '#forum':
			myDBClass.getPostListCache(path[1]);
			break;
		  case '#reply':
			myDBClass.getRepliesCache(path[1]);
			break;
		  default:
			myDBClass.getHtmlCache(hash);
	  };

	  if(layoutData.pageMode=='scroll') setTimeout(function(){myScroll.refresh();},200);
}

//get new data for the page by ajax
function ajaxLoad() {

	  $('.menuNav').hide();
	  $('.bodyMask').hide();

	  var url = window.location.hash;
	  url = url?url.replace('#',''):'index';

	  $.ajax({
		  type:  'GET',
		  url :  siteJson.get_url('myApp/' + url),
		  dataType: 'json',
		  beforeSend: function() {

			  if(layoutData.noRefresh) {
				 layoutData.noRefresh = false;
				 return false;
			  }

			  if(!onPulling) $('.ajaxLoader').show();
			  return true;

		  },
		  success: function(data,textStatus, jqXHR) {
			  try{
				 //data = JSON.parse(data);
				 if(data.template=='list_items') showPageClass.showPostList(data,'prepend',false);
				 else if(data.template=='post_item')  showPageClass.showPostContent(data,false);
				 else if(data.template=='replies')  showPageClass.showReplies(data,false);
				 else showPageClass.showOtherContent(data,false);
			  }
			  catch(err){
				  if(jqXHR.status =='204') {
					  if(url.split('/')[0]=='post' || url.split('/')[0]=='reply') {
						  myDBClass.deleteItem('lists','uid',url.split('/')[1],function(){});
						  myDBClass.deleteItem('posts','uid',url.split('/')[1],function(){});
						  myDBClass.deleteItem('replies','tid',url.split('/')[1],function(){});
					  }
				  }
				  showMsg(layoutData.notFound,'danger');
			  }
		  },
		  error: function(XMLHttpRequest, textStatus, errorThrown) {

			  //console.log(XMLHttpRequest.status);

			  showMsg(layoutData.error,'danger');
		  },
		  complete: function(){
			  $('.ajaxLoader').hide();
			  if(layoutData.pageMode=='scroll') setTimeout(function(){myScroll.refresh();},200);
		  }

	  })
}

function touchMoveEvent(e) { e.preventDefault(); }

function pageViewer() {

	if(layoutData.pageMode=='scroll') {
		$('body').attr('id','bodyScroll');
		$('#wrapper').css({"left": "-9999px"});
		$("#wrapper").loadTemplate($("#page_list"),{},{noDivWrapper: true,success:function(){
			document.addEventListener('touchmove', touchMoveEvent, false);
            setTimeout(loaded,200);
		}});
	}
	else {
		$('body').attr('id','bodyCommon');
		$("#wrapper").loadTemplate($("#page_content"),{},{noDivWrapper: true,success: function(){
			setTimeout(function(){
				document.removeEventListener('touchmove', touchMoveEvent);
		        if(myScroll) myScroll.destroy();
			},200);
		}});
	}
}

function mobileAndTabletcheck() {
	  var check = false;
	  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	  return check;
}

$(function(){

	if(!mobileAndTabletcheck()) {
	    var href = window.location.href.replace('myApp','');
	    href = href.replace('#','');
	    window.location.replace(href);
	}

	$('.today').text(moment().format('MMMDo dddd')).show();

	$('#back-button').click(function(){
		history.go(-1);
	});

	if(!myLocalStorage.isSupported()) {
	   showMsg('请退出无痕浏览模式');
	   return false;
	}

	myLocalStorage.refresh();
	/*
	if(!myReload.validate()) {
	   showMsg('警告：您的访问过于频繁!','danger');
	   return false;
	}*/

	myInitCache.initial();

	webSqlLoad();

	setTimeout(function(){
		window.onhashchange = webSqlLoad;
	},200);

});