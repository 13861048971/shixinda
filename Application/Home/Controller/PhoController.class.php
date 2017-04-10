<?php
use Think\Controller;
use Think\Verify;
class PhoController extends PublicController{
	public $mod;
	public $userId;
	public $pho;
	
	function _initialize(){
		parent::_initialize();
		$this->mod = D('User');
		$this->userId = $this->user['id'];
	}
	
	function index(){
		
	}
	
	//摄影师退单
	function orderRefuse(){
		$mod = d('order');
		if($mod->cancel($_POST)){
			return ajaxReturn2(0, '操作成功!');
		}
		return ajaxReturn2(1, $mod->getError());
	}
	
	//摄影师接单
	function orderReceive(){
		$mod = d('order');
		if($mod->receive($_POST)){
			return ajaxReturn2(0, '操作成功!');
		}
		return ajaxReturn2(1, $mod->getError());
	}
	
	//投标
	function taskJoinAdd(){
		$mod = d('task');
		if($id = $mod->join($_POST)){
			return ajaxReturn2(0, '操作成功!',['id'=>$id]);
		}
		ajaxReturn2(1, $mod->getError());
	}
	
	//投标列表
	function taskJoinList(){
		$task_id = (int)$_GET['task_id'];
		$con['task_id'] = $task_id;
		$mod = d('join');
		$data = $mod->getPageList($con);
		ajaxReturn2(0,'', $data);
	}
	
	//投标详情
	function taskJoinDetail(){
		$id = (int)$_GET['id'];
		$join = d('join')->getInfo($id);
		$task = $join['task'];
		if(in_array($this->userId, [$join['user_id'], $task['user_id']]))
			return ajaxReturn2(0,'', ['join'=>$join]);
		
		ajaxReturn2(1,'没有权限!');
	}
	
	//投标拒绝
	function taskJoinRefuse(){
		$mod = d('join');
		$data = $_POST;
		$data['user_id'] = $this->userId;
		if($mod->refuse($data)){
			return ajaxReturn2(0, '操作成功!');
		}
		return ajaxReturn2(1, $mod->getError());
	}
	
	//投标接受并下单
	function taskJoinReceive(){
		$mod = d('join');
		$data = $_POST;
		$data['user_id'] = $this->userId;
		if($mod->receive($data)){
			return ajaxReturn2(0, '操作成功!');
		}
		return ajaxReturn2(1, $mod->getError());
	}
	
	//添加套餐
	function mealAdd(){
		$mod = d('meal');
		$data = $_POST;
		$data['pho_id'] = $this->userId;
		if($id = $mod->edit($data)){
			return ajaxReturn2(0, '操作成功!',['id'=>$id]);
		}
		return ajaxReturn2(1, $mod->getError());
	}
	//编辑套餐
	function mealEdit(){
		$d = $_POST;
		$mod = d('meal');
		if(!$d['id'] || !($row = $mod->find($d['id'])))
			return ajaxReturn2(1, '套餐不存在!');
		
		if($this->userId != $row['pho_id'])
			return ajaxReturn2(1, '没有权限!');
		
		$d = array_merge($row, $d);
		if($mod->edit($d, $d['id']))
			return ajaxReturn2(0, '操作成功!');
		
		return ajaxReturn2(1, $mod->getError());
	}
	
	//删除套餐
	function mealDel(){
		$d = $_POST;
		$mod = d('meal');
		if(!$d['id'] || !($row = $mod->find($d['id'])))
			return ajaxReturn2(1, '套餐不存在!');
		
		if($this->userId != $row['pho_id'])
			return ajaxReturn2(1, '没有权限!');
		
		$d['status'] = 2;
		if($mod->data($d)->save())
			return ajaxReturn2(0, '已删除!');
		
		return ajaxReturn2(1, $mod->getError());
	}
	
	//我的喜欢
	function myAttention(){
		$mod = d('collect');
		$con = $_GET;
		$con['user_id'] = $this->userId;
		$data = $mod->getPageList($con);
		ajaxReturn2(0,'',$data);
	}
	//实名认证申请
	function verRealname(){
		$mod = d('artist');
		$data = $_POST;
		$data['user_id'] = $this->userId;
		if(!$mod->edit($data)){
			return ajaxReturn2(1, $mod->getError());
		}
		return ajaxReturn2(0, '操作成功!');
	}
	
	//实名认证详情
	function verRealnameDetail(){
		$info = d('artist')->getInfo($this->userId);
		$info = filter([$info], 'id,user_id,realname,image1,image2,image3,idno,status,statusName');
		
		ajaxReturn2(0, '', $info[0]);
	}
	
	//摄影师认证申请
	function verPho(){
		$mod = d('pho');
		$data = $_POST;
		$data['user_id'] = $this->userId;
		
		$row = $mod->getInfo($this->userId);
		$row && $data = array_merge($row, $data);
		
		if(!$mod->edit($data, $row['id'])){
			return ajaxReturn2(1, $mod->getError());
		}
		return ajaxReturn2(0, '操作成功!');
	}
	
	//机构摄影师认证
	function verPhoOrg(){
		$name = $_POST['orgname'];
		$address = $_POST['orgaddress'];
		
		if(!$name)
			return ajaxReturn2(1, '缺少认证机构名称!');
		if(!$address)
			return ajaxReturn2(1, '缺少认证机构地址!');
		$_POST['type'] = 1;
		$this->verPho();
	}
	//个人摄影师认证详情
	function verPhoDetail(){
		$info = d('pho')->getInfo($this->userId);
		$info = filter([$info], 'orgaddress,orgname', false);
		ajaxReturn2(0, '', $info[0]);
	}
	//机构摄影师认证详情
	function verPhoOrgDetail(){
		$info = d('pho')->getInfo($this->userId);
		$info = filter([$info], 'image3', false);
		ajaxReturn2(0, '', $info[0]);
	}
	
	//摄影师更新信息
	function phoEdit(){
		$d = array_filter($_POST);
		if(!$d){
			ajaxReturn2(1, '缺少参数');
		}
		$d['id'] = $this->userId;
		$mod = d('pho');
		if(false !== $mod->data($d)->save())
			return ajaxReturn2(0, '更新成功!');
		
		return ajaxReturn2(1, $mod->getError());
	}
	
	//摄影师的投标列表
	function myJoin(){
		$mod = d('join');
		$con = $_GET;
		$con['user_id'] = $this->userId;
		
		$tabArr = [['lt', 1],['gt', 0]];
		$tab = $con['tab'];
		isset($tab) && $tabArr[$tab] && ($con['status'] = $tabArr[$tab]);
		
		$data = $mod->getPageList($con);
		ajaxReturn2(0,'',$data);
	}
	
	//摄影师 工作单
	function myWorkorder(){
		$mod = d('order');
		$arr = [
			['in', [1]], //待确认
			['in', [2]], //拍摄中
			['in', [3,4]], //已完成
			['in', [8,9]], //已关闭
		];
		$tab = $_GET['tab'];
		$tabV = $arr[$tab];
		$tabV && $con['status'] = $tabV;
		
		$con['pho_id'] = $this->userId;
		$data = $mod->getPageList($con);
 		ajaxReturn2(0, '', $data);
	}
}
	