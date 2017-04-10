<?php
use Think\Controller;
class UserController extends PublicController {
	public $mod;
	
	function _initialize(){
		parent::_initialize();
		$this->mod = d('user');
	}
	
	//用户列表
    public function index($field = 'add_time', $showTimeArr = true){
		$userMod = $this->mod;
		$con = $_GET;
		
		$map = $this->setTimeArr($field, $showTimeArr);
		$map && $con += $map;
		
		if($w = trim($con['keywords'])){
			is_numeric($w) && ($where['mobile'] =  ['like', '%' .$w. '%']);
			$where['nickname'] = ['like', '%' .$w. '%'];
			$where['_logic'] = 'or';
			$con['_complex'] = $where;
		}
		
		$rightAction[]  = ['name'=>'添加用户','dialog-lg'=>true,
			'url'=>u('edit'), 'dialog' => true];
		$this->setRightAction($rightAction);
		
		$field && $order = $field . ' desc';
		$data = $userMod->getPageList($con, 'id', $order);
		$this->assign('search', 	$_GET);
		$this->assign('userList', 	$data['list']);
		$this->assign('page', 	  	$data['pageVar']);
		
		if($_GET['newwin']){
			return $this->display('userList');
		}
		if(IS_AJAX){
			return ajaxReturn(0,"", $data['list']);
		}
		
		
		$this->display('index');
    }
	
	public function edit(){
		$this->ajaxEdit('user', null, function($row, $mod){
			$sexList = [['list'=> d('user')->sexArr, 'name'=>'sex', 'checked'=>$row['sex']]];
			$this->assign('sexList', $sexList);
		});
	}
	
	public function del(){
		if($id = (int)$_REQUEST['id']){
			$this->mod->delete($id);
			ajaxReturn(0, '删除成功!');
		}
	}
	
	//登陆时间
	public function loginTime(){
		$this->index('last_login');
	}
	//小黑屋
	public function hei(){
		$_GET['status'] = 1;
		$this->index('', false);
	}

	//变更用户状态
	public function userChange(){
		$mod 	= d('user');
		if(IS_POST){
			$status = (int)$_POST['status'];
			$id 	= (int)$_POST['id'];
			$block_note = $_POST['block_note']; 
			if(!$mod->block($id, $status, $block_note))
				return ajaxReturn(1, $mod->getError().'操作失败!');
			
			return ajaxReturn(0,'操作成功!');
		}
		
		$this->ajaxEdit('user', null, function(&$row, $mod){
			$row['status'] = 1;
			$this->assign('blockArr', $mod->blockArr);
		});
	}
	
	//实名认证
	public function phoList(){
		$con = $_GET;
		$mod = d('pho');
		$data = $mod->getPageList($con);
		$statusArr = [[
			'paddText'=>'状态',
			'name'=>'status', 
			'list'=>$mod->statusArr, 
			'selected' => $_GET['status']
		]];
		
		$typeArr = [[
			'paddText'=>'类型',
			'name'=>'type', 
			'list'=>$mod->typeArr, 
			'selected' => $_GET['type']
		]];
		
		$this->assign($data);
		$this->assign('statusArr', $statusArr);
		$this->assign('typeArr', $typeArr);
		$this->assign('search', $_GET);
		$this->display('phoList');
	}

