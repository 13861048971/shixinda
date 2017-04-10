<?php
use Think\Controller;
class CountController extends PublicController {
	public $mod;
	
	function _initialize(){
		parent::_initialize();
		$this->mod = d('user');
	}
	
	//每日数据
    public function index($fieldArr = null, $return = false){
	    if($arr = explode(' - ', $_GET['date'])){	
			$d = explode('.', $arr[0]);
			$from = mktime(0,0,0, $d[1], $d[2], $d[0]);
			$d = explode('.', $arr[1]);
			$to = mktime(0,0,0, $d[1], $d[2], $d[0]);
		}
		$d  = getdate();
		$to1 = mktime(0,0,0, $d['mon'], $d['mday'], $d['year']);
		!$from  && ($from = $to1 - 3600*24*6);
		!$to  && $to = $to1;
		
		//要统计的字段和表
		!$fieldArr && $fieldArr = [
			['新增注册用户',	'add_time', 	'user'],
			['登陆用户', 		'last_login', 	'user'],
			['申请个人认证',	'add_time', 	'pho', ['type'=>['lt',1]]	],
			['通过实名认证', 	'verify_time', 	'pho', ['type'=>['lt',1], 'status'=>1] ], 
			['申请机构认证', 	'add_time', 	'pho', ['type'=>['gt', 0], ] ],
			['通过机构认证', 	'verify_time', 	'pho', ['type'=>['gt', 0],] ],
			['发布任务', 		'add_time', 	'task',],
			['中标数', 			'add_time', 	'task',['status'=>1]],
			['发起订单', 		'add_time', 	'order',],
			['付款订单', 		'pay_time', 	'order',],
			['确认订单', 		'receive_time', 'order',],
			['完工订单', 		'done_time', 	'order',],
			['已接单', 			'update_time',  'order',],
		];
		
		if($from && $to && $to > $from){
			$error = '只能统计一个月的数据';
			if($to - $from < 3600 * 24 *30){
				$error = '';
				$days = ($to - $from)/3600/24;
				$dateArr = $arrT1 = ['合计'];
				for($i=0;$i<$days+1; $i++){
					$t = $from + $i*3600*24;
					$arrT1[] = self::d($t);
					$dateArr[] = date('Y-m-d', $t);
				}
				//取数据
				foreach($fieldArr as $v){
					$numArr[] = $this->getNum($v, $dateArr, [$from, $to]);
				}
				
				$rows = [];
				foreach($dateArr as $k=>$v){
					$arr = [$v, $arrT1[$k]];
					foreach($fieldArr as $k2=>$v2){
						$arr[] = (int)$numArr[$k2][$k];
					}
					$rows[] = $arr;				
				}
				krsort($rows, 0);
			}
		}
		
		$v = date('Y.m.d', $from).' - '.date('Y.m.d', $to);
		$selectDate = [['type'=>'daterange', 'name'=> 'date', 'value'=>$v,'opens'=>'right',
			'format'=>'YYYY.MM.DD']];
		
		$this->assign('fieldArr', 	$fieldArr);
		$this->assign('rows', 		$rows);
		$this->assign('selectDate', $selectDate);
		$this->assign('selectDate2', $selectDate2);
		$this->assign('error', $error);
		
		if($return) return $selectDate2;
		
		$this->display('index');
    }
	
	//格式化时间显示
	static function d($timestamp){
		$now = date('Y-m-d');
		$date = date('Y-m-d', $timestamp);
		
		if($now == $date)
			return '今天';
		
		return date('m月d日',$timestamp);
	}
	
	/**
	 * 去统计数据
	 * @param array $arr [1=>'统计字段',2=>'模型名称',3=>'附加条件']
	 * @param array $dateArr
	 */
	public function getNum($arr, $dateArr, $range){
		$mod = d($arr[2]);
		$con = [$arr[1] =>['between', $range]];
		$arr[3] && $con += $arr[3];
		
		$list = $mod->where($con)->getField($arr[1], true);
		$arr2 = [];
		foreach($list as $v){
			$t = date('Y-m-d', $v);
			$k = array_search($t, $dateArr);
			if($k === false) continue;
			$arr2[$k] += 1;
			$arr2[0] += 1;
		}
		return $arr2;
	}
	
