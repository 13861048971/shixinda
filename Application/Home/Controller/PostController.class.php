<?php
use Think\Controller;
class PostController extends PublicController {
    //获取帖子分类
	public function index(){ 
        $list = d('postCate')->getList(['pid'=>'0', 'status'=>'1']);
        foreach($list as $k=>$v){
            $list[$k]['list'] = d('postCate')->getList(['pid'=>$list[$k]['id'], 'status'=>'1']);
        } 
        $this->assign('list', $list);
		$this->display();
	}
	 
	//获取帖子列表
	public function postList(){
	    $post_cate_id = $_GET['id'];
	    $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id, 'status'=>'1'], '*', 'update_time desc', 1);
	    $this->assign('list', $data['list']);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->display();
	}
	
	//获取帖子详情
	public function postDetail(){
	    $id = $_GET['id'];
	    $row = d('post')->getInfo($id);
	    $this->assign('row', $row);
	    $this->display();
	}
	
	
}