<?php
use Think\Controller;
class IndexController extends PublicController {
	public $userId;
    public $configInfo;//网站配置信息
    public $about;//关于我们
	public function _initialize(){
	    $this->configInfo = $this->config();
	    $this->about = $this->aboutOur();
	    $navigation = d("navigation")->where(['pid'=>['eq',6]])->order('rank desc')->select();
	    $childNavigation = d("navigation")->where(['pid'=>['neq',0]])->order('rank desc')->select();

	    foreach ($navigation as $k=>$v){
	        if($v['url'] == $_SERVER['REQUEST_URI'])
	        $navigation[$k]['current'] = true;
	        foreach ($childNavigation as $k2=>$v2) {
	            if($v['id'] == $v2['pid']){
	                $navigation[$k]['list'][] = $v2;
	                 
	            }
	        }
	    }
	    $this->assign('navigation',$navigation);
	    $this->assign('aboutOur',$this->about);
	    $this->assign('config',$this->configInfo);
	}
	
	
    //首页
	public function index(){ 
        
		$this->display();
	}
	
	//产品展示
	public function product(){
	  
	    $this->display();
	}
	
	//新闻
	public function news(){
	
	    $this->display();
	}
	
	//服务
	public function services(){
	   
	    $this->display();
	}
	
	//案例
	public function cases(){
	 
	    $this->display();
	}
	

	/**
	 * 套餐列表
	 **/
	public function mealList(){
		$con = $_GET;
		$con['status'] = ['lt', 1];
		$data = d('meal')->getPageList($con);
		ajaxReturn2(0, null, $data);
	}
	
	//套餐详情
	public function mealDetail(){
		$id = (int)$_GET['id'];
		$info = d('meal')->getInfo($id);
		ajaxReturn2(0,'', ['meal'=>$info]);
	}
	
	//摄影师列表
	public function phoList(){
		$con = $_GET;
		$mod = d('pho');
		$p = $_GET['page'];
		
		$sort = (int)$_GET['sort']; 
		$sortArr = ['默认排序','销量最多','评价最高'];
		$orderArr = ['meal_update_time desc', 'sales desc', 'star desc'];
		$order = $orderArr[$sort];
		!$order && $order = $orderArr[0];
		
		$con['status'] = 1;
		if($con['good_at']){
			$con['_string'] = 'find_in_set("' . $con['good_at'] . '", good_at)';
			unset($con['good_at']);
		}
		
		//评分最高的5个摄影师
		if($sort < 1 ){
			$list = $mod->getList($con, 5,'id', 'star desc');
			!$list && $list = [];
			
			foreach($list as $v){
				$idArr[] = $v['id']; 
			}
			
			$idArr && $con['id'] = ['not in', $idArr];
		}
		$data = $mod->getPageList($con, 'id', $order);
		if($sort < 1 && $p < 2){
			!$data['list'] && $data['list'] = [];
			$data['list'] = array_merge($list, $data['list']);
		}
		$data['sortArr'] = $sortArr;
		
		ajaxReturn2(0,'', $data);
	}
	
	public function phoDetail(){
		$id = (int)$_GET['id'];
		$mod = d('pho');
		ajaxReturn2(0,'', ['pho' => $mod->getInfo($id)]);
	}
	
	//任务列表接口
	public function taskList(){
		$cateMod = d('category');
		$con = $_GET;
		if($id = (int)$con['region']){
			$name = $cateMod->getInfo($id)['name'];
			$name && $con['region'] = ['like', '%'. $name .'%'];
		}
		$perNum = 15;
		$con['pageNum'] && $perNum = $con['pageNum'];
		$con['_complex'] = [
			'begin_time' => ['gt', time()],
			'status' => 0,
		];
		$field = 'id';
		//默认排序
		$lastLogin = time() - 72*3600; //72小时
		if(!$con['sort']){
			$field = 'id';
		}
		$sortArr = ['id desc', 'price desc', 'join_num desc'];
		$sort = $sortArr[(int)$con['sort']];
		$data = d('task')->getPageList($con, $field, $sort, $perNum);
		
		$page = $data['page'];
		$data['regionArr'] = filter($cateMod->getList(null, 2),'id,name');
		$data['typeArr'] = filter($cateMod->getList(null, 1), 'id,name');
		$data['sortArr'] = ["默认排序",'出价排序','火热排序'];
		
		ajaxReturn2(0,'', $data);
	}

	//任务详情接口
	public function taskDetail(){
		$id = (int)$_GET['id'];
		$info = d('task')->getInfo($id);
		$info['joinList'] = d('join')->getList(['task_id'=>$id],4,'id desc');
		
		ajaxReturn2(0,null, ['task'=>$info]);
	}

	//地区
	public function regions(){
		$mod = d('region');
		$province = $mod->getList(['region_type'=>1]);
		$city = $mod->getList(['region_type'=>2]);
		
		foreach($province as $k=>$v){
			$v = ['id'=>$v['id'], 'name'=>$v['region_name']];
			foreach($city as $k2=>$v2){
				if($v2['parent_id'] == $v['id']){
					$v['city'][] = ['id'=>$v2['id'], 'name'=>$v2['region_name']];
					unset($city[$k2]);
				}
			}
			$province[$k] = $v;
		}
		ajaxReturn2(0, '', [ 'province' => $province ]);
	}
	
