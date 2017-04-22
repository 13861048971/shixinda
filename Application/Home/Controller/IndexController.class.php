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
	    $productList = d('content')->getPageList($_GET);//产品列表页
	    $CateChildren = d('contentCate')->getList(['pid'=>3]);//产品子类信息
	    $productCateList = d('content')->getPageList(['cate_id'=>$_GET['cate_id']]);//产品根据分类获取列表
	    $this->assign('ChildCateList',$CateChildren);
	    $this->assign('productList',$productList['list']);
        $this->assign('list',$productList);
	    $this->display();
	}
	
	//产品详情
	public function productEdit(){
	    $info = d('content')->getInfo($_GET['id']);
	    $this->assign('info',$info);
	    $this->display();
	}
	
	//新闻
	public function news(){
	    $data = d('admin/content')->getPageList(['cate_id'=>'1'], '', 'add_time desc', 2);
	    $hotList = d('admin/content')->getList(['cate_id'=>'1'], 5, 'click desc'); 
	    $list = $data['list'];
	    foreach($list as $k=>$v){
	        $list[$k]['content'] =  mb_substr(strip_tags($v['content']), 0, 50);
	    }
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('list', $list);
	    $this->assign('hotList', $hotList);
	    $this->display('news');
	}
	
	//新闻详情
	public function newsDetail(){
	    $id = $_GET['id'];
	    $row = d('admin/content')->getInfo($id);
	    $hotList = d('admin/content')->getList(['cate_id'=>'1'], 5, 'click desc');
	    $this->assign('hotList', $hotList);
	    //dump($row);exit();
	    $this->assign('row',$row);
	    $this->display('newsDetail');
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
	public function about(){
		$info = d('config')->getInfo('about');
		
		$desc = strip_tags($info['value']['content']);
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//使用协议
	public function agreement(){
		$info = d('config')->getInfo('agreement');
		
		$desc = strip_tags($info['value']['content']);
		
		if($_GET['format'] == 'html'){
			$this->assign('desc', $desc);
			return $this->display();
		}
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//使用协议
	public function tradeNote(){
		$info = d('config')->getInfo('trade_note');
		
		$desc = strip_tags($info['value']['content']);
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//吐槽我们
	public function feedback(){
		$mod = d('feedback');
		
		$data = [
			'user_id'=>$this->user['id'], 
			'desc' => htmlentities($_POST['desc']),
		];
		
		if(!$mod->edit($data))
			ajaxReturn2(1, $mod->getError());
		
		ajaxReturn2(0,'操作成功!');
	}

	//任务分享页面
	public function taskShare(){
		if( !($id = $_GET['id']) || !($row = d('task')->getInfo($id)) )
			return ajaxReturn2(1, '任务不存在');
		$client = $_GET['client'];
		$row['joinList'] = d('join')->getList(['task_id'=>$id],4,'id desc');
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}
	//套餐分享页面
	public function mealShare(){
		if( !($id = $_GET['id']) || !($row = d('meal')->getInfo($id)) )
			return ajaxReturn2(1, '套餐不存在');
		$client = $_GET['client'];
		
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}
	//摄影师分享页面
	public function phoShare(){
		if( !($id = $_GET['id']) || !($row = d('pho')->getInfo($id)) )
			return ajaxReturn2(1, '任务不存在');
		$client = $_GET['client'];
		
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$row['mealList'] = d('meal')->getList(['pho_id'=>$id]);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}

	//支付结果通知
	public function payNotify(){
		d('order')->payNotify($_POST);
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