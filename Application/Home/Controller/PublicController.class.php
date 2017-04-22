<?php
use Think\Controller;

class PublicController extends Controller {

	public $sid;
	public $user;
	
	public function _initialize(){
		$this->session();
		
		if($this->user = self::isLogin()){
			$this->assign('user', $this->user);
			return;
		}
		// $this->user = d('user')->getInfo(23);return;
		self::checkUrl();
	}
	
	//设置session
	private function session(){
		$sid = $_REQUEST['sid'];
		
		if($sid && strlen($sid) != 26){
			return ajaxReturn2(1, '错误的会话id!');
		}
		
		$sid && session_id($sid);
		session('[start]');
		$this->sid = session_id();
	}
	
	static function isLogin(){
		if(!($user = session('user')))
			return false;
		
		$user = d('user')->getInfo($user['id']);
		
		if(!$user){
			return false;
		}
		
		return $user;
	}
	
	//检测是否需要登录
	static function checkUrl(){
		$arr = [
			'regist','getvercode','login', 'recvcode','passreset'
		];
		
		$ctr = strtolower(CONTROLLER_NAME);
		
		if( in_array($ctr, ['file','index']) )
			return true;
		
		$actName = strtolower(ACTION_NAME);
		if(!in_array($actName, $arr)){
			return ajaxReturn2(1, '你还没有登录!');
			exit;
		}
	}
	
	function _empty(){
		echo '当前页面不存在';
	}
	
	function index(){
		echo '当前页面不存在!';
	}
	
	function upload($type, $filename){
		$typeArr = ['image', 'avatar'];
		!in_array($type, $typeArr) && $type = 'image';

		!$filename && $filename = $type;
        $output = array('err' => 0, 'msg' => '', 'src' => '');
		$upload = new \Think\Upload();							// 实例化上传类
		$upload->maxSize   = 3145728 ;							// 设置附件上传大小
		$upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  = 'Public/upload/'; 						// 设置附件上传根目录
		$upload->savePath  = $type . '/'; 							// 设置附件上传（子）目录
		// 上传文件 
		$info = $upload->upload();
		if(!$info) {
			$output['err'] = 1;
            $output['msg'] = $upload->getError();;
		}else{
			$file = $info[$filename];
			$src = '/'.$upload->rootPath . $file['savepath'] . $file['savename'];
			$output['file'] = $file;
			$output['src'] = $src;
		}
		
		return $output;
	}
	
	/**
	 * 分页
	 * @param $mod
	 * @param $order 排序字段
	 * @param $perNum 每页条数
	 * @param $pageVar 模板的分页视图变量名称
	 * @return $list 数据变量
	 */
	public function page($mod, $order = 'id', $perNum = 15, $pageVar = 'page'){
	    $count   = $mod->count();
	    $Page    = new Think\Page($count, $perNum);
	    $show    = $Page->show();
	    $list 	 = $mod->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign($pageVar, $show);
	    return $list;
	}
	
}