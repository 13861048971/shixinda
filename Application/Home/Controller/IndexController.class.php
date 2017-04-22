<?php
use Think\Controller;
class IndexController extends PublicController {
	public $userId;
    public $configInfo;//网站配置信息
    public $about;//关于我们
	public function _initialize(){
	    $this->configInfo = $this->config();
	    $this->about = $this->aboutOur();
	    $navigation = d("navigation")->where(['pid'=>['eq',6]])->order('rank ')->select();
	    $childNavigation = d("navigation")->where(['pid'=>['neq',0]])->order('rank desc')->select();
        $uri = $_SERVER['REQUEST_URI'];
	    foreach ($navigation as $k=>$v){
	        if(strpos(strtolower($uri), $v['url']) !== false){
	            if(strtolower($uri) != '/' && $v['url'] != '/')
	                $navigation[$k]['current'] = true;
	            
                if(strtolower($uri) == $v['url'] )
                    $navigation[$k]['current'] = true;
	        }
	               
	        foreach ($childNavigation as $k2=>$v2) {
	            if($v['id'] == $v2['pid']){
	                $navigation[$k]['list'][] = $v2;       
	            }
	        }
	    }
	    $this->assign('navigation',$navigation);
	    $this->assign('aboutOur',$this->about);
	    $this->assign('config',$this->configInfo);
	}
	
	
    //首页
	public function index(){ 
        
		$this->display();
	}
	
	//产品列表
	public function product(){
	    $productList = d('content')->getPageList($_GET,'','',6);//产品列表页
 	    //var_dump($productList);exit();
	    $CateChildren = d('contentCate')->getList(['pid'=>3]);//产品子类信息
	    $this->assign('ChildCateList',$CateChildren);
	    $this->assign('productList',$productList['list']);
        $this->assign('list',$productList);
	    $this->display();
	}
	
	//产品详情
	public function productDetail(){
	    $CateChildren = d('contentCate')->getList(['pid'=>3]);//产品分类信息
	    $productInfo = d('content')->getInfo($_GET['id']);
	    $product = d('content')->select();
	    foreach ($CateChildren as $k=>$v){
	      foreach ($product as $k1=>$v1){
	          if($v1['cate_id'] == $v['id']){
	              $CateChildren[$k]['childInfo'][] = $v1;
	          }
	      }
	    }
	    $this->assign('productInfo',$productInfo);
	    $this->assign('ChildCateList',$CateChildren);
	    if(IS_AJAX)
	        return ajaxReturn(0,'',$productInfo);
	    $this->display();
	}
	
// 	//获取产品详情
// 	public function ajaxProductInfo($id){
// 	   $productInfo = d('content')->getInfo($id);
// 	    ajaxReturn('0','',$productInfo);
// 	}

	//新闻
	public function news(){
		/* $configInfo = $this->config();
        $about = $this->aboutOur();
        $this->assign('aboutOur',$about);
	    $this->assign('config',$configInfo);
	    $this->display(); */
	    $data = d('admin/content')->getPageList();
	    $hotList = d('admin/content')->getList([], 5, 'add_time desc'); 
	    $this->assign('list', $data['list']);
	    $this->assign('hotList', $hotList);
	    $this->display('news');
	}
	
	//新闻详情
	public function newsDetail(){
	    $data = d('admin/content')->getPageList();
	    $hotList = d('admin/content')->getList([], 5, 'add_time desc');
	    $this->assign('list', $data['list']);
	    $this->assign('hotList', $hotList);
	    $this->display('news');
	    
	    //TODO
	}
	
	
	//服务
	public function services(){
	   
	    $this->display();
	}
	
	//案例
	public function cases(){
	 
	    $this->display();
	}
	
	//关于我们
	public function aboutOur(){
	    $mod = d('config');
	    $info = $mod->getList();
	    $info = $info['about']['node'];
	    
	    return $info;
	}
	//网站配置信息
	public  function config(){
	    $mod = d('config');
	    $list = $mod->getList();
	    $list = $list['config']['node'];

	return $list;
	}
}