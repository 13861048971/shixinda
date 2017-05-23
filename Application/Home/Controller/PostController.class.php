<?php
use Think\Controller;
class PostController extends PublicController {
    
    public function _initialize(){
        parent::_initialize();  
    }
    //获取帖子分类
	public function index(){
	    $hotList = d('post')->getPostList([], 7, 'click desc');
	    $newList = d('post')->getPostList([], 7, 'add_time desc');
        $list = d('postCate')->getList(['status'=>'1'],100);
        
        foreach ($list as $k=>$v){
            if($v['pid'] == 0 ){
                $list1[] = $v;
                unset($list[$k]);
            } 
        }
        foreach ($list as $k1=>$v1){
            foreach($list1 as $k2=>$v2){
                if($v1['pid'] == $v2['id']){
                    $list1[$k2]['list'][] = $v1;
                    unset($list[$k1]);
                }         
            }    
        }
        foreach ($list as $k=>$v){
            foreach ($list1 as $k1=>$v1){
                foreach ($v1['list'] as $k2=>$v2){
                    if($v['pid'] != $v2['id']) continue;
                    $list1[$k1]['list'][$k2]['list'][] = $v;
                }  
            }
        }
           
        foreach ($list1 as $k1=>$v1){
            foreach ($list1[$k1]['list'] as $k2=>$v2){
                $idArr = [];
                foreach ($v2['list'] as $k3=>$v3){
                    $idArr[$k3] = $v3['id'];    
                }
                $idArr[] = $v2['id'];
                $con = [
                    'add_time'     => ['gt', strtotime(date("Y-m-d"))],
                    'post_cate_id' => ['in', $idArr]
                ];
                $list1[$k1]['list'][$k2]['todayPostNum'] = d('post')->where($con)->count();
                $list1[$k1]['list'][$k2]['mainPostNum'] = d('post')->where(['post_cate_id' => ['in', $idArr]])->count();
                $list1[$k1]['list'][$k2]['replyPostNum'] = d('post')->where(['post_cate_id' => ['in', $idArr]])->sum('comment_num');
                $list1[$k1]['list'][$k2]['postNum'] = $list1[$k1]['list'][$k2]['mainPostNum'] + $list1[$k1]['list'][$k2]['replyPostNum'];
            }
        } 
        $block = d('block')->getBlock('9');
        $this->assign('hotList', $hotList);
        $this->assign('block', $block);
        $this->assign('newList', $newList);
        $this->assign('list', $list1);
		$this->display();
	}
	 
