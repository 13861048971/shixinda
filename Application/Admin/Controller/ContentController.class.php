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
            ['name' => '添加内容', 'url' => u('contentEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
        ];
        $this->setRightAction($rightBtn);
        $cateId = d('contentCate')->where(['name'=>$_GET['cateName']])->getField('id');
        $data = d('content')->getPageList($_GET);
        $this->assign($data); 
        $this->assign('contentTitle',$_GET['title']);
        $this->display('content','list');
    }
	
    //内容编辑
	public function contentEdit(){
	    $this->ajaxEdit('content', null, function($row, $mod){
	    $list = d('contentCate')->getList(['pid'=>'0'], $limit=50);
	    $url = "/admin/content/contentCateChildren/pid/";
        $selectMuti = [
            'list'      => $list,
            'url'       => $url,
            'name'      => 'cate_id',
        ];
        if(!$row){
            $row['publish_time'] = time();
        }
        
        $this->assign('row',$row);
	    $this->assign('selectMuti',$selectMuti);
	    $this->assign('qiNiuYunImgName',c('QINIUYUN.imgName'));
	    });
	}
	 
	//内容删除
	public function contentDel(){
	    $this->ajaxDel('content');
	}
	
	//内容分类
	public function contentCate(){
	    $rightBtn = [
	        ['name' => '添加分类','url'=>u('contentCateEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
        $data = d('contentCate')->getPageList(['p'=>$_GET['p'],'name'=>$_GET['name'],'pid'=>'0']);
        $this->assign($data);
        $this->assign('cateName',$_GET['name']);
        $this->assign('list',$data['list']);
        $this->display('contentCate','list');     
	}
	
	//内容分类获取子类
	public function contentCateChildren($pid){
	    $list = d('contentCate')->getList(['pid'=>$pid]);
	    ajaxReturn(0,'子类获取成功',['list'=>$list]);
    }
	
	//分类编辑
	public function contentCateEdit(){
	    $this->ajaxEdit('contentCate', null, function($row, $mod){
	        $this->assign('row',$row);
	    });
	}
	
	//添加内容子类
	public function contentCateChildrenEdit(){
	    $this->ajaxEdit('contentCate', null, function($row, $mod){
	        $name = $_GET['name'];
	        $id = $_GET['id'];
	        $data = ['name'=>$name,'id'=>$id];
	        //dump($data);exit();
	        $this->assign('data',$data);
	    });
	}
	
	//分类删除
	public function contentCateDel(){
	    $this->ajaxDel('contentCate');
	}
	
	//友情链接
	public function friendLink(){
	    $rightBtn = [
	        ['name' => '添加新连接', 'url'=> u('friendLinkEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
	    $data = d('friendLink')->getPageList($_GET, '*', 'add_time desc', 15);
	    $this->assign($data);
	    $this->assign('linkName',$_GET['name']);
	    $this->display('friendLink','list');
	}
	
	//链接编辑
	public function friendLinkEdit(){
	    $this->ajaxEdit('friendLink', null, function($row, $mod){
	        $this->assign('qiNiuYunImgName',c('QINIUYUN.imgName'));
	    });
	}
	
	//链接删除
	public function friendLinkDel(){
	    $this->ajaxDel('friendLink');
	}
	
	//导航管理
	public function navigation(){
	    $rightBtn = [
	        ['name' => '添加新导航','url'=> u('navigationEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
	    ];
	    $this->setRightAction($rightBtn);
	    $data = d('navigation')->getPageList(['p'=>$_GET['p'],'name'=>$_GET['name'],'pid'=>'0'],null);
	    
	    $this->assign($data);
	    $this->assign('navigationName',$_GET['name']);
	    $this->display('navigation','list');
	}
	
	//导航管理子类获取
	public function navigationChildren($pid){
	    $data = d('navigation')->getPageList(['pid'=>$pid]);
	    $this->assign($data);
	    $list = $data['list'];
	    ajaxReturn(0,'子类获取成功',['list'=>$list]);
	}
	
	//导航管理编辑
	public function navigationEdit(){
	    $this->ajaxEdit('navigation', null, function($row, $mod){
	       $this->assign('qiNiuYunImgName',c('QINIUYUN.imgName'));    
	    });
	}
	
	//添加导航子类
	public function navigationChildrenEdit(){
	    $this->ajaxEdit('navigation', null, function($row, $mod){
	        $name = $_GET['name'];
	        $id = $_GET['pid'];
	        $parent = ['name'=>$name,'id'=>$id];
	        $this->assign('parent',$parent);
	    });	        
	}
	
	//导航管理删除
	public function navigationDel(){
	    $this->ajaxDel('navigation');
	}
	
	//区块管理
	public function block(){
	    $rightBtn = [
	        ['name'      => '添加新区块', 'dialog' => 1,
	         'dialog-lg' => 1, 'list' => [['name'=>'轮播图区块', 'url'=> u('blockEdit', ['type'=>1])]]
	        ]
	    ];
	    $this->setRightAction($rightBtn);
	    $data = d('block')->getPageList($_GET, '*', 'add_time desc', '15');
	    $this->assign('list', $data['list']);
	    $this->assign($data);
	    $this->assign('blockTitle', $_GET['title']);
	    $this->display();
	}
	
	//区块管理编辑
	public function blockEdit(){
	    if($_GET['type'] == 1){
	        $template = 'blockEdit1';
	    }
	    $this->ajaxEdit('block', $template, function($row, $mod){
	        $this->assign('qiNiuYunImgName',c('QINIUYUN.imgName'));
	    });
	}
	
	//区块管理删除
	public function blockDel(){
	    $this->ajaxDel('block');
	}
}