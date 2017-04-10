<?php
use Think\Model;
import('Org.Aop.AopClient', '', '.php');
import('Org.Weixin.WeixinAppPay');
/**
 * 订单
 */
class OrderModel extends BaseModel {
	public $cacheKey  = 'order_';
	public $statusArr = ['待付款',1=>'待确认',2=>'拍摄中',3=>'已完成',4=>'已结款', 8=>'已被系统撤销',9=>'已关闭'];
	public $typeArr = [1=>'套餐订单','任务订单'];
	public $payArr = [1=>'支付宝','微信'];
	public $starArr = [1=>'1星','2星','3星','4星','5星'];
	public $reportTypeArr = [ 1=>'联系不上(手机无法接听)','诈骗,提前收取费用',
		'信息违法虚假','涉黄违法','其他原因' ];
	public $cancelTypeArr = [ 1=>'联系不上摄影师','摄影师与描述不符',
		'不想拍了','其他' ];
		
	public $expire;
	
	function __construct(){
		parent::__construct();
		$this->expire = 24 * 3600;
	}
	
	function setValidate($data, $id){
		$this->_validate = [
			['user_id', 'require', 		'缺少用户id!', 1],
			['pho_id', 'require', 		'缺少摄影师id!', 1],
			['node_id', 'require', 		'缺少套餐id或者任务id!', 1],
			['type', 	 'require', 	'缺少套餐类型!', 1],
			['begin_time', 'require', 	'缺少约拍时间!', 1],
			['address',  'require', 	'缺少地址!', 1],
			['buy_num',  'require', 	'缺少件数!', 1],
			['city', 	 'require', 	'缺少城市!', 1],
			['contact',  'require', 	'缺少联系人!', 1],
			['mobile', 	 'require', 	'缺少联系电话!', 1],
		];
		
		//更新
		if($id){
			$info = $this->find($id);
			if(!$info) 
				return $this->setError('订单不存在!');
			$data = array_merge($info, $data);
		}else{
			
			($data['num'] && !$data['buy_num']) && ($data['buy_num'] = $data['num']);
			if(1 == $data['type'] && ($node = d('meal')->getInfo($data['node_id']))){
				if($data['buy_num'] < $meal['min_buy_num'])
					return $this->setError('不能少于最少购买件数!');
				$data['num'] = $data['buy_num'];
				$data['price'] = $node['price'];
				$data['total'] = $node['price'] * $data['buy_num'];
			}
			if(2 == $data['type'] && ($node = d('task')->getInfo($data['node_id']))){
				$data['num'] = $data['buy_num'] = $node['num'];
			}

			$data['pho_id'] = $node['pho_id'];
			$data['city'] = $node['city'];
			$data['city_name'] = d('region')->getName($data['city']);
			if(!$data['total']){
				return $this->setError('订单价格有误!');
			}
			
			$data['order_sn'] = date('YmdHis') . rand(1000, 9999);
		}
		return $data;
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		$data = $this->setValidate($data, $id);
		if($id){
			$data['update_time'] = time();
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(false === $return){
				$this->lastError = '修改失败!';
				return false;
			}
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data))
			return false;

		if(!($id = $this->add())){
			return $this->setError('添加失败!');
		}
		return $id;
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
	
		$info['typeName'] 	= $this->typeArr[$info['type']];
		$info['statusName'] = $this->statusArr[$info['status']];
		$info['addTime'] 	= local_date($info['add_time']);
		$info['updateTime']   = local_date($info['update_time']);
		$info['beginTime']   = local_date($info['begin_time']);
		$info['doneTime']   = local_date($info['done_time']);
		$info['reportTypeName'] = $this->reportTypeArr[$info['report_type']];
		
		if(!$info['pay'] && $info['status'] == 9){
		}
 
		$info['payName'] = $this->payArr[$info['pay']];
		
		if(1 == $info['type']){
			$info['meal'] = d('meal')->getInfo($info['node_id']);
		}
		
