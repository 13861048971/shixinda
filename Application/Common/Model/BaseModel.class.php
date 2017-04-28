<?php
use Think\Model;
class BaseModel extends Model {
	public $lastError;
	protected $errorCode;
	public $user, $admin, $shop;

	public function __construct(){
		parent::__construct();
		$this->user = session('user');
		
		//$this->admin = session('admin');
		//$this->shop = session('shop');
		// session('[pause]');
	}
	
	/**
	 * 是否是管理员
	 */
	protected function isAdmin(){
		$this->admin = session('admin');
		if(!$this->admin)
			return false;
		return true;
	}
	
	//获取缓存
	protected function getCache($key, $method, $param = ''){
		$data = s($key);
		if(!$data){
			$method = '_cache' . $method;
			$data = $this->$method($param);
			s($key, $data);
		}
		return $data;
	}
	
	//删除缓存
	protected function removeCache($key){
		return s($key, null);
	}
	
	//重置缓存
	protected function resetCache($key, $method, $param = ''){
		$method = '_cache' . ucfirst($method);
		$data = $this->$method($param);	
		s($key, $data);
		return $data;
	}
	
	//是否存在
	public function isExists($data){
		return $this->where($data)->find();
	}
	
	//清空表
	public function truncate(){
		$sql = ' TRUNCATE TABLE `' . $this->trueTableName . '`';
		$this->execute($sql);
	}
	
	//检测用户是否登录
	function checkUser(){
		$userMod = D('User');
		$uid = $userMod->getUserId(cookie('username'));
		if(!$uid){
			$this->lastError = '登陆出现了问题!';
			return false;
		}
		return $uid;
	}

	/**
	 * 获取带有分页的数据列表
	 */
	public function getPageList($con, $fields = 'id',$order = '', $perNum = 15){
		!$_GET['p'] && $_GET['p'] = $_GET['page'];
		$_REQUEST['perNum'] > 0 && $perNum = (int)$_REQUEST['perNum'];
		
		foreach($con as $k=>$v){
			if(!$v && $v !== '0' )
				unset($con[$k]);
		}
		
		$count   = $this->where($con)->count();

		$Page    = new Think\Page($count, $perNum);
		$Page->setConfig('header','<li class="rows">共<b>%TOTAL_ROW%</b>条记录 第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('first','首页');
		$Page->setConfig('last','末页');
		$Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
		$Page->lastSuffix = false;//最后一页不显示为总页数
		$pageVar = $Page->show();
		$page = [
			'page'	=> (int)$Page->totalPages, 
			'cur'	=> (int)$Page->cur_page, 
			'pre'	=> (int)$Page->up_page, 
			'next'	=> (int)$Page->down_page,
			'total' => (int)$count,
		];
		
		if($fields) 
		 $this->field($fields);
		$list 	 = $this->where($con)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		$arr['list'] = $list;
		!$arr['list'] && $arr['list'] = [];
		
		$arr['pageVar'] = $pageVar;
		
		return array_merge($arr,$page);
	}
	
	//设置错误信息
	protected function setError($msg, $code=1,$flag = false){
		$this->lastError = $msg;
		$this->errorCode = $code;
		return $flag;
	}
	
	//错误码
	public function getErrorCode(){
		return $this->errorCode;
	}
	
	public function getError(){
		if($this->lastError)
			return $this->lastError;
		return parent::getError();
	}
	
	/**
	 * 使用云片发送短信
	 * @param string $mobile 手机号
	 * @param array  $varArr 参数列表
	 * @param string 短信类型
	 * @return bool
	 **/
	protected function sendMsg($mobile, $varArr, $type = 'code'){
		$typeArr = [
			'code'    	  => '验证码',
			'userBlock'   => '封号',
			'userUnblock' => '解封',
			'verPass' 	  => '认证通过',
			'verUnpass'   => '认证未通过'
		];
		
		$conf = d('config')->getInfo('SMS')['value'];
		//var_dump($conf);exit;
		
		if('userBlock' == $type || 'verUnpass' == $type)
			$varArr['phone'] = $conf['phone'];
			
		if( !$typeArr[$type] || !($tplId = $conf[$type] ))
			return $this->setError('模板不存在!');
		import('Org.Yunpian.YunpianMessage');
		$obj = new YunpianMessage($conf['apikey']);
		$res = $obj->tplSend($mobile, $tplId, $varArr);
		$arr = json_decode($res, true);
		if($arr['code'] != 0){
			\Think\Log::Write($res, "Err");
			return $this->setError('发送短信失败,'.$arr['msg']);
		}
		
		\Think\Log::Write($res, "Log");
		return $arr;
	}
	
	//过滤参数
	static function filter($arr, $ids, $bool = true){
		if(	MODULE_NAME == 'Home'){
			$arr = filter([$arr], $ids, $bool)[0];
		}
		return $arr;
	}
}