<?php
use Think\Controller;

/**
 * 内容管理
 *
 */
class ContentController extends PublicController {
	public function _initialize(){
		parent::_initialize();
	}
	
    public function index(){
        $list = d('content')->getList();
        $this->assign('list',$list);
        $this->display('Content','list');
    }
	
	
}