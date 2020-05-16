<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reply;
use Carbon\Carbon;
use Hashids;
use App\Models\Setting;
use Psy\Util\Json;

class MultiPostController extends Controller
{
    //

    protected  $finished = 0;
	
    function post(Request $request) {
        $inputs = $request->input();

        $keys=['content','post_time','username','nickname','post_id'];

        foreach($keys as $key) {
            $inputs[$key] = explode('|**=**|',$inputs[$key]);
            $len[$key] = count($inputs[$key]);
//            echo $key.':'.count($inputs[$key])."\n";
        }

        for($i = 0; $i < min($len); $i++) {
            foreach($keys as $key) {
                if(isset($inputs[$key][$i])) $data[$key] = $inputs[$key][$i];
                else echo '[msg]'.$key.'['.$i.'] undefined [/msg]'."\n";
            }

            $data['title'] = $inputs['title'];
            $data['thread_id'] = $inputs['thread_id'];
            $data['category'] = $inputs['category'];
            $data['forum'] = $inputs['forum'];
            $data['original_url'] = $inputs['original_url'];

            if($data['forum'] == 'scut')
                $data['post_id']= $data['thread_id'] + $i*10000;
            else if ($data['forum'] == 'fudan') {
                $data['post_id'] = (int)($data['post_id']/10000000000);
                $data['thread_id'] = (int)($data['thread_id']/10000000000);
            }
            
			if(strpos($data['post_time'],'年'))
                $data['post_time'] = Carbon::createFromFormat('Y年m月d日H:i:s', $data['post_time']);
            else if($data['forum'] == 'scut' || $data['forum'] == 'szu')
                $data['post_time'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['post_time']);
            else
                $data['post_time'] = Carbon::createFromFormat('D M j H:i:s Y', $data['post_time']);


            $this->alter_id($data);

            if($data['tid'] == $data['uid'])
                $this->insert_post($data);
            else
                $this->insert_reply($data);
        }

        return $this->echo_str();
    }

    private function  insert_post($data) {
        unset($data['tid']);
        
        $entry = Post::withTrashed()->where(['uid' => $data['uid']])->first();
        
		if($entry)  return false; //主贴已存在，取消插入
        
		$this->get_images($data);
		
        $entry = Post::create($data);

        $entry->top_time = date('Y-m-d H:i:s',time());
        $entry->get_time = $entry->top_time;
        $entry->emotion = $this->init_emotion();
        $entry->save();
		
		$this->finished = 1;
    }

    private function  insert_reply($data) {
        unset($data['title'],$data['original_url']);

        $post = Post::where(['uid' => $data['tid']])->first();
        if(!$post)  return false;  //主贴不存在，取消插入回帖

        $reply = Reply::withTrashed()->where(['uid' => $data['uid']])->first();
        if($reply)  return false;  //该回帖已存在，取消插入

		$this->get_images($data);
		
        $reply = Reply::create($data);
        $reply->save();

        $post->replies = $post->replies + 1;
        $post->save();
		
		$this->finished = 1;
    }

    function  alter_id(&$data) {
        $forum_id = \App\Models\Forum::where('brief',$data['forum'])->first()->id;
        $data['thread_id'] += $forum_id;
        $data['post_id'] += $forum_id;

        $data['uid'] =  Hashids::encode($data['post_id']);
		$data['tid'] =  Hashids::encode($data['thread_id']);
		
		unset($data['thread_id'],$data['post_id']);
    }

    private function echo_str() {
        if($this->finished)
            return '[ok]';
        else
            return '[err]内容重复[/err]';
    }

    private  function init_emotion() {

        $emotions = json_decode(Setting::where('name','emotions')->first()->value);
        foreach($emotions as $index => $value)
            $data[$index] = 0;

        return  Json::encode($data);

    }
	
	private function get_images(&$data) {
		
		if(!in_array($data['forum'],['nju','sjtu'])) return false;
		
		$regEx = '/(http:\/\/bbs\.'.$data['forum'].'\.edu\.cn){1}(.*)\.(jpg|gif|png|jpeg|bmp|JPG|GIF|PNG|JPEG|BMP){1}/';
		
		preg_match_all($regEx,$data['content'],$images,PREG_SET_ORDER);
		
		foreach($images as $index => $image) {
			
			$filePath = 'upload/'.date('Y-m');
			if(!is_dir($filePath)) 
				mkdir($filePath,0777);
			
			$fileType = $image[3];
			$fileName = $filePath.'/'.$data['uid'].'-'.$index.'.'.$fileType;
			
			$content = @file_get_contents($image[0]);
			$result = @file_put_contents($fileName, $content);
			
			if($result)  $data['content'] = str_replace($image[0], url($fileName),$data['content']);
		}
	}
}
