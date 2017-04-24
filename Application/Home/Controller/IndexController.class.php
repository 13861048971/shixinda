<?php
use Think\Controller;
class IndexController extends PublicController {
	public $userId;
    public $configInfo;//网站配置信息
    public $about;//关于我们
	public function _initialize(){
	    parent::_initialize();
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
	    $this->assign('user',session('user'));
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
	    $CateChildren = d('contentCate')->getList(['pid'=>3]);//产品子类信息
	    $cateIdArr = [0];
	    foreach ($CateChildren as $k=>$v){
	        $cateIdArr[] = $v['id'];
	    }
	    $con = ['cate_id'=>['in', $cateIdArr]];
	    if($pid = (int)$_GET['cate_id'])
	        $con = ['cate_id' => $pid];
	    
	    $productList = d('content')->getPageList($con,'','',6);//产品列表页
	        
	    $this->assign('ChildCateList',$CateChildren);
	    $this->assign('productList',$productList['list']);
        $this->assign('list',$productList);
	    $this->display();
	}
	
	//产品详情
	public function productDetail(){
	    $CateChildren = d('contentCate')->getList(['pid'=>3]);//产品分类信息
	    $productInfo = d('content')->getInfo($_GET['id']);
	    $product = d('content')->where(['pid'=>3])->select();
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
	
	//新闻
	public function news(){
	    $data = d('content')->getPageList(['cate_id'=>'1'], '', 'add_time desc', 2);
	    $hotList = d('content')->getList(['cate_id'=>'1'], 5, 'click desc'); 
	    $list = $data['list'];
	    foreach($list as $k=>$v){
	        $list[$k]['content'] =  mb_substr(strip_tags($v['content']), 0, 50);
	    }
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('list', $list);
	    $this->assign('hotList', $hotList);
	    $this->display();
	}
	
	//新闻详情
	public function newsDetail(){
	    $id = $_GET['id'];
	    $row = d('content')->getInfo($id);
	    $hotList = d('content')->getList(['cate_id'=>'1'], 5, 'click desc');
	    $data = d('comment')->getPageList(['node_id'=>$id, 'type'=>'news']);
	    $this->assign('commentList', $data['list']);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('hotList', $hotList);
	    $this->assign('row',$row);
	    $this->display();
	}
	
	//用户评论
	public function comment(){
       
	   
	}
	
	//服务项目
	public function services(){
	   
	    $this->display();
	}
	//取id集合
	public function catePid($id){
	    $cateinfo = d('contentCate')->where(['id'=>$id])->find();
	    if($cateinfo['pid']!=0){
	       $pid = $this->catePid($id);
	    }else{
	        return $cateinfo['id'];
	    }
	}
	//案例
	public function cases(){
	    $CateChildren = d('contentCate')->getList(['id'=>'4']);//产品子类信息
	    
	    foreach ($CateChildren as $k=>$v){
	        $cateIdArr[] = $v['id'];
	    }
	    //var_dump($cateIdArr);exit;
        $con = ['cate_id'=>['in', $cateIdArr]];
	    if($pid = (int)$_GET['cate_id'])
	        $con = ['cate_id' => $pid];
	    
        $caseList = d('content')->getPageList($con,'','',6);//产品列表页
	    $contentList = d('content')->select();
	    $this->assign('ChildCateList',$CateChildren);
	    $this->assign('caseList',$caseList['list']);
        $this->assign('list',$caseList);
	    $this->display();
	}

	//关于我们的列表
	public function about(){
        $this->display();
		
		
	}
	
	//支付结果通知
	public function payNotify(){
		d('order')->payNotify($_POST);
	}
	
	//关于我们配置信息
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
	
	//用户登录
	public function login(){
	    $mobile = $_POST['mobile'];
	    $pass = $_POST['password'];

        $user = d('user')->login($mobile, $pass);
        
        if($user)
            $this->success('登录成功',"index");
        $this->display();
	}
	
	//用户退出
	
	public function loginOut(){
	    session('user',null);
	    if(empty(session('user')))
	    $this->success('退出成功','login');
	}
	
	//用户注册
	public function register(){
	    $mobile = $_POST['mobile'];
	    $pass = $_POST['password'];
	    $vercode = $_POST['vcode'];
        $regist = d('user')->regist($mobile, $pass,$vercode);
         //var_dump($regist);exit;
        if($regist)
            $this->success('注册成功',"login");
        $this->display();
	}
	
	//获取手机验证码
	public function getVercode(){
	    $mobile = $_POST['mobile'];
	    $Vercode = d('user')->getVercode($mobile);
	    ajaxReturn('获取验证码失败','',['list'=>$Vercode]);
	  
	}
	//密码重置
	public function passReset(){
	    
	    $id = d('user')->passReset($_POST);
	    
	    return $id;
	}
		
	
}