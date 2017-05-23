<?php
use Think\Controller;
use Think\Verify;
class UserController extends PublicController{
	public $userId;
	
	function _initialize(){
		parent::_initialize();
		$this->userId = $this->user['id'];
	}
	//用户中心
	function index(){
	    $user = $this->user;
	    $genderList = [
	        [
	            'list' => d('user')->genderArr, 
	            'name' => 'gender',
	            'checked' => $user['gender']
	        ]
	    ];
	    //dump($user);exit();
	    $this->assign('genderList', $genderList);
	    $this->assign('qiNiuYunImgName',c('QINIUYUN.imgName'));
	    $this->display();
	}	
	
	//用户登录
	public function login(){
	    if(isset($_POST) && $_POST){
	
	        $mobile = $_POST['mobile'];
	        $pass = $_POST['password'];
	         
	        $user = d('user')->login($mobile, $pass);
	         
	        if($user)
	            ajaxReturn('0','登录成功',['list'=>$user]);
	             
	            if(!$user)
	                ajaxReturn('1','登录失败'.d('user')->getError());
	    }else{
	        $this->display();
	    }
	}
	
	//用户退出
	
	public function loginOut(){
	    $onlineTime = time()-$this->user['last_login'] + $this->user['online_time'];
	    $data = [
	        'id'=>$this->user['id'],
	        'last_logout'=>time(),
	        'online_time' =>$onlineTime,
	        
	    ];
	    $outTime = d('user')->data($data)->save();
	    if($outTime)
	       session('user',null);
	    if(empty(session('user')))
	        ajaxReturn(0,'退出成功');
	}
	
	//用户注册
	public function regist(){
	     
        if(isset($_POST) && $_POST){
            $mobile = $_POST['mobile'];
            $pass = $_POST['password'];
            $vercode = $_POST['vcode'];
            $regist = d('user')->regist($mobile, $pass,$vercode);
             
            if($regist)
                ajaxReturn('0','注册成功,请登录',['list'=>$regist]);
                 
                if(!$regist)
                    ajaxReturn('1','注册失败'.d('user')->getError());
        }
         
	    $act = $_REQUEST['act'];
	    $this->assign('act',$act);
	    $this->display();

	}
	
	//获取手机验证码
	public function getVercode(){
	    $mobile = $_POST['mobile'];
	    $Vercode = d('user')->getVercode($mobile);
	    if(!$Vercode)
	        ajaxReturn('1','获取验证码失败'.d('user')->getError());
	        ajaxReturn('0','已发送',['list'=>$Vercode]);
	         
	}
	//密码重置
	public function passReset(){
	    $id = d('user')->passReset($_POST);
	    if($id)
	        ajaxReturn('0','修改成功');
	        if(!$id)
	            ajaxReturn('1',d('user')->getError());
	}
	
	
	//用户帖子编辑
	function postEdit(){
	    
	    $id = $_GET['id'];
	    if(isset($_POST) && $_POST){
	        $_POST['user_id'] = $this->user['id'];
	        $_POST['status'] = 1;
	        $data = d('post')->edit($_POST,$id);
	        if($data)
	            ajaxReturn(0,'操作成功',['list'=>$data]);
	        if(!$data)
	            ajaxReturn(1,d('post')->getError());
	    }
	    $postInfo = d('post')->where(['id'=>$id])->find();
	    $this->assign('info',$postInfo);
	    $this->display();
	}
	
	//帖子删除
	public function postDel(){
	    $this->ajaxDel('post');
	}
	
	
	//用户帖子管理
	function postList(){
	  
	    $data = d('post')->getPageList(['user_id'=>$this->user['id']],null);
	    $this->assign('list',$data['list']);
	    $this->display();
	}
	
	//帖子子类显示
	public function postCateChildren(){
	    $con = $_GET;
	    $list = d('postCate')->getList($con);
	    ajaxReturn(0,'',array('list'=>$list));
	}
	
	//用户信息设置
	function userEdit(){
	    $id = $this->user['id'];
	    if($_POST && isset($_POST))
        $data = d('user')->edit($_POST, $id);
	    
	    if($data)
	       ajaxReturn(0,'修改成功,请刷新页面',['list'=>$data]);
        ajaxReturn(1,'修改失败'.d('user')->getError());
	}
	
	//用户密码修改
	function changePwd(){
	    $data = d('user')->changePwd($_POST);
	    
	    if($data)
	       ajaxReturn(0,'修改成功，请重新登录',['list'=>$data]);
        ajaxReturn(1,'修改失败'.d('user')->getError());
	}

	//退出
	function logout(){
		$d = ['id' => $this->userId, 'last_logout'=>time()];
		d('user')->data($d)->save();
		session('user', null);
		return ajaxReturn2(0, '已退出登录!');
	}
	
	//消息通知
	function message(){
 		$mod = d('userMsg');
 		$con = $_GET;
 		
		$con['_complex'] = [
			'user_id' => $this->userId, 
		];
// 		return  $this->display();
 		$data = $mod->getPageList($con,"");
 		
 		$this->assign($data);
		$this->display();
		
	}
	
	//站内消息
	function messageSiteNew(){
	    $mod = d('userMsg');
	    $data = [
	        'user_id' =>$_POST['user_id'],
	        'content' => $_POST['content'],
	        'from_user_id' =>$this->user['id'],
	        'type' => d('userMsg')->typeArr['站内信息'],
	    ];
	    $id = $mod->edit($data);
	    if($id){
	        ajaxReturn2(0,'操作成功');
	    }
	    ajaxReturn2(1,$mod->getError());
	}
	
	//消息删除
	function messageDel(){
	    if(!($id = (int)$_REQUEST['id']))
			return ajaxReturn(1, '缺少ID!');
	   $data = d('userMsgRead')->where(['msg_id'=>$id])->find();
	   if($data){
	       if(!d('userMsg')->delete($id) || !d('userMsgRead')->where(['msg_id'=>$id])->delete())
	           return ajaxReturn(1, '删除失败a!');
	   }else{
	       if(!d('userMsg')->delete($id))
	           return ajaxReturn(1, '删除失败!');
	   }
		
		
		return ajaxReturn(0,'删除成功!');
	}
	//消息详情
	function messageDetail(){
		$info = d('userMsg')->getInfo((int)$_GET['id']);
		$info = filter([$info], false,'')[0];
		ajaxReturn2(0,'', $info);
	}
	
	//标记消息为已读
	function messageRead(){
		$id = $_REQUEST['id'];
		$mod= d('user_msg');
		if($mod->read($id, $this->userId)){
			ajaxReturn2(0,'操作成功!');
		}
		ajaxReturn2(1,  $mod->getError());
	}
	//收藏
	function collect(){
		$con['user_id'] = $this->userId;
		$con['type'] = ['lt',1];
		$data = d('collect')->getPageList($con);
		
		$this->assign($data);
		$this->display();
	}
	
	//新消息
	function messageNew(){
		$arr = d('userMsg')->newMsgNum($this->userId);
		ajaxReturn2(0,'',['count'=>$arr]);
	} 
	
	private function checkVer($code, $id=''){
		$ver = new Verify();
		return $ver->check($code, $id);
	}
}
	