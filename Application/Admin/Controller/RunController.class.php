<?php
use Think\Controller;
class RunController extends PublicController {
	public $mod;
	
	function _initialize(){
		parent::_initialize();
		$this->mod = d('user');
	}
	function index(){
		$this->task();
	}
	
	//任务列表
	function task(){
		$con = $_GET;
		$mod = d('task');
		$data = $mod->getPageList($_GET);
		
		$statusArr = [['list'=>$mod->statusArr, 'padd1'=>true, 'paddText'=>'状态',
			'name'=>'status', 'selected'=> $_GET['status']]];
		$this->assign('statusArr', $statusArr);
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display('task');
	}
	
	//用户列表
    public function index2($map = []){
		$mod = d('circle');
		$con['hide'] = ['lt', 1];
		$con['delete'] = ['lt', 1];
		
		$con = array_merge($_GET, $con, $map);
		!$con['sort'] && $con['sort'] = 0;
		
		//排序
		$sortArr = [
			['name'=>'按照信息流', 'sort'=>['add_time desc'],],
			['name'=>'按照新鲜事', 'sort'=>['add_time desc'],'con'=>['node_id'=>['lt', 1]],],
		];
		$order = 'add_time desc';
		if( ($sort = $con['sort']) &&  $sortArr[$sort]){
			$order = $sortArr[$sort]['sort'];
			$sortArr[$sort]['con'] && $con += $sortArr[$sort]['con'];
		}
		$orderArr = [['selected'=>$sort, 'list'=>$sortArr,'name'=>'sort']];
		
		!$con['status'] && $con['status'] = ['lt', 1];
		
		//选择时间
		($map = $this->setTimeArr()) && $con += $map; 
		
		//关键词
		if($w = trim($con['keywords'])){
			is_numeric($w) && ($where['mobile'] =  ['like', '%' .$w. '%']);
			$where['nickname'] = ['like', '%' .$w. '%'];
			$where['_logic'] = 'or';
			$con['_complex'] = $where;
		}
		$data = $mod->getPageList($con, 'id', $order);
		
		$rightAction[]  = ['name'=>'添加信息','dialog-lg'=>true,
			'url'=>u('edit'), 'dialog' => true];
		$this->setRightAction($rightAction);
		$this->assign('search', 	$_GET);
		$this->assign('orderArr', 	$orderArr);
		$this->assign('userList', 	$data['list']);
		$this->assign('page', 	  	$data['pageVar']);		
		$this->display('index');
    }
	
