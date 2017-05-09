<?php
use Think\Controller;
class IndexController extends PublicController {
	public $userId;
    public $configInfo;//网站配置信息
    public $about;//关于我们
	public function _initialize(){
	    parent::_initialize();
	    
	}
	
	
    //首页
	public function index(){ 

		$this->display();
	}
	
	//产品列表
	public function product(){    
	    $con = [
	        'node_id' => (int)$_GET['cate_id']?$_GET['cate_id']:3,
	        'type' => d('tdk')->typeArr['contentCate']
	    ];
	    $this->tdkList($con);
	    
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
	   $this->tdkDetail();
	    
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
	    $con = [
	        'node_id' => (int)$_GET['cate_id']?$_GET['cate_id']:1,
	        'type' => d('tdk')->typeArr['contentCate']
	    ];
	    $this->tdkList($con);
	    
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
        $this->tdkDetail();
	    $id = $_GET['id'];
	    $row = d('content')->getInfo($id);
	    $hotList = d('content')->getList(['cate_id'=>'1'], 5, 'click desc');
	    $data = d('comment')->getPageList(['node_id'=>$id, 'type'=>'news'], '*', 'add_time desc', 2);
	    $this->assign('commentList', $data['list']);
	    $this->assign('pageVar', $data['pageVar']);
	    $this->assign('hotList', $hotList);
	    $this->assign('row', $row);
	    $this->assign('user', $this->user);
	    $this->display('newsDetail');
	}
	
	//用户评论
	public function comment(){
        $data = ['node_id' => $_POST['node_id'],
                 'type'    => $_POST['type'], 
                 'content' => $_POST['content'],
                 'user_id' => $this->user['id']
        ]; 
        $id = d('comment')->edit($data);
	    if(!$id)
	        return ajaxreturn(1, '评论失败');
	    return ajaxreturn(0,'评论成功');
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
	    $con = [
	        'node_id' => (int)$_GET['cate_id']?$_GET['cate_id']:4,
	        'type' => d('tdk')->typeArr['contentCate']
	    ];
	    $this->tdkList($con);
	    
	    $CateChildren = d('contentCate')->getList(['pid'=>'4']);//产品子类信息
	    
	    foreach ($CateChildren as $k=>$v){
	        $cateIdArr[] = $v['id'];
	    }

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
	    if($_GET['cate_id'] && !empty($_GET)){
	        $contentInfo = d('content')->where($_GET)->find();
	    }else{
	        $contentInfo = d('content')->where(['cate_id'=>'18'])->find();
	    }

	    $CateChildren = d('contentCate')->getList(['pid'=>'5']);//产品子类信息
        $this->assign('ChildCateList',$CateChildren);//分类信息
        $this->assign('info',$contentInfo);//内容信息
        $this->display();
	
	}
	
	//支付结果通知
	public function payNotify(){
		d('order')->payNotify($_POST);
	}

	
		
	
	// //获取手机验证码
	// public function getVercode(){
	//     $mobile = $_POST['mobile'];
	//     $Vercode = d('user')->getVercode($mobile);
	//     ajaxReturn('获取验证码失败','',['list'=>$Vercode]);
	  
	// }
	// //密码重置
	// public function passReset(){
	    
	//     $id = d('user')->passReset($_POST);
	    
	//     return $id;
	// }
	
	
}