	//全国城市 按拼音排序
	public function citys(){
		$key = 'city_width_pinyin_order';
		if($citys2 = S($key))
			ajaxReturn2(0, '', [ 'citys' => $citys2 ]);
		
		$mod = d('region');
		$citys = $mod->getList(['region_type'=>2]);
		$districts = $mod->getList(['region_type'=>3]);
		$py = new \Org\Util\Pinyin;
		
		$range = array_filter(range('A', 'Z'), function($v){
			return in_array($v, ['I','O','U','V']) ? false:true ;
		});
		$citys2 = array_fill_keys($range, []);
		
		foreach($citys as $k=>$v){
			$code = strtoupper($py->qupinyin(mb_substr($v['region_name'], 0, 1), 1));
			$v = [ 'id'	=> $v['id'], 'name' => $v['region_name'], 'code' => $code, ];
			
			foreach($districts as $k2=>$v2){
				if($v2['parent_id'] == $v['id']){
					$v['district'][] = ['id'=>$v2['id'], 'name'=>$v2['region_name']];
					unset($city[$k2]);
				}
			}
			$citys2[$code][] = $v;
		}
		ksort($citys2);
		S($key, $citys2);
		
		ajaxReturn2(0, '', [ 'citys' => $citys2 ]);
	}
	
	//类目字典
	public function category(){
		$meal = d('meal');
		$task = d('task');
		$join = d('join');
		$order = d('order');
		$pho = d('pho');
		$userMsg = d('userMsg');
		$attention = d('collect');
		$slide = d('slide');
		
		$typeArr = $task->typeArr;
		
		$styles = self::toObjArr($task->styleArr);
		
		foreach($styles as $k=>$v){
			$styles[$k]['image'] = '/Public/images/task-style'.$v['id'].'.jpg';
		}

		$arr = [
			'task' => [
				'types' => self::toObjArr($task->typeArr),
				'styles' => $styles,
				'statuss' => self::toObjArr($task->statusArr),
				'sorts' => self::toObjArr($task->sortArr),
			],
			'meal' => [
				'scenes' => self::toObjArr($meal->sceneArr),
				'afters' => self::toObjArr($meal->afterArr),
				'statuss' => self::toObjArr($meal->statusArr),
			],
			'join' => [
				'afters' => self::toObjArr($join->afterArr),
				'statuss' => self::toObjArr($join->statusArr),
			],
			'pho' => [
				'types' => self::toObjArr($pho->typeArr),
				'sorts' => self::toObjArr($pho->sortArr),
				'goodAts' => self::toObjArr($pho->goodAtArr),
				'statuss' => self::toObjArr($pho->statusArr),
			],
			'order' => [
				'types' => self::toObjArr($order->typeArr),
				'pays' => self::toObjArr($order->payArr),
				'reportTypes' => self::toObjArr($order->reportTypeArr),
				'cancelTypes' => self::toObjArr($order->cancelTypeArr),
				'stars' => self::toObjArr($order->starArr),
				'statuss' => self::toObjArr($order->statusArr),
			],
			'message' => [
				'cates' => self::toObjArr($userMsg->cateArr),
			],
			'attention' => [
				'types' => self::toObjArr($attention->typeArr),
			],
			'slide' => [
				'types' => self::toObjArr($slide->typeArr),
			],
		];
		
		
		ajaxReturn2(0, '', [ 'category'=>$arr ]);
	}
	
	static function toObjArr($arr){
		$arr2 = [];
		foreach($arr as $k => $v){
			$arr2[] = ['id'=>$k, 'name'=>$v];
		}
		return $arr2;
	}

	//关于我们
	public function about(){
		$info = d('config')->getInfo('about');
		
		$desc = strip_tags($info['value']['content']);
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//使用协议
	public function agreement(){
		$info = d('config')->getInfo('agreement');
		
		$desc = strip_tags($info['value']['content']);
		
		if($_GET['format'] == 'html'){
			$this->assign('desc', $desc);
			return $this->display();
		}
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//使用协议
	public function tradeNote(){
		$info = d('config')->getInfo('trade_note');
		
		$desc = strip_tags($info['value']['content']);
		ajaxReturn2(0,'', ['desc'=>$desc]);
	}
	
	//吐槽我们
	public function feedback(){
		$mod = d('feedback');
		
		$data = [
			'user_id'=>$this->user['id'], 
			'desc' => htmlentities($_POST['desc']),
		];
		
		if(!$mod->edit($data))
			ajaxReturn2(1, $mod->getError());
		
		ajaxReturn2(0,'操作成功!');
	}

	//任务分享页面
	public function taskShare(){
		if( !($id = $_GET['id']) || !($row = d('task')->getInfo($id)) )
			return ajaxReturn2(1, '任务不存在');
		$client = $_GET['client'];
		$row['joinList'] = d('join')->getList(['task_id'=>$id],4,'id desc');
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}
	//套餐分享页面
	public function mealShare(){
		if( !($id = $_GET['id']) || !($row = d('meal')->getInfo($id)) )
			return ajaxReturn2(1, '套餐不存在');
		$client = $_GET['client'];
		
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}
	//摄影师分享页面
	public function phoShare(){
		if( !($id = $_GET['id']) || !($row = d('pho')->getInfo($id)) )
			return ajaxReturn2(1, '任务不存在');
		$client = $_GET['client'];
		
		$conf = d('config')->getInfo('app')['value'];
		$link = $conf['down'];
		$client == 'ios' && ($link = $conf['down_ios']);
		$row['mealList'] = d('meal')->getList(['pho_id'=>$id]);
		$this->assign('downlink', $link);
		$this->assign('row', $row);
		$this->display();
		exit;
	}

	//支付结果通知
	public function payNotify(){
		d('order')->payNotify($_POST);
	}
	
	//关于我们
	public function aboutOur(){
	    $mod = d('config');
	    $info = $mod->getList();
	    $info = $info['about']['node'];
	    
	    return $info;
	}
	//网站配置信息
	public  function config(){
	    $mod = d('config');
	    $list = $mod->getList();
	    $list = $list['config']['node'];

	return $list;
	}
}