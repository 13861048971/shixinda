<?php
use Think\Controller;
class PostController extends PublicController {
    //获取帖子分类
	public function index(){ 
	    $hotList = d('post')->getList([], 7, 'click desc');
	    $newList = d('post')->getList([], 7, 'add_time desc');
        $list = d('postCate')->getList(['pid'=>'0', 'status'=>'1']);
        foreach($list as $k1=>$v1){
            $list[$k1]['list'] = d('postCate')->getList(['pid'=>$list[$k1]['id'], 'status'=>'1']);
            foreach($list[$k1]['list'] as $k2=>$v2){
                //dump($v2['id']);exit();
                $id = d('postCate')->where(['pid'=>$v2['id']])->getField('id');
                //dump($id);exit();
                $idArr = [$v2['id'], $id];
                $con = [
                        'add_time'     => ['gt', strtotime(date("Y-m-d"))],
                        'post_cate_id' => ['in', $idArr] 
                       ];
                $list[$k1]['list'][$k2]['todayPostNum'] = d('post')->where($con)->count();
                $list[$k1]['list'][$k2]['mainPostNum'] = d('post')->where(['post_cate_id' => ['in', $idArr]])->count();
            }
        } 
        $this->assign('hotList', $hotList);
        $this->assign('newList', $newList);
        $this->assign('list', $list);
		$this->display();
	}
	 
	//获取帖子列表
	public function postList(){
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
	        if($post_cate_id2&&$post_cate_id3){
	            $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id3, 'status'=>'1'], '*', 'add_time desc', 3);
	        }elseif($post_cate_id2){
	            $data = d('post')->getPageList([$map, 'status'=>'1'], '*', 'add_time desc', 3);
	        }
	        $this->assign('childrenList', $childrenList);
	    }
	    else{
	        $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id2, 'status'=>'1'], '*', 'add_time desc', 3);
	    }
	    
	    
	    $this->assign('name', $name);
	    $this->assign('post_cate_id2', $post_cate_id2);
	    $this->assign('post_cate_id3', $post_cate_id3);
	    $this->assign('list', $data['list']);
	    $this->assign('user', $this->user);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->display();
	}
	
	//获取帖子详情
	public function postDetail(){
	    $id = $_GET['id'];
	    $userId = d('post')->where(['id'=>$id])->getField('user_id');
	    $userRow = d('user')->where(['id'=>$userId])->find();
	    $postRow = d('post')->getInfo($id);
	    $this->click('post',$id);//访问量+1
	    $data = d('post_comment')->getPageList(['post_id'=>$id], '*', 'add_time desc', 5);
	    foreach($data['list'] as $k=>$v){
	        //$data['list'][$k]['userName'] = d('user')->where(['id'=>$v['user_id']])->getField('nickname');
	        $data['list'][$k]['avatar'] = d('user')->where(['id'=>$v['user_id']])->getField('avatar');
	    } 
	    //dump($data['list']);exit();
	    $this->assign('userRow', $userRow);
	    $this->assign('list', $data['list']);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('postRow', $postRow);
	    $this->display();
	}
	
	
}