		if(2 == $info['type']){
			$info['task'] = d('task')->getInfo($info['node_id']);
		}
		
		$info['user'] = d('user')->getInfo($info['user_id']);
		$info['pho'] = d('pho')->getInfo($info['pho_id']);
		$info['expire'] = $this->expire;
		$leftTime = ($info['add_time'] + $this->expire) - time();
		$info['leftTime'] = $leftTime;
		
		if($info['leftTime'] < 0 && $info['status'] < 1){
			$info['statusName'] = '已过期';
			$info['status'] = 9;
		}
		
		return $info;
	}
	
	/**
	 * 退单
	 */
	public function cancel($post){
		$id = (int)$post['id'];
		if(!$id || !($o = $this->getInfo($id)))
			return $this->setError('订单不存在!');
		if(8 == $o['status'] || 9 == $o['status'])
			return $this->setError('已经操作过了!');
		if($o['status'] > 2)
			return $this->setError('订单状态不能撤销!');
		$users = [$o['pho_id'], $o['user_id']];
		$status = 9;
		//前端退单
		if('Home' == MODULE_NAME ){
			if(!in_array($this->user['id'] ,$users) )
				return $this->setError('没有权限!');
			
			//摄影师拒单
			if($this->user['id'] == $users[0]){
				if(!in_array($o['status'], [1]))
					return $this->setError('系统当前不可退单!');
				
				$o['cancel_note'] = '摄影师婉拒';
			}else{
				//用户取消订单
				if(!in_array($o['status'], [0,1]))
					return $this->setError('系统当前不可退单,请联系摄影师或者摄影热线!');
				
				$o['cancel_note'] = $this->cancelTypeArr[$post['cancel_type']] . ' ' . 
					htmlspecialchars($post['cancel_note']);
			}
			if(!$o['cancel_note'])
				return $this->setError('缺少退单备注!');
		}else{
			$o['status'] < 1 && ($status = 8);
		}

		if($o['pay_time'] && $o['pay']){
			if(!$this->tradeRefund($id)){
				return false;
			}
		}
		$o['status'] = $status;
		
		if($this->edit($o, $id)){
			//摄影师拒单消息
			if($this->user['id'] == $o['pho_id']){
				$d = [
					'cate'      => 2,
					'node_id'	=> $id, 
					'from'		=> $o['pho_id'], 
					'user_id'	=> $o['user_id'],
					'title' 	=> "订单号为{$o[order_sn]},摄影师不能满足要求",
				];
				$d['content'] = $d['title'];
				d('userMsg')->edit($d);	
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 摄影师接单
	 */
	public function receive($post){
		$id = (int)$post['id'];
		if(!$id || !($o = $this->getInfo($id)))
			return $this->setError('订单不存在!');
		if(2 == $o['status'])
			return $this->setError('已经操作过了!');
		if($o['status'] != 1)
			return $this->setError('订单状态不能接单!');
		
		if($o['pho_id'] != $this->user['id'])
			return $this->setError('没有权限!');
		
		$o['status'] = 2;
		$o['receive_time'] = time();
		
		if($this->edit($o, $id)){
			$d = [
				'cate'      => 2,
				'node_id'	=> $id, 
				'from'		=> $o['pho_id'], 
				'user_id'	=> $o['user_id'],
				'title' 	=> '摄影师已接单',
			];
			$d['content'] = $d['title'];
			d('userMsg')->edit($d);	
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * 用户确认完工
	 * 
	 */
	public function done($post){
		$id = (int)$post['id'];
		$star = (int)$post['star'];
		if(!$id || !($o = $this->getInfo($id)))
			return $this->setError('订单不存在!');
		if(!$star)
			return $this->setError('请评分!');
		if($star < 1 || $star > 5 )
			return $this->setError('评分不合适!');
		if(MODULE_NAME == 'Home' && $o['user_id'] != $this->user['id'])
			return $this->setError('没有权限!');
		if(3 == $o['status'])
			return $this->setError('已经操作过了!');
		if(2 != $o['status'])
			return $this->setError('订单状态不能完工!');
		
		$o['status'] = 3;
		$o['done_time'] = time();
		$o['star'] = $star;
		
		$d = [
			'cate'      => 2,
			'node_id'	=> $id, 
			'from'		=> $o['user_id'], 
			'user_id'	=> $o['pho_id'],
			'title' 	=> "订单号为{$o[order_sn]},客户已确认完工",
		];
		$d['content'] = $d['title'];
		
		if($this->edit($o, $id)){
			d('pho')->updateCount($o['pho_id']);
			d('userMsg')->edit($d);
			return true;
		}
		return false;
	}
	
	/**
	 * 确认完工
	 * 
	 */
	public function payToPho($post){
		$id = (int)$post['id'];
		$note = trim($post['pay_to_pho_note']);
		if(!$id || !($o = $this->getInfo($id)))
			return $this->setError('订单不存在!');
		if(!$note)
			return $this->setError('缺少备注!');
		
		$o['status'] = 4;
		$o['pay_to_pho_note'] = $note;
		return $this->edit($o, $id);
	}
	
	/**
	 * 申请支付
	 * @param int $id
	 * @param int $type 支付方式 
	 **/
	public function checkout($id, $type){
		import('Org.Aop.AopClient', '', '.php');
		import('Org.Aop.request.AlipayTradeCreateRequest', '', '.php');
		
		$aop = new AopClient();
		$request = new AlipayTradeCreateRequest ();
		$order = ['out_trade_no'=>'11111', 'total_amount'=>'100', 'subject'=>'subject'];
		$request->setBizContent(json_encode($order));
		$result = $aop->execute($request);
		print_r($result);
		if(!$result)
			return $this->setError($aop->getError());
		
		return true;
	}
	
	/**
	 * 获取订单签名
	 * @param int $id
	 * @param int $pay 支付方式 
	 **/
	public function payParam($id, $pay){
		$order = $this->find($id);
		if(!$order)
			return $this->setError('订单不存在!');
		if(!$this->payArr[$pay])
			return $this->setError('不支持的支付方式!');
		if($order['status'] == 1 )
			return $this->setError('订单已支付!');
		if($order['status'] > 1 )
			return $this->setError('订单不可支付!');
		
		$order['pay'] = $pay;
		if(!$this->edit($order, $id))
			return false;
		
		if($pay == 1){
			return $this->alipayPayParam($order);
		}
		
		return $this->weixinPayParam($order);	
	}
	
	//查看交易状态
	public function tradeQuery($id){
		$order = $this->find($id);
		$aop = new AopClient();
		$res = $aop->tradeQuery($order['order_sn']);
		
		echo $aop->getError();
		return ; 
	}
	
	//退单
	public function tradeRefund($id){
		$order = $this->find($id);
		if($order['pay'] == 1){
			$aop = new AopClient();
			if($res = $aop->tradeRefund($order['order_sn'], $order['total'])){
				return true;
			}
			return $this->setError($aop->getError());
		}
		
		if(2 == $order['pay']){
			$wxPay = new WeixinAppPay();
			if($wxPay->orderRefund($order['order_sn'], $order['total']))
				return true;
			return $this->setError($wxPay->getError());
		}
		
		return false;
	}
	
	/**
	 * 支付通知
	 * @param array $p  通知参数
	 */
	public function payNotify($p){
		//支付宝通知
		if($p && is_array($p)){
			$order_sn = $con['order_sn'] = $p['out_trade_no'];
			$order = $this->where($con)->find();
			if(!$order){
				\Think\Log::write('订单不存在,'.$order_sn, 'err');
				return false;
			}
			if(!$this->payArr[$order['pay']]){
				 \Think\Log::write('不支持的支付方式!'.$order_sn, 'err');
				return false;
			}
			
			if($order['pay'] == 1){
				$aop = new AopClient();
				if(!$aop->rsaCheckV1($p)){
					\Think\Log::write('签名验证失败!'.$order_sn, 'err');
					return false;
				}
			}
			$order['status'] = 1;
			$order['pay_time'] = time();
			if($this->edit($order, $order['id'])){
				echo 'success';
				return true;
			}
			
			\Think\Log::write($order_sn . '通知修改订单状态失败!'.$this->getError(), 'err');
			return false;
		}
		
		$wxPay = new WeixinAppPay();
		$res = $wxPay->payNotify(function($d){
			$order_sn = $con['order_sn'] = $d['out_trade_no'];
			$order = $this->where($con)->find();
			if(!$order){
				\Think\Log::write('微信支付订单不存!订单号:'.$order_sn, 'err');
				return false;
			}
			
			if($order['total'] != $d['total']){
				\Think\Log::write('微信支付金额错误!订单号:'.$order_sn, 'err');
				return false;
			}
			
			$order['status'] = 1;
			$order['pay_time'] = time();
			if($this->edit($order, $order['id']))
				return true;
				
			\Think\Log::write('微信通知修改订单状态失败!'.$order_sn.$this->getError(), 'err');
			return false;
		});
	}
	
	//支付宝支付参数
	private function alipayPayParam($order){
		$aop = new AopClient();
		$order = [
			'out_trade_no' => $order['order_sn'], 
			'total_amount' => $order['total'], 
			'subject'=>'订单类型:'.$this->typeArr[$order['type']],
		];
		return $aop->getPayParam($order, $notifyUrl);	
	}
	
	//微信支付参数
	private function weixinPayParam($order){
		$order['subject'] = '订单类型:'.$this->typeArr[$order['type']];
		$wxPay = new WeixinAppPay();
		$param = $wxPay->getPayParam($order);
		if($order['wx_prepay_id'] != $param['prepayid']){
			$d = ['wx_prepay_id' => $param['prepayid'], 'id'=>$order['id']];
			if(!$this->data($d)->save())
				return false;
		}
		
		return $param;
	}
	
	//订单统计
	function getCountArr($con){ 
		$list = $this->where($con)->group('status')
			->getField('status,count(*)',true);
		$arr = [
			'orderNoPayNum' => (int)$list[0], 
			'orderShotNum' => (int)($list[1] + $list[2]), 
			'orderDoneNum' => (int)($list[3] + $list[4]), 
			'orderCloseNum' => (int)($list[8] + $list[9]),
			'orderNum' => array_sum($list)
		];
		return $arr;
	}
	
	//举报
	function report($id, $type, $note){
		if(!$type || !$this->reportTypeArr[$type])
			return $this->setError('请选择举报类型!');
		
		if(!$note)
			return $this->setError('请填写举报备注说明!');
		
		$data['report_type'] = $type;
		$data['report_note'] = $note;
		$data['status'] = 4;
		return $this->edit($data, $id);
	}
	
	/**
	 * @param array $con
	 * @return array
	 **/
	public function getList($con, $limit = 50, $order = 'add_time desc'){
		$list = $this->where($con)->field('id')->limit($limit)->order($order)->select();
		foreach($list as $k=>$v){
			$list[$k] = $this->getInfo($v['id']);
		}
		return $list;
	}
	
	function getPageList($con, $fields = 'id', $order = 'id desc', $perNum = 15){
		$time = time() - $this->expire ;
		if(isset($con['status']) && $con['status'] !== '' && 
			($con['status'] < 1 || $con['status'] == ['in', [0]]) ){
			$con['add_time'] = ['gt', $time];
		}
		$arr = ['in', [8,9]];
		if($con['status'] && in_array($con['status'], [9, $arr]) ){
			$con['_complex'] = [
				'status' => $arr,
				'_complex' => [
					'add_time' => ['lt', $time],
					'status' => 0
				],
				'_logic' => 'or',
			];
			unset($con['status']);
		}

		$data = parent::getPageList($con, $fields, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$data['list'][$k] = $v;
			
		}
	
		return $data;
	}
	
}