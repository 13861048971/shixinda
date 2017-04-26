<?php
use Think\Controller;
class PostController extends PublicController {
    //获取帖子分类
	public function index(){ 
        $list = d('postCate')->getList(['pid'=>'0', 'status'=>'1']);
        foreach($list as $k1=>$v1){
            $list[$k1]['list'] = d('postCate')->getList(['pid'=>$list[$k1]['id'], 'status'=>'1']);
            foreach($list[$k1]['list'] as $k2=>$v2){
                $con = [
                        'add_time'     => ['gt', strtotime(date("Y-m-d"))],
                        'post_cate_id' => $v2['id']
                       ];
                $list[$k1]['list'][$k2]['todayPostNum'] = d('post')->where($con)->count();
                $list[$k1]['list'][$k2]['mainPostNum'] = d('post')->where(['post_cate_id' => $v2['id']])->count();
            }
        } 
        $this->assign('list', $list);
		$this->display();
	}
	 
	//获取帖子列表
	public function postList(){
	    $post_cate_id = $_GET['id'];
	    $name = d('postCate')->where(['id'=>$post_cate_id])->getField('name');
	    $data = d('post')->getPageList(['post_cate_id'=>$post_cate_id, 'status'=>'1'], '*', 'update_time desc', 1);
	    $this->assign('name', $name);
	    $this->assign('list', $data['list']);
	    $this->assign('user', $this->user);
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