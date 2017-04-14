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
        $data = d('content')->getPageList($_GET);
        $this->assign($data); 
        $this->assign('contentTitle',$_GET['title']);
        $this->display('content','list');
    }
	
    //内容编辑
	public function contentEdit(){
	    $this->ajaxEdit('content', null, function($row, $mod){
	    });
	}
	 
	//内容删除
	public function contentDel(){
	    $this->ajaxDel('content');
	}
	
	//内容分类
	public function contentCate(){
	    $rightBtn = [
	        ['name' => '添加内容','url'=>u('contentCateEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
        $data = d('contentCate')->getPageList($_GET);
        $this->assign($data);
        $this->assign('cateName',$_GET['name']);
        $this->display('contentCate','list');
	}
	
	//分类编辑
	public function contentCateEdit(){
	    $this->ajaxEdit('contentCate', null, function($row, $mod){
	        
	    });
	}
	//添加子类
	public function addChildren(){
	    $this->ajaxEdit('contentCate', null, function($row, $mod){
	        $pname = $_GET['pname'];
	        $pid = $_GET['pid'];
	        $data = ['name'=>$pname,'id'=>$pid];
	        //dump($data);exit();
	        $this->assign('data',$data);
	        
	    });
	}
	//分类删除
	public function contentCateDel(){
	    $this->ajaxDel('contentCate');
	}
	
	//友情连接
	public function friendLink(){
	    $rightBtn = [
	        ['name' => '添加内容','url'=>u('friendLinkEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
	    $data = d('friendLink')->getPageList($_GET);
	    $this->assign($data);
	    $this->assign('linkName',$_GET['name']);
	    $this->display('friendLink','list');
	}
	
	//连接编辑
	public function friendLinkEdit(){
	    $this->ajaxEdit('friendLink', null, function($row, $mod){
	    });
	}
	
	//连接删除
	public function friendLinkDel(){
	    $this->ajaxDel('friendLink');
	}
	
	//导航管理
	public function navigation(){
	    $rightBtn = [
	        ['name' => '添加内容','url'=>u('navigationEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
	    $data = d('navigation')->getPageList($_GET);
	    $this->assign($data);
	    $this->assign('navigationName',$_GET['name']);
	    $this->display('navigation','list');
	}
	
	//导航管理编辑
	public function navigationEdit(){
	    $this->ajaxEdit('navigation', null, function($row, $mod){
	    });
	}
	
	//导航管理删除
	public function navigationDel(){
	    $this->ajaxDel('navigation');
	}
}