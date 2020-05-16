$(function(){

    $('.today').text(moment().format('MMMDo dddd')).show();

    $('.timestamp').each(function(){
        $(this).text( moment($(this).text(), "YYYY-MM-DD HH:mm:ss").fromNow().split(' ').join('')).show();
    });

    $('#menu-clicker').click(function(){
		$('.menuNav').slideToggle();
		$('.bodyMask').addClass('sureMask').toggle();
		
		$('a:not(#menu-clicker),.bodyMask').click(function(){
			$('.menuNav').hide();
			$('.bodyMask').hide();
		});
	});

    $('.content').each(function(){
        var str = $(this).html();
        var forum = $('.post_source').data('forum');
		
		str = str.replace(/(^|[^\"\'\]])(http|ftp|mms|rstp|news|https)\:\/\/([^\s\033\[\]\"\'\(\)（）。，]+)/gi,"$1[url]$2://$3[/url]");
        str = str.replace(/\[url\]http\:\/\/(\S+\.)(gif|jpg|png|jpeg|bmp|GIF|JPG|PNG|JPEG|BMP)\[\/url\]/gi,"[img]http://$1$2[/img]");

        str = str.replace(/\[url\](.+?)\[\/url\]/gi,"<a href=$1 target=\"_blank\">$1</a>");
        str = str.replace(/\[img\]http\:\/\/(\S+\.)(gif|jpg|png|jpeg|bmp|GIF|JPG|PNG|JPEG|BMP)\[\/img\]/gi,"<img src=\""+siteJson.get_url('images/image.png')+"\" class=\"img-responsive lazyload\" data-original=\"http://$1$2\">");
        
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
		
		$('.word').removeClass("invisible");
		
		$("img.lazyload").lazyload({    
			placeholder : siteJson.get_url('images/loading.gif'),    
			event : "scroll",
            effect : "fadeIn"			
		});
		
    });

    //reply thumb handling
    //initial thumb setting
    $('.reply-header').each(function(){
        var thumb = $.cookie('reply_' + $(this).parent().data('id'));
        if(thumb)  {
            $(this).addClass('done');
            $(this).find('.'+thumb).addClass('selected');
        }
        $(this).data('thumb',thumb);
    });

    //set the thumb data according to the action
    $('.reply-header:not(".done") .thumb').one('click',function(){
        var value = parseInt($(this).text());

        $(this).text(value + 1);
        $(this).addClass('selected');
        $(this).parent().addClass('done');
        $(this).siblings('.thumb').unbind();

        $(this).parent().data('thumb',$(this).data('for'));
        $.cookie('reply_'+ $(this).closest('.reply').data('id'), $(this).parent().data('thumb'));

        $.post(
            siteJson.get_url('thumb/reply'),
            {
                uid:       $(this).closest('.reply').data('id'),
                thumb:     $(this).parent().data('thumb'),
                _token :   $('[name="_token"]').val()
            }
        );
    })

    //post attitude handling
   if($('.post').length) {
       var my_attitude = $.cookie('post_'+ $('.post').data('id'));
       if(my_attitude) {
           $('.' + my_attitude).addClass('selected');
           $('.my-attitude').addClass('done');
       }
       $('.my-attitude').data('attitude',my_attitude);
   }

    $('.my-attitude:not(".done") .attitude').one('click',function(){
        var num = $(this).find('.num');
        var attitude = num.data('for');
        var value = parseInt(num.text());

        num.text(value + 1);
        $(this).addClass('selected');
        $('.my-attitude').addClass('done');
        $('.my-attitude').data('attitude',attitude);

        $('.attitude').unbind();

        $.cookie('post_'+ $('.post').data('id'), $('.my-attitude').data('attitude'));

        $.post(
            siteJson.get_url('attitude/post'),
            {
                uid:       $('.post').data('id'),
                attitude: $('.my-attitude').data('attitude'),
                _token :  $('[name="_token"]').val()
            }
        );

    })

    if($('.post').length && !$.cookie('hit_post_'+ $('.post').data('id'))) {
        $.post(
            siteJson.get_url('hit/post'),
            {
                uid:       $('.post').data('id'),
                _token :       $('[name="_token"]').val()
            },function(){
                $.cookie('hit_post_'+ $('.post').data('id'), 1);
            }
        );
    }

    if($('.reply').length && !$.cookie('hit_reply_'+ $('.thread').data('id') + '_' +$('#currentPage').val())) {
        var uid = [];

        $('.reply').each(function(){
            uid.push($(this).data('id'));
        });

        uid = uid.join('|');

        $.post(
            siteJson.get_url('hit/reply'),
            {
                uid:      uid,
                _token :      $('[name="_token"]').val()
            },function(){
                $.cookie('hit_reply_'+ $('#currentPage').val(), 1);
            }
        );
    }

})
	
function sjtu_str(str) {
	return str.replace(/http:\/\/bbs\.sjtu/g,'https://bbs.sjtu');
}		

function newsmth_str(str) {
	return str.replace(/fakeImg\.jpg/g ,'');  //without suffix for smth images url
}

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

function whnet_str(str) {

    // whnet smiles
    var bbs_smiles = new Array("anger", "arrogant", "bad", "baoquan", "beat", "berserk", "bs", "byebye", "cahan", "cake", "chajin", "clap", "coldsweat", "cool", "cry", "curse", "dive", "dog", "embarrassed", "envy", "fade", "faint", "fear", "fighting", "fist", "flash", "grievance", "grin", "hand", "heartbroken", "hug", "hungry", "insidious", "jump", "kawayi", "kill", "knife", "koubi", "love", "loveyou", "mua", "nap", "naughty", "no", "ok", "petrify", "piezui", "pig", "pitiful", "proud", "puke", "qiu", "question", "rose", "sad", "salute", "seduce", "shake", "shiai", "shock", "shuai", "shutup", "shy", "simper", "skeleton", "sleepy", "smile", "smoke", "snigger", "stupid", "sun", "sweat", "tear", "thumbdown", "torment", "unhappy", "uplook", "winer", "wv", "xia", "xu", "yawn", "yhh", "zan", "zhh");
    for (i = 0; i < bbs_smiles.length; i++) {
        var reg = new RegExp("\\[" + bbs_smiles[i] + "\\]", "g");
        var smileStr = "<img src=\"http://bbs.whnet.edu.cn/style/emotion/" + bbs_smiles[i] + ".gif\" alt=\"[" + bbs_smiles[i] + "]\" width=\"20\" height=\"20\" border=\"0\" />";
        str = str.replace(reg, smileStr);
    }

    return  str;
}