	/**
	 * 获取统计时间段
 	 */
	private function getDateArr(){
		if($arr = explode(' - ', $_GET['date'])){
			$d = explode('.', $arr[0]);
			$from = mktime(0,0,0, $d[1], $d[2], $d[0]);
			$d = explode('.', $arr[1]);
			$to = mktime(0,0,0, $d[1], $d[2], $d[0]);
		}
		$d  = getdate();
		$to1 = mktime(0,0,0, $d['mon'], $d['mday'], $d['year']);
		!$from  && ($from = $to1 - 3600*24*6);
		!$to  && $to = $to1;
		
		$sec = 3600 * 24;
		
		if($from && $to && $to > $from){
			$error = '只能统计一个月内的数据!';
			if($to - $from < 3600 * 24 *31){
				$error = '';
				$days = ($to - $from)/3600/24;
				$dateArr = $arrT1 = [];
				for($i=0;$i<$days+1; $i++){
					$t = $from + $i*$sec;
					$arrT1[] = [
						'date' => local_date($t),
						'name' => self::d($t), 
						'range'=>[$t, $t+$sec],
						'total' => 0,
						'num' => 0
					];
				}
			}
		}
		$selectDate = [[
			'type' =>'daterange', 
			'name' => 'date', 
			'value'=> date('Y.m.d', $from).' - '.date('Y.m.d', $to),
			'opens'=>'right',
			'format'=>'YYYY.MM.DD'
		]];
		$this->assign('selectDate', $selectDate);
		$this->assign('error', $error);
		return ['between'=> [$from, $to], 'dateArr' => $arrT1];
	}
	
	//订单统计
	public function order(){
		$tab = $_GET['tab'];
		!$tab && $tab = 1;
		$tabArr = [
			1=>['按地区', u('', ['tab'=>1]), $tab == 1,   ],
			2=>['订单趋势', u('', ['tab'=>2]), $tab == 2,   ],
		];
		
		$mod = d('order');
		if($tab == 1){
			$data = $mod->field('count(id) as value,city_name as name')
				->group('city')->order('value desc')->limit(100)->select();
			
			foreach($data as $k=>$v){
				if($v['name']) continue;
				$v['name'] = '其他';
				$data[$k] = $v;
			}
			
			$sData = json_encode($data);
		}else{
			$arr = $this->getDateArr();
			$con = ['add_time' => ['between', $arr['between']]];
			$list = d('order')->where($con)->getField('id,total,add_time', true);
			$dateArr = $arr['dateArr'];
			
			foreach($list as $k=>$v){
				foreach($dateArr as $k2=>$v2){
					if($v['add_time'] >= $v2['range'][0] && $v['add_time'] < $v2['range'][1] ){
						$dateArr[$k2]['total'] += $v['total'];
						$dateArr[$k2]['num'] += 1;
					}
				}
			}
			foreach($dateArr as $v){
				$sData['x'][] = $v['name'];
				$sData['total'][] = $v['total'];
				$sData['num'][] = $v['num'];
			}
			$sData = json_encode($sData);
		}
		
		$this->assign('sData', $sData);
		$this->assign('tab', $tab);
		$this->assign('tabs', $tabArr);
		$this->display();
	}
	
	//在线用户统计
	public function user(){
		$mod = d('user');
		$pho = d('pho');
		
		$d  = getdate();
		$cur = mktime(0,0,0, $d['mon'], $d['mday'], $d['year']);
		
		// $con = ['last_login' => ['gt', $cur]];
		
		$sub = $pho->field('id')->select(false);
		$con[] = 'last_login > last_logout';
		
		$d = $mod->where($con)->field('count(id) user_num, sum(if( (id in ' . $sub . '),1,0)>0) as pho_num')->find();
		$arr = [
			['name'=>'普通用户', 'value'=>$d['user_num'] - $d['pho_num']],
			['name'=>'摄影师', 'value'=> $d['pho_num']],
		];
		$sData = json_encode($arr);
		$this->assign('d',$d);
		$this->assign('sData', $sData);
		$this->display();
	}
	
}