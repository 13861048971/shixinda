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
	
	//内容列表
    public function index(){
        $rightBtn = [
            ['name' => '添加内容','url'=>u('contentEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
        ];
        $this->setRightAction($rightBtn);
        $list = d('content')->getList();
        $this->assign('list',$list);
        $this->display('Content','list');
    }
	
    //内容编辑
	public function contentEdit(){
	    $this->ajaxEdit('content', null, function($row, $mod){
	        $this->assign('statusArr', $mod->statusArr);
	    });
	}
	
	//内容删除
	public function contentDel(){
	    $this->ajaxDel('content');
	}
}