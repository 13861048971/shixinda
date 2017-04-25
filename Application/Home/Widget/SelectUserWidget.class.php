<?php 
/**
 * 生成select表单控件
 **/
use Think\Controller;

class SelectUserWidget extends Controller{
	
	/**
	 * @param int $uid 
	 */
	function index($uid = 0, $name = 'user_id'){
		if((int)$uid){
			$user= d('user')->getInfo($uid);
			$this->assign('user', $user);
		}
		$this->assign('name', $name);
		$this->display('Widget:selectUser:index');
	}
}

