<?php
use Think\Controller;

/**
 * 管理员
 *
 */
class DocumentController extends PublicController {
	public function _initialize(){
		$this->leftNav = [
			['name' => '概况', 'url'=> u('index')],
			['name' => '管理员' , 'url' => u('adminList')],
			['name' => '角色管理', 'url' => u('role')],
			['name' => '服务器信息', 'url' => u('phpInfo')],
		];
		parent::_initialize();
	}
	
    public function index(){
		$id = $this->admin['id'];
		$con = [ 'admin_id' => $id ];
		$sid = session_id();
		$admin = d('admin')->getInfo($id);
		$userMod = d('user');
		$cityArr = d('region')->getList(['region_type'=>2]);
		
		if("注册和登录"){
		$artistMod = d('pho');
		$forms[1] = [[
			'group'  => '注册和登录',
			'title'  => '用户注册 /regist?sid=SID',
			'action' => '/regist',
			'method' => 'post',
			'class'  => 'form-horizontal ajaxSubmit',
			'eles' => [
				['label'=>'手机号','name'=>'mobile', 'require'=>1, ],
				['label'=>'验证码','name'=>'vercode', 'require'=>1, ],
				['label'=>'密码','name'=>'password', 'require'=>1, ],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		
		$forms[2] = [[
			'title'  => '用户登陆 /login?sid=SID',
			'action' => '/login',
			'method' => 'post',
			'class'  => 'form-horizontal ajaxSubmit',
			'eles' => [
				['label'=>'手机号','name'=>'mobile', 'require'=>1, ],
				['label'=>'密码','name'=>'password', 'require'=>1, ],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		$forms[3] = [[
			'title'  => '第三方用户登陆 /login?sid=SID',
			'action' => '/login',
			'method' => 'post',
			'class'  => 'form-horizontal ajaxSubmit',
			'eles' => [
				['label'=>'qq_id','name'=>'qq_id', 'require'=>0, ],
				['label'=>'weixin_id','name'=>'weixin_id', 'require'=>0, ],
				['label'=>'手机号','name'=>'mobile', 'require'=>0, ],
				['label'=>'验证码','name'=>'vercode', 'require'=>0, ],
				['label'=>'nickname','name'=>'nickname', 'require'=>0, ],
				['label'=>'avatar','name'=>'avatar', 'require'=>0, ],
				['label'=>'birthday','name'=>'birthday', 'require'=>0, ],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		
		$forms[4] = [[
			'title'  => '获取验证码 /vercode?sid=SID',
			'action' => '/vercode',
			'method' => 'get',
			'class'  => 'form-horizontal ajaxSubmit',
			'eles' => [
				['label'=>'手机号','name'=>'mobile', 'require'=>true, ],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		$forms[5] = [[
			'title'  => '获取用户信息接口 /user/index?sid=SID',
			'action' => '/user/index',
			'method' => 'get',
			'target' => '_blank',
			'class'  => 'form-horizontal',
			'eles' => [
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		$forms[6] = [[
			'title'  => '更新用户资料 /user/profile?sid=SID',
			'action' => '/user/profile',
			'method' => 'post',
			'target' => '_blank',
			'class'  => 'form-horizontal',
			'eles' => [
				['label'=>'nickname','name'=>'nickname', 'require'=>0, ],
				['label'=>'avatar','name'=>'avatar', 'require'=>0, ],
				['label'=>'birthday','name'=>'birthday', 'require'=>0, ],
				['label'=>'city','name'=>'city', 'require'=>0, ],
				['label'=>'sex','name'=>'sex', 'require'=>0, 'type'=>'radio', 'list'=>$userMod->sexArr],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		
		$forms[7] = [[
			'title'  => '用户退出登录 /logout?sid=SID',
			'action' => '/logout',
			'method' => 'get',
			'target' => '_blank',
			'class'  => 'form-horizontal',
			'eles' => [
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		
		$forms[8] = [[
			'title'  => '密码重置 /user/passReset?sid=SID',
			'action' => '/user/passReset',
			'method' => 'post',
			'target' => '_blank',
			'class'  => 'form-horizontal',
			'eles' => [
				['label'=>'mobile','name'=>'mobile', 'require'=>1, ],
				['label'=>'vercode','name'=>'vercode', 'require'=>1, ],
				['label'=>'password','name'=>'password', 'require'=>1, ],
				['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
			]
		]];
		}
		
		if('首页'){
			$forms[9] = [[
				'group'  => '首页',
				'title'  => '首页 /index?sid=SID',
				'action' => '/index',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[10] = [[
				'title'  => '摄影师列表搜索 /phoList?sid=SID',
				'action' => '/phoList',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[11] = [[
				'title'  => '摄影师主页详情 /phoDetail?sid=SID',
				'action' => '/phoDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'value'=>1, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[12] = [[
				'title'  => '摄影师套餐列表 /mealList?sid=SID',
				'action' => '/mealList',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'pho_id','name'=>'pho_id', 'r'=>5, 'value'=>1, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[13] = [[
				'title'  => '套餐详情 /mealDetail?sid=SID',
				'action' => '/mealDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id',	'name'=>'id', 	'require'=>1, 'type'=>'text' ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			//关注（喜欢）摄影师/套餐,取消关注
			$mod = d('collect');
			$forms[14] = [[
				'title'  => '关注（喜欢）,取消关注 /user/attention?sid=SID',
				'action' => '/user/attention',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'node_id','name'=>'node_id', 	'require'=>1, ],
					['label'=>'type',	'name'=>'type', 	'require'=>1, 'type'=>'select', 'list'=>$mod->typeArr ],
					['label'=>'status',	'name'=>'status', 	'require'=>1, 'type'=>'select', 'list'=>$mod->statusArr ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '预约套餐 /user/inviteMeal?sid=SID',
				'action' => '/user/inviteMeal',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'node_id','name'=>'node_id', 	'require'=>1, ],
					['label'=>'begin_time',	'name'=>'begin_time', 	'require'=>1,],
					['label'=>'address',	'name'=>'address', 	'require'=>1,],
					['label'=>'desc',	'name'=>'desc', 	'require'=>1,],
					['label'=>'num',	'name'=>'num', 	'require'=>1,],
					['label'=>'contact','name'=>'contact', 	'require'=>1,],
					['label'=>'mobile', 'name'=>'mobile', 	'require'=>1,],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		}
		
		if('任务'){
			$mod = d('task');
			$forms[] = [[
				'group'  => '任务',
				'title'  => '任务发布 /user/taskAdd?sid=SID',
				'action' => '/user/taskAdd',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'任务标题','name'=>'title', 'require'=>1, ],
					['label'=>'拍摄类型','name'=>'type', 'require'=>1,'list'=>$mod->typeArr,'type'=>'select'  ],
					['label'=>'所在城市','name'=>'city', 'require'=>1,'list'=>$cityArr,'type'=>'select','nameKey'=>'region_name'  ],
					['label'=>'约定时间','name'=>'begin_time', 'require'=>1, ],
					['label'=>'上交时间','name'=>'over_time', 'require'=>1, ],
					['label'=>'件数','name'=>'num', 'require'=>1, ],
					['label'=>'模特数量','name'=>'model', 'require'=>1, ],
					['label'=>'出价','name'=>'price', 'require'=>1, ],
					['label'=>'风格','name'=>'style', 'require'=>1, 'list'=>$mod->styleArr,'type'=>'select' ],
					['label'=>'实物照片','name'=>'images[0]', 'require'=>1, 'type'=>'image'],
					['label'=>'备注拍摄要求','name'=>'note', 'require'=>1, ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '任务列表 /taskList?sid=SID',
				'action' => '/taskList',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '任务详情 /taskDetail?sid=SID',
				'action' => '/taskDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];

			$forms[] = [[
				'title'  => '任务取消 /user/taskCancel?sid=SID',
				'action' => '/user/taskCancel',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];


			$forms[] = [[
				'title'  => '投标 /pho/taskJoinAdd?sid=SID',
				'action' => '/pho/taskJoinAdd',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'task_id','name'=>'task_id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'num','name'=>'num', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'after','name'=>'after','r'=>5,'type'=>'radio','list'=>d('join')->afterArr,'require'=>true],
					['label'=> 'overdays','name'=>'overdays', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'price','name'=>'price', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'makeup','name'=>'makeup', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'dapei','name'=>'dapei', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'address','name'=>'address', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'todoor','name'=>'todoor', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'model','name'=>'model', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'note','name'=>'note', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'flow','name'=>'flow', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '投标列表 /pho/taskJoinList?sid=SID',
				'action' => '/pho/taskJoinList',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'task_id','name'=>'task_id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '投标详情 /pho/taskJoinDetail?sid=SID',
				'action' => '/pho/taskJoinDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '投标婉拒 /user/taskJoinRefuse?sid=SID',
				'action' => '/user/taskJoinRefuse',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '投标接受并下单 /user/taskJoinReceive?sid=SID',
				'action' => '/user/taskJoinReceive',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		
		}
		
		if('订单'){
			$forms[] = [[
				'group'  => '订单',
				'title'  => '我的订单列表 /user/myOrder?sid=SID',
				'action' => '/user/myOrder',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '订单详情 /user/orderDetail?sid=SID',
				'action' => '/user/orderDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'value'=>1, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '申请支付 /user/checkout?sid=SID',
				'action' => '/user/checkout',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id','name'=>'id', 'require'=>1, ],
					['label'=>'pay','name'=>'pay', 'require'=>1,'list'=>d('order')->payArr, 'type'=>'select' ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '退单 /user/orderCancel?sid=SID',
				'action' => '/user/orderCancel',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id','name'=>'id', 	'require'=>1, ],
					['label'=> 'cancel_type','name'=>'cancel_type', 'r'=>5,'list'=>d('order')->cancelTypeArr , 'type'=>'select', 'require'=>true],
					['label'=> 'cancel_note','name'=>'cancel_note', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '订单完工 /user/orderDone?sid=SID',
				'action' => '/user/orderDone',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id','name'=>'id', 	'require'=>1, ],
					['label'=>'star','name'=>'star', 	'require'=>1, ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师婉拒订单 /pho/orderRefuse?sid=SID',
				'action' => '/pho/orderRefuse',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师接单 /pho/orderReceive?sid=SID',
				'action' => '/pho/orderReceive',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '订单举报 /user/phoReport?sid=SID',
				'action' => '/user/phoReport',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'pho_id','name'=>'pho_id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'report_type','name'=>'report_type', 'r'=>5, 'type'=>'select','list'=>d('report')->reportTypeArr, 'require'=>true],
					['label'=> 'report_note','name'=>'report_note', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		}
		
		if('消息'){
		
			$forms[] = [[
				'group'  => '消息',
				'title'  => '消息列表 /user/message?sid=SID',
				'action' => '/user/message',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'isRead','name'=>'isRead', 'r'=>5, 'type'=>'text',],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '消息详情 /user/messageDetail?sid=SID',
				'action' => '/user/messageDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '消息已读接口 /user/messageRead?sid=SID',
				'action' => '/user/messageRead',
				'method' => 'POST',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'id','name'=>'id', 'r'=>5, 'type'=>'text', 'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		}
		
		if('用户中心'){
			$forms[] = [[
				'group'  => '用户中心',
				'title'  => '用户信息 /usersid=SID',
				'action' => '/user',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '我发布的任务 /user/myTask?sid=SID',
				'action' => '/user/myTask',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '我的喜欢（关注） /user/myAttention?sid=SID',
				'action' => '/user/myAttention',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'type','name'=>'type', 'r'=>5, 'type'=>'select', 'list'=>d('collect')->typeArr,],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '我发布的任务 /user/myTask?sid=SID',
				'action' => '/user/myTask',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '关于摄蝇 /about?sid=SID',
				'action' => '/about',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '建议反馈 /feedback?sid=SID',
				'action' => '/feedback',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> '内容','name'=>'desc', 'r'=>5,'require'=>true],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '使用协议 /agreement?sid=SID',
				'action' => '/agreement',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
		}
		
		if('摄影师'){
			$forms[] = [[
				'group'  => '摄影师',
				'title'  => '摄影师个人认证申请 /pho/verPho?sid=SID',
				'action' => '/pho/verPho',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'realname','name'=>'realname', 'require'=>1, ],
					['label'=>'idno','name'=>'idno', 'require'=>1, ],
					['label'=>'alipay','name'=>'alipay', 'require'=>1, ],
					['label'=>'worklink','name'=>'worklink', 'require'=>1, ],
					['label'=>'city','name'=>'city', 'require'=>1, ],
					['label'=>'手持身份证','name'=>'image1', 'require'=>1, 'type'=>'image' ],
					['label'=>'身份证正面图片','name'=>'image2', 'require'=>1,  'type'=>'image'],
					['label'=>'身份证反面图片','name'=>'image3', 'require'=>1,  'type'=>'image'],
					['label'=>'主页背景','name'=>'pho_bg', 'require'=>1,  'type'=>'image'],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '摄影师个人认证详情 /pho/verPhoDetail?sid=SID',
				'action' => '/pho/verPhoDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师机构认证申请 /pho/verPhoOrg?sid=SID',
				'action' => '/pho/verPhoOrg',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'orgname','name'=>'orgname', 'require'=>1, ],
					['label'=>'orgaddress','name'=>'orgaddress', 'require'=>1, ],
					['label'=>'realname','name'=>'realname', 'require'=>1, ],
					['label'=>'idno','name'=>'idno', 'require'=>1, ],
					['label'=>'alipay','name'=>'alipay', 'require'=>1, ],
					['label'=>'worklink','name'=>'worklink', 'require'=>1, ],
					['label'=>'city','name'=>'city', 'require'=>1, ],
					['label'=>'手持身份证','name'=>'image1', 'require'=>1, 'type'=>'image' ],
					['label'=>'身份证正面图片','name'=>'image2', 'require'=>1,  'type'=>'image'],
					['label'=>'身份证反面图片','name'=>'image3', 'require'=>1,  'type'=>'image'],
					['label'=>'主页背景','name'=>'pho_bg', 'require'=>1,  'type'=>'image'],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师机构认证详情 /pho/verPhoOrgDetail?sid=SID',
				'action' => '/pho/verPhoOrgDetail',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师更新信息 /pho/phoEdit?sid=SID',
				'action' => '/pho/phoEdit',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'pho_bg','name'=>'pho_bg', 'r'=>5, 'type'=>'image','require'=>false ],
					['label'=> 'service_city','name'=>'service_city', 'r'=>5, 'type'=>'select','list'=> $cityArr,'nameKey'=>'region_name'],
					['label'=> 'goodAtArr','name'=>'goodAtArr', 'r'=>5, 'type'=>'select', 'list'=>d('pho')->goodAtArr],
					['label'=> 'good_at','name'=>'good_at', 'r'=>5, ],
					['label'=> 'inshort','name'=>'inshort', 'r'=>5, 'type'=>'text',],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$mod = d('meal');
			$forms[] = [[
				'title'  => '摄影师添加套餐 /pho/mealAdd?sid=SID',
				'action' => '/pho/mealAdd',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'标题','name'=>'title', 'require'=>1, ],
					['label'=>'标签','name'=>'tag', 'require'=>1, ],
					['label'=>'服务城市','name'=>'city', 'require'=>0,'list'=>$cityArr,'type'=>'select','nameKey'=>'region_name' ],
					['label'=>'场景','name'=>'scene', 'require'=>1,'list'=>$mod->sceneArr,'type'=>'select' ],
					['label'=>'后期','name'=>'after', 'require'=>1, 'list'=>$mod->afterArr,'type'=>'select' ],
					['label'=>'拍摄类型','name'=>'type', 'require'=>1,'list'=>$mod->typeArr,'type'=>'select'  ],
					['label'=>'件数','name'=>'num', 'require'=>1, ],
					['label'=>'提供化妆','name'=>'makeup', 'require'=>1, ],
					['label'=>'模特数量','name'=>'model', 'require'=>1, ],
					['label'=>'出价','name'=>'price', 'require'=>1, ],
					['label'=>'实物照片','name'=>'images[0]', 'require'=>1, 'type'=>'image'],
					['label'=>'服务流程','name'=>'flow', 'require'=>1, ],
					['label'=>'特色','name'=>'note', 'require'=>1, ],
					['label'=>'套餐背景图','name'=>'meal_bg', 'require'=>1, 'type'=>'image'],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师编辑套餐 /pho/mealEdit?sid=SID',
				'action' => '/pho/mealEdit',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id','name'=>'id', 'require'=>1, ],
					['label'=>'标题','name'=>'title',],
					['label'=>'标签','name'=>'tag', ],
					['label'=>'服务城市','name'=>'city', 'require'=>0,'list'=>$cityArr,'type'=>'select','nameKey'=>'region_name' ],
					['label'=>'场景','name'=>'scene','list'=>$mod->sceneArr,'type'=>'select' ],
					['label'=>'后期','name'=>'after','list'=>$mod->afterArr,'type'=>'select' ],
					['label'=>'拍摄类型','name'=>'type','list'=>$mod->typeArr,'type'=>'select'  ],
					['label'=>'件数','name'=>'num',],
					['label'=>'提供化妆','name'=>'makeup',],
					['label'=>'模特数量','name'=>'model',],
					['label'=>'出价','name'=>'price',],
					['label'=>'实物照片','name'=>'images[0]','type'=>'image'],
					['label'=>'服务流程','name'=>'flow',],
					['label'=>'特色','name'=>'note',],
					['label'=>'套餐背景图','name'=>'meal_bg','type'=>'image'],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '摄影师删除套餐 /pho/mealDel?sid=SID',
				'action' => '/pho/mealDel',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=>'id','name'=>'id', 'require'=>1, ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '我的投标 /pho/myJoin?sid=SID',
				'action' => '/pho/myJoin',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '我的工作单 /pho/myWorkorder?sid=SID',
				'action' => '/pho/myWorkorder',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		}
		
		if('其他'){
			$forms[] = [[
				'group'  => '其他',
				'title'  => '图片上传 /file/image?sid=SID',
				'action' => '/file/image',
				'method' => 'post',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'enctype'=> 'multipart/form-data',
				'eles' => [
					['label'=>'file','name'=>'file', 'type'=>'file','require'=>1, ],
					['label'=>'type','name'=>'type', 'type'=>'radio', 'list' => ['avatar'=>'avatar', 
						'image'=>'image'],'checked'=>'image' ,'require'=>1, ],
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '地区接口 /regions?sid=SID',
				'action' => '/regions',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			
			$forms[] = [[
				'title'  => '全国城市 /citys?sid=SID',
				'action' => '/citys',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
			$forms[] = [[
				'title'  => '类目字典 /category?sid=SID',
				'action' => '/category',
				'method' => 'get',
				'target' => '_blank',
				'class'  => 'form-horizontal',
				'eles' => [
					['label'=> 'sid','name'=>'sid', 'r'=>5, 'value'=>$sid, 'type'=>'text', 'require'=>true],
				]
			]];
		}
		
		$this->assign('admin', $admin);
		$this->assign('sexArr', [['list' => $userMod->sexArr, 'selected'=>1,'name'=>'sex' ]]);
		$this->assign('sid', $sid);
		$this->assign('forms', $forms);
		$this->display();
    }
	
	public function db(){}
	public function code(){}
	public function server(){}
	
}