	//获取帖子列表
	public function postList(){
	    $con = [
	        'node_id' => (int)$_GET['post_cate_id']?$_GET['post_cate_id']:0,
	        'type' => d('tdk')->typeArr['contentCate']
	    ];
	    $this->tdkList($con);
	    
	    //商户交流的帖子列表
	    $customList = d('post')->getPostList([], 6, 'add_time desc');
	    
	    $post_cate_id2 = $_GET['post_cate_id2'];
	    $post_cate_id3 = $_GET['post_cate_id3'];
	    
	    //三级分类列表
	    $childrenList = d('postCate')->getList(['pid'=>$post_cate_id2, 'status'=>1]);
	 
	    if($childrenList){
	        foreach($childrenList as $k=>$v){
	            $childrenList[$k]['count'] = d('post')->where(['post_cate_id'=>$v['id']])->count();
	            $idArr[$k] = $v['id'];
	        }
	        
	        //二级分类名称
	        $name = d('postCate')->where(['id'=>$post_cate_id2])->getField('name');
	        
	        $where['post_cate_id'] = array('in', $idArr);
	        $map['post_cate_id'] = array('eq', $post_cate_id2);
	        $map['_complex'] = $where;
	        $map['_logic'] = 'or';
	        if($post_cate_id2 && $post_cate_id3){
	            $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id3, 'status'=>'1'], '*', 'add_time desc', 15);
	        }elseif($post_cate_id2){
	            $data = d('post')->getPageList([$map, 'status'=>'1'], '*', 'add_time desc');
	        }
	    }
	    else{
	        $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id2, 'status'=>'1'], '*', 'add_time desc', 15);
	    } 

	    $this->assign('customList', $customList);
	    $this->assign('todayPostNum', $_GET['todayPostNum']);
	    $this->assign('mainPostNum', $_GET['mainPostNum']);
	    $this->assign('childrenList', $childrenList);
	    $this->assign('name', $name);
	    $this->assign('post_cate_id2', $post_cate_id2);
	    $this->assign('post_cate_id3', $post_cate_id3);
	    $this->assign('list', $data['list']);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->display('postList');
	}
	
	//获取帖子详情
	public function postDetail(){
	    
	    if($_GET['msg_id'])
	        d('userMsg')->read($_GET['msg_id'], $this->user['id'], $read = true);
	        
	    $con = [
	        'node_id' => (int)$_GET['id'],
	        'type' => d('tdk')->typeArr['post']
	    ];
	    $this->tdkList($con);
// 	    $tdkInfo = d('tdk')->tdkInfo($con);
	    
// 	    if($tdkInfo)
// 	    $this->setTdk($tdkInfo['title'], $tdkInfo['keywords'], $tdkInfo['description']);

	    //商户交流的帖子列表
	    $customList = d('post')->getPostList([], 6, 'add_time desc');
	    
	    $id = $_GET['id'];
	    $p = $_GET['p'];
	    //判断帖子是二级分类还是三级分类
	    $post_cate_id = d('post')->where(['id'=>$id])->getField('post_cate_id');
	    $post_cate_pid = d('postCate')->where(['id'=>$post_cate_id])->getField('pid');
	    $post_cate_ppid = d('postCate')->where(['id'=>$post_cate_pid])->getField('pid');
	    
	    $idArr = ['post_cate_id2'=>$post_cate_id];
	    if($post_cate_ppid > 0)
	        $idArr = ['post_cate_id2'=>$post_cate_pid, 'post_cate_id3'=>$post_cate_id];

	    $postRow = d('post')->getPost($id);//帖子信息
	    $this->click('post',$id);//访问量+1
	    $con = ['post_id'=>$id];
	    if($_GET['viewHost']){ 
	       $con['user_id'] = $postRow['user_id'];
	    }
	    $data = d('postComment')->getPageList($con, '*', 'add_time', 15);//帖子评论信息
	    //评论的id数组
	    $idArr1 = getIdArr($data['list']);
	    if($idArr1){
	        //点赞的类型值
	        $type = d('support')->typeArr['postComment'];
	        //评论点赞状态列表
	        $isSupportList = d('support')->where(['node_id'=>['in', $idArr1], 'type'=>$type])->select(false);
	        //评论点赞数列表
	        $supportNumList = d('support')->getNumArr($idArr1, $type, 1);
	        //评论踩数列表
	        $notSupportNumList = d('support')->getNumArr($idArr1, $type, 0);
	        //举报状态列表
	        $isReportList = d('report')->where(['node_id'=>['in', $idArr1], 'user_id'=>$this->user['id']])->field('id,node_id')->select();
	        //举报数列表
	        $reportNumList = d('report')->getNumArr($idArr1);
	    }

	    //帖子评论信息的赞和踩状态
	    foreach ($data['list'] as $k=>$v){ 
	       foreach ($isSupportList as $k1=>$v1){
	           if($v1['node_id'] == $v['id']){
	               $data['list'][$k]['isSupport'] = $v1['support'];
	           }
           }
           $data['list'][$k]['supportNum'] = 0;
	       foreach ($supportNumList as $k1=>$v1){
	           if($v1['node_id'] == $v['id']){
	               $data['list'][$k]['supportNum'] = $v1['num'];
	           }
	       } 
	       $data['list'][$k]['notSupportNum'] = 0;
	       foreach ($notSupportNumList as $k1=>$v1){
	           if($v1['node_id'] == $v['id']){
	               $data['list'][$k]['notSupportNum'] = $v1['num'];
	           }
	       }
	       foreach ($isReportList as $k1=>$v1){
	           if($v1['node_id'] == $v['id']){
	               $data['list'][$k]['isReport'] = $v1['id'];
	           }
	       }
	       $data['list'][$k]['reportNum'] = 0;
	       foreach ($reportNumList as $k1=>$v1){
	           if($v1['node_id'] == $v['id']){
	               $data['list'][$k]['reportNum'] = $v1['num'];
	           }
	       }
	    }
	    
	    $postRow['collectNum'] = d('collect')->getNum($id, d('collect')->typeArr['post'], '');//收藏数
	    //判断收藏状态
	    $postRow['isCollect'] = d('collect')->where(['node_id'=>$id, 'user_id'=>$this->user['id']])->getField('id');
	    $postRow['supportNum'] = d('support')->getNum($id, d('support')->typeArr['post'], 1);//点赞数
	    $postRow['notSupportNum'] = d('support')->getNum($id, d('support')->typeArr['post'], 0);//踩数
	    //判断点赞状态
	    $postRow['isSupport'] = d('support')->where(['node_id'=>$id, 'user_id'=>$this->user['id']])->getField('support');
	    //判断举报状态
	    $postRow['isReport'] = d('report')->where(['node_id'=>$id, 'user_id'=>$this->user['id']])->getField('id');
	    $postRow['reportNum'] = d('report')->getNum(['node_id'=>$id]);//举报数

	    $this->assign('p', $p);
	    $this->assign('customList', $customList);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('page', $data['page']);
	    $this->assign('proNum', $data['proNum']);
	    $this->assign('viewHost', $_GET['viewHost']);
	    $this->assign('idArr', $idArr);
	    //$this->assign('userRow', $userRow);
	    $this->assign('list', $data['list']);
	    $this->assign('postRow', $postRow);
	    $this->assign('user', $this->user);
	    $this->display('postDetail');
	}
	
    //帖子评论
    public function comment(){
        $data = [
            'user_id' => $this->user['id'],
            'post_id' => $_POST['post_id'],
            'content' => $_POST['content']
        ];
        
        $id = d('postComment')->edit($data);
        
        $messageData = [
            'from_user_id' => $data['user_id'],
            'node_id' => $id,
            'type' => d('userMsg')->typeArr['评论信息'],
            'user_id' => d('post')->where(['id'=>$data['post_id']])->getfield('user_id'),
        ];
        if(!d('userMsg')->edit($messageData)){
            ajaxReturn(1, d('userMsg')->getError());
        };
        
        if(!$id){
            ajaxReturn(1, '评论失败',['id'=>$id]);
        } 
        
        ajaxReturn(0, '评论成功', ['id'=>$id]);
    }
    
    //帖子收藏
    public function postCollect(){
        if(!$this->user['id'])
           return ajaxReturn2(1,'请先登录');
        d(collect)->collect('post');
    }
     
    //帖子点赞或者踩
    public function postSupport(){
        if(!$this->user['id'])
            return ajaxReturn2(1,'请先登录');
        $support = d('support')->isSupport('post');
    }
    
    //帖子回复点赞或者踩
    public function postCommentSupport(){
        $data = [
            'user_id' => $this->user['id'],
            'post_id' => $_GET['post_id'],
            'id' => $_GET['id']
        ];

        if(!$this->user['id'])
            return ajaxReturn2(1,'请先登录');
            d('support')->isSupport('postComment');
    }
    
    //帖子举报
    public function postReport(){
        if(!$this->user['id'])
            return ajaxReturn2(1,'请先登录');
        if($_POST && !empty($_POST)){
            $data = [
                'user_id' => $this->user['id'],
                'type' => d('report')->typeArr['post'],
                'node_id' =>$_POST['post_id'],
                'content' =>$_POST['content'],
                'status' =>1
            ];
          
            $id = d('report')->edit($data);
            if($id)
                return ajaxReturn2(0,'举报成功');
                return ajaxReturn2(1,d('report')->getError());
        }
        
    }
    
    //别人用户信息
    function personInfo(){
        $personInfo = d('user')->getPerson($_GET['userId']);//用户信息
        $this->assign('person',$personInfo);
        if($_GET['act'] == 'original' ){
            $data = d('post')->getPageList(['user_id'=>$_GET['userId']],'*','id desc');//用户主贴
            $this->assign('postList',$data['list']);
            return $this->display();
        }
        if($_GET['act'] == 'reply'){
            $repaly = d('postComment')->getPageList(['user_id'=>$_GET['userId']],'*');//回帖信息
            $this->assign('personReplay',$repaly['list']);
           return $this->display();
        }
        
        $this->display();
    }
    
    //用户回复
    function personReplay(){
        $reply_id = $_POST['reply_id'];   
        $row = d('postComment')->where(['id'=>$reply_id])->find();
        $data = [
            'user_id'  => $this->user['id'],
            'post_id'  => $row['post_id'], 
            'content'  => $_POST['content'],
            'reply_id' => $reply_id
        ];
        
        
        $id = d('postComment')->edit($data);
        $messageData = [
            'from_user_id' => $data['user_id'],
            'node_id' => $id,
            'type' => d('userMsg')->typeArr['回复信息'],
            'from_user_name' => $this->user['nickname'],
            'user_id' => d('postComment')->where(['id'=>$data['reply_id']])->getfield('user_id'),
        ];
        d('userMsg')->edit($messageData);
        
        
        if(!$id)
            ajaxReturn(1, '回复失败', ['id'=>$id]);
        $reply = d('postComment')->getInfo($id);
        ajaxReturn(0, '回复成功', ['reply'=>$reply]); 
    }
    
}