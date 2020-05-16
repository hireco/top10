$(function(){

    if(mobileAndTabletcheck()) {
		var href = window.location.href.replace(siteJson.base_url,'');
		href = href=='/'?href.replace('/','/myApp#index'):href.replace('/','/myApp#');
		window.location.replace(siteJson.base_url + href);
		return false;
	}

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

function mobileAndTabletcheck() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
}