	private function setTimeArr($field = 'add_time'){
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
	
	public function edit(){
		$this->ajaxEdit('circle', null, function($row, $mod){
			$elements = [[
				[ 'type'=>'text', 'label' => '评论节点id','name' => 'node_id',
					'value'=>$row['node_id']],
				[ 'type'=>'text', 'label' => '上级id','name' => 'parent_id',
					'value'=>$row['parent_id']],
				[ 'type'=>'text', 'label' => '发布地址','name' => 'address',
					'required' => true ,'value'=>$row['address']],
				[ 'type'=>'editor', 'label' => '内容','name' => 'desc',
					'required' => true ,'value'=>$row['desc']],
			]];
			$this->assign('elements', $elements);
			
		});
	}
	
	public function del(){
		if($id = (int)$_REQUEST['id']){
			$this->mod->delete($id);
			ajaxReturn(0, '删除成功!');
		}
	}
	
	public function dustbin(){
		$map = [
			'hide' => 0, 'delete' => 0,
			'_complex'=> [
				'hide' => 1, 'delete'=>1,'_logic'=>'or'
			]
		];
		$this->index($map);
	}
	
	//隐藏 or 删除
	public function change(){
		if(IS_POST){
			$mod = d('circle');
			$id = (int)$_POST['id'];
			$type = (int)$_POST['type'];
			
			if(!$mod->change($id, $type))
				return ajaxReturn(1, $mod->getError().'操作失败!');
			return ajaxReturn(0, '操作成功!');
		}
	}

	//撤销任务
	public function taskCancel(){
		$mod = d('task');
		if($id = (int)$_POST['id']){
			$cancel_note = htmlspecialchars($_POST['cancel_note']);
			$data = ['id'=>$id, 'status'=>2, 'cancel_time' => time(),
				'cancel_note'=>$cancel_note];
			if(false !== $mod->save($data))
				return ajaxReturn(0, '操作成功!');
			return ajaxReturn(1, '操作失败!'.$mod->getError());
		}
		$this->assign('row', $mod->getInfo($_GET['id']));
		$this->assign('cancelArr', $mod->cancelArr);
		return ajaxReturn(0,'', [ 'content'=> $this->fetch() ] );
	}
	
	//订单管理
	public function order(){
		$con = $_GET;
		$mod = d('order');
		$data = $mod->getPageList($con);
		
		$statusArr = [[
			'paddText'=>'订单状态',
			'name'=>'status', 
			'list'=>$mod->statusArr, 
			'selected' => $_GET['status']
		]];
		
		$payArr = [[
			'paddText'=>'支付方式',
			'name'=>'pay', 
			'list'=>$mod->payArr, 
			'selected' => $_GET['pay']
		]];
		
		$typeArr = [[
			'paddText'=>'类型',
			'name'=>'type', 
			'list'=>$mod->typeArr, 
			'selected' => $_GET['type']
		]];
		
		$this->assign($data);
		$this->assign('statusArr', $statusArr);
		$this->assign('payArr', $payArr);
		$this->assign('typeArr', $typeArr);
		$this->assign('search', $_GET);
		$this->display();
	}
	
	//撤销订单
	public function orderCancel(){
		$mod = d('order');
		if($mod->cancel($_POST)){
			return ajaxReturn(0);
		}
		return ajaxReturn(1, $mod->getError());
	}
	
	//订单完工
	public function orderDone(){
		if(IS_POST){
			$mod = d('order');
			if($mod->done($_POST)){
				return ajaxReturn(0);
			}
			return ajaxReturn(1, $mod->getError());
		}
		
		$this->ajaxEdit('order', 'orderDone',function(&$row, $mod){
			$row['status'] = 3;
			$eles = [[
				['list'=>$mod->starArr, 'type'=>'radio', 'label'=>'评分','required'=>true, 'name'=>'star']
			]];
			
			$this->assign('elements', $eles);
		});
	}
	
	//订单结款 给摄影师
	public function orderPay(){
		if(IS_POST){
			$mod = d('order');
			if($mod->payToPho($_POST)){
				return ajaxReturn(0);
			}
			return ajaxReturn(1, $mod->getError());
		}
		
		$this->ajaxEdit('order', 'orderDone',function(&$row, $mod){
			$row['status'] = 4;
			$eles = [[
				['type'=>'textarea', 'label'=>'结款备注', 'placeholder'=>'备注支付给摄影师款项的方式!','required'=>true, 'name'=>'pay_to_pho_note']
			]];
			
			$this->assign('elements', $eles);
		});
	}
	
	//订单详情页
	public function orderDetail(){
		$mod = d('order');
		$id = (int)$_GET['id'];
		
		$row = $mod->getInfo($id);
		$this->mainTitle = '订单详情';
		$this->assign('row', $row);
		$this->display();
	}	
	
	//投标管理
	public function join(){
		$con = $_GET;
		$mod = d('join');
		$data = $mod->getPageList($_GET);
		
		$statusArr = [['list'=>$mod->statusArr, 'padd1'=>true, 'paddText'=>'状态',
			'name'=>'status', 'selected'=> $_GET['status']]];
		$this->assign('statusArr', $statusArr);
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display('join');
	}
	
	//套餐管理
	public function meal(){
		$con = $_GET;
		$mod = d('meal');
		$data = $mod->getPageList($_GET);
		
		$statusArr = [['list'=>$mod->statusArr, 'padd1'=>true, 'paddText'=>'状态',
			'name'=>'status', 'selected'=> $_GET['status']]];
		$this->assign('statusArr', $statusArr);
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display('meal');
	}
	
	//修改套餐状态
	public function mealChange(){
		$mod = d('meal');
		$id = (int)$_POST['id'];
		$status = (int)$_POST['status'];
		$d = ['status'=>$status, 'id'=>$id];
		
		if(!$id && !$status)
			return ajaxReturn(1, '缺少参数!');
		
		if($mod->data($d)->save())
			return ajaxReturn(0);
		
		return ajaxReturn(1, $mod->getError());
		
	}
	//反馈列表
	public function feedback(){
		$con = $_GET;
		$feedbackMod = d('Feedback');
		$con = $this->setTimeArr('add_time');
		
		$res = $feedbackMod->getPageList($con);
		
		$rightAction[]  = ['name'=>'添加反馈',
			'url'=>u('feedbackEdit'), 'dialog' => true];
		$this->setRightAction($rightAction);
		$this->assign('list', $res['list']);
		$this->assign('page', $res['pageVar']);
		$this->assign('search', $_GET);
		$this->display();
	}
	
	//反馈评论
	public function feedbackEdit(){
		$this->ajaxEdit('feedback', null,function($row, $mod){
			
		});
	}
	
	//首页轮播图
	public function slide(){
		$data = d('slide')->getPageList($_GET);
		$this->setRightAction([[ 'name'=>'添加', 'dialog'=>true, 
			'dialog-lg'=>true, 'url' => u('slideEdit') ]]);
		$this->assign($data);
		$this->display();
	}
	public function slideEdit(){
		$this->ajaxEdit('slide',null, function($row, $mod){
			$typeNode = [['type'=>'select', 'name'=>'type', 'list'=>$mod->typeArr,
				'selected'=>$row['type'],'r'=>5]];
			$imgNode = [['type'=>'image', 'name'=>'image',
				'value'=>$row['image'],'r'=>6]];
			$this->assign('typeNode', $typeNode);
			$this->assign('imgNode', $imgNode);
		});
	}
	public function slideDel(){
		$this->ajaxDel('slide');
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
		
		$tab = $_GET['tab'];
		$tabs = [
			['消息推送', u('',['tab'=>0]), ($tab == 0?1:0) ],
			['推送历史',u('',['tab'=>1]), ($tab == 1?1:0)],
		];	
		$mod = d('UserMsg');
		$con = $_GET;
		$data = d('UserMsg')->getPageList($con);
		
		$typeList = [['name'=>'type2','list' => $mod->typeArr]];
		
		$typeArr = $mod->typeArr;
		$typeIdArr = $mod->typeIdArr;
		
		foreach($typeArr as $k => $v){
			$v = ['name'=>$v, 'value'=>$k];
			$v['ids'] = $typeIdArr[$k];
			$typeArr[$k] = $v;
		}

		$this->assign($data);
		$this->assign('typeList' ,$typeList);
		$this->assign('typeArr' ,$typeArr);
		$this->assign('search', $_GET);
		$this->assign('tabs', $tabs);
		$this->assign('tab', $_GET['tab']);
		$this->display();
	}
	public function messageEdit(){
		$this->ajaxEdit('UserMsg',null, function(&$row, $mod){
			!isset($row['status']) && $row['status'] = 0;
		},'发送');
	}
	public function messageDel(){
		$this->ajaxDel('UserMsg');
	}
	
	
}