	//变更摄影师认证
	public function phoChange(){
		$mod 	= d('pho');
		if(IS_POST){
			$status = (int)$_POST['status'];
			$id 	= (int)$_POST['id'];
			$verify_note = $_POST['verify_note']; 
			if(!$mod->change($id, $status, $verify_note))
				return ajaxReturn(1, $mod->getError().'操作失败!');
			
			return ajaxReturn(0,'操作成功!');
		}
		
		$this->ajaxEdit('pho', null, function(&$row, $mod){
			$row['status'] = 2;
			$this->assign('unpassArr', $mod->unpassArr);
		});
	}
	//头部时间选择
	private function setTimeArr($field = 'add_time', $showTimeArr = true){
		if(!$showTimeArr) return;
		$date = $_GET['date'];
		if($date && $ts = explode(' - ', $date) ){
			$arr = explode('.', $ts[0]);
			$arr2 = explode('.', $ts[1]);
			$t = mktime(0,0,0, $arr[1], $arr[2], $arr[0]);
			$t2 = mktime(0,0,0, $arr2[1], $arr2[2], $arr2[0]);
			
			$con['add_time'] = ['between', [$t, $t2 + 3600*24 ] ];
		}
		$selectDate = [['type'=>'daterange', 'name'=> 'date', 'value'=>$date,
			'format'=>'YYYY.MM.DD']];

		!$field && $field = 'add_time';
		$timeArr = [];
		$today = mktime(0,0,0);
		$time = $_GET['time'];
		for($i=0; $i<7;$i++){
			$day = $today - $i*3600*24;
			$timeArr[$day] = [
				'url'  => u().'?time='.$day,
				'name' => $i<1? '今天':date('m月d日', $day), 
				'cur'  => $day == $time ? 1:0, 
			];
			if($time && $day == $time){
				$timeArr[$day]['cur'] = 1;
				$con[$field] = ['between', [$day, $day + 3600*24]];
			}
		}
		$this->assign('selectDate', $selectDate);
		$this->assign('timeArr',	$timeArr);
		return $con;
	}
	
	//认证历史
	public function history(){
		$con = $_GET;
		$con['status'] = ['gt', 0];
		$map = $this->setTimeArr();
		$con['_complex'] = $map;
		$data = d('artist')->getPageList($con);
		$typeList = d('category')->getList(null, 1);
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display('artist');
	}
	
	//用户详情页
	public function userDetail(){
		$id = (int)$_GET['id'];
		$userMod = d('user');
		$user = $userMod->find($id);
		$this->assign('user', $user);
		$this->display('userDetail');
	}	
	
	function report(){
		$con = $_GET;
		$mod = d('report');
		$data = $mod->getPageList($con);
		$this->assign($data);
		$this->display();
	}
	function reportDel(){
		$this->ajaxDel('report');
	}
	
	//评论列表
	public function feedback(){
		$search['title'] = trim($_GET['title']);
		$search['username'] = trim($_GET['username']);
		$feedbackMod = d('Feedback');
		$res = $feedbackMod->getPageTopicList(0, $search);
		
		$rightAction[]  = ['name'=>'添加评论',
			'url'=>u('feedbackEdit'), 'dialog' => true];
		$this->setRightAction($rightAction);
		$this->assign('list', $res['list']);
		$this->assign('page', $res['pageVar']);
		$this->assign('search', $_GET);
		$this->display();
	}
	
	//编辑评论
	public function feedbackEdit(){
		if($_POST){
			$data = $_POST;
			$feedbackMod = d('Feedback');
			if($feedbackMod->edit($data, (int)$data['id']))
				return $this->success('编辑成功!');
			
			return $this->error('编辑失败!'. $feedbackMod->lastError);
		}
		
		$id = (int)$_GET['id'];
		$feedbackMod = d('Feedback');
		$topic = $feedbackMod->getInfo($id);
		
		$typeArr = $feedbackMod->typeArr;
		// $typeArr = [['name'=>'type', 'list'=>$typeArr, 'selected'=>$topic['type']]];
		$this->assign('statusArr', 	$feedbackMod->statusArr);
		$this->assign('typeArr', 	$typeArr);
		$this->assign('topic', 		$topic);
		ajaxReturn(0, '',			['content'=>$this->fetch()]);
	}
	
	public function feedbackDel(){
		if($id = (int)$_REQUEST['id']){
			d('Feedback')->delete($id);
			ajaxReturn(0, "评论删除成功!");
		}
	}

	public function message(){
		$this->setRightAction([[ 'name'=>'添加消息', 'dialog'=>true, 
			'dialog-lg'=>true, 'url' => u('messageEdit') ]]);
		$con = $_GET;
		$data = d('UserMsg')->getPageList($con); 
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display();
	}
	public function messageEdit(){
		$this->ajaxEdit('UserMsg',null, function(&$row, $mod){
			!isset($row['status']) && $row['status'] = 0;
		});
	}
	public function messageDel(){
		$this->ajaxDel('UserMsg');
	}
	
	
}