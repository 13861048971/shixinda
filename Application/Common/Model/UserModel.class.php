<?php
use Think\Model;

import('Org.Util.Validator');
/**
 * 用户模型
 */
class UserModel extends BaseModel{
	private $cacheKey = 'user_';
	public $statusArr = ['小黑屋','正常'];
	public $blockArr = ['多次发布违禁类容', '涉嫌邀约欺诈','其他原因'];
	public $genderArr = ['女'=>'女','男'=>'男'];
	
	protected $_validate = [
		['mobile', 'require', 	'缺少手机号码!', 1],
		['mobile', '', '此手机号已经注册过了!',0,'unique',3],
	];
	
	/**
	 * 用户登录
	 * @param string $mobile
	 * @param string $pass 
	 * @return bool
	 */
	function login($mobile, $pass){
	    
		if(!Org\Util\Validator::isMobile($mobile))
			return $this->setError('手机号格式错误!');
		
		$con['mobile'] = htmlentities($mobile);
		
		$user = $this->where($con)->find();
	    
		
		$id = $user['id'];
		
		if( !($pass1 = $this->getPass($pass) ) )
			return false;
			
		if( $pass1 != $user['password'] )
			return $this->setError('密码错误!');
			
		
		//账号被封
		if((int)$user['status'] == 0){
			return $this->setError('你的账号异常,请联系管理员!');
		}
		
		$this->save(['last_login'=>time(), 'id'=>$user['id']]);
		$user = $this->getInfo($id);
		
		session('user', $user);
		return $user;
	}
	
	/**
	 * 第三方登陆
	 * @param array $post [
	    'weixin_id'/'qq_id' => '',
		'mobile'=> 手机号, 
		'vercode'  => 
		'nickname' =>
		'avatar'   => 
		'birthday' =>
	 ]
	 * @param string $value  第三方唯一码
	 * @param string $field  字段名称
 	 */
	function login3($post){
		$fieldArr = ['weixin_id', 'qq_id'];
		foreach($fieldArr as $k=>$v){
			if( ($value = $post[$v]) && ($field = $v) )
				break;
		}
		if(!$value)
			return $this->setError('缺少参数!');
		
		$con[$field] = $value;
		$user = $this->where($con)->find();
		$id = $user['id'];
		
		//首次登陆
		if(!$id){
			$mobile = $post['mobile'];
			$vercode = $post['vercode'];
			if(!$mobile || !$vercode){
				return $this->setError('首次登陆,参数不完整!', 1301);
			}
			
			if(!Org\Util\Validator::isMobile($mobile))
				return $this->setError('手机号格式错误!',1301);
			if( $vercode != session('msgvercode') )
				return $this->setError('验证码错误!', 1301);
			
			$id = $this->where(['mobile' => $mobile])->getField('id');
			$data = [ 
				'mobile'	=> $mobile, 
				'last_login'=> time()
			];
			$id && $data['id'] = $id;
			$data[$field] = $value;
			if($id = $this->edit($data, $id)){
				$user = $this->getInfo($id);
				session('user', $user);
				return $user;
			}
			return false;
		}
		
		if( $value != $user[$field] )
			return $this->setError('第三方唯一码错误!');
		
		//账号被封
		if($user['status']){
			return $this->setError('你的账号异常!');
		}
		
		$this->save(['last_login'=>time(), 'id'=>$user['id']]);
		$user = $this->getInfo($id);
		session('user', $user);
		return $user;
	}
	
	/**
	 * 取用户关联数据 个数
	 * @param int $id 用户id
	 * @return array
	 */
	function getLinkNum($id){
		$arr['taskNum'] = d('task')->getNum($id);
		$con = ['user_id'=>$id, 'type'=>0];
		$arr['likePhoNum'] = (int)d('collect')->where($con)->count();
		$con = ['user_id'=>$id, 'type' => 1];
		$arr['likeMealNum'] = (int)d('collect')->where($con)->count();
		$map = d('order')->getCountArr(['user_id'=>$id]);
		return array_merge($arr, $map);
	}
	
	/**
	 * 重置密码
	 * @param array $post ['mobile'=>,'password'=>, 'vercode'=> ]
	 *
	 */
	function passReset($post){
		$mobile = $post['mobile'];
		$pass = $post['password'];
		$vercode = $post['vercode'];
		
		if( !($pass = $this->checkPass($pass)))
			return false;
		if( $vercode != session('msgvercode') )
			return $this->setError('验证码错误!');
		
		if(!($id = $this->where(['mobile'=>$mobile])->getField('id')))
			return $this->setError('用户不存在!');
		$data = ['password'=>$pass];
		if(!$this->edit($data, $id))
			return false;
		return $id;
	}
	
	/**
	 * 手机验证码 阿里大鱼
	 */
	function getVercode2($mobile){
		if(!Org\Util\Validator::isMobile($mobile)){
			return $this->setError('手机号码格式错误!');
		}
		
		$lastTime = session('msgLstTime');
		$t = 60;
		$t2 = time() - $lastTime;
		//60s 内只发送一次
		if($lasTime && $t2 < $t ){
			return $this->setError('60s内只能发送一次!');
		}
	
		require_once(LIB_PATH . 'Org/Top/TopClient.php');
		require_once(LIB_PATH . 'Org/Top/ResultSet.php');
		require_once(LIB_PATH . 'Org/Top/RequestCheckUtil.php');
		require_once(LIB_PATH . 'Org/Top/request/AlibabaAliqinFcSmsNumSendRequest.php');
		
		$conf = d('config')->getInfo('SMS')['value'];
		$appkey = $conf['appkey'];
		$secret = $conf['secretKey'];
		$code 	= $conf['param'];
		$templateCode = $conf['templateCode'];
		$freeSignName = $conf['freeSignName'];
		
		!$code && $code = 'code';	
		$vercode = rand(100000, 999999); session('msgvercode', $vercode);return $vercode;
		$c = new TopClient;
		$c ->appkey = $appkey;
		$c ->secretKey = $secret;
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req ->setExtend( "123456" );
		$req ->setSmsType( "normal" );
		$req ->setSmsFreeSignName( $freeSignName );
		$req ->setSmsParam( '{"code":"' . $vercode .'"}' );
		$req ->setRecNum( $mobile );
		$req ->setSmsTemplateCode( $templateCode);
		$resp = $c ->execute( $req );
		if($resp->code > 0){
			//return $this->setError('发送短信失败,错误码:' . $resp->code .';'.$resp->msg);
		}
		
		session('msgvercode', $vercode);
		session('msgLstTime', time());
		return $vercode;
	}
	
	/**
	 * 手机验证码
	 * param string $mobile
	 */
	function getVercode($mobile){
		if(!Org\Util\Validator::isMobile($mobile)){
			return $this->setError('手机号码格式错误!');
		}
		
		$conf = d('config')->getInfo('SMS')['value'];
		$appkey = $conf['appkey'];
		$secret = $conf['secretKey'];
		$code 	= $conf['param'];
		$templateCode = $conf['templateCode'];
		$freeSignName = $conf['freeSignName'];
		
		!$code && $code = 'code';
		$vercode = rand(100000, 999999); 
		if(!$this->sendMsg($mobile, ['code'=>$vercode]))
			return false;
			
		session('msgvercode', $vercode);
		session('msgLstTime', time());
		return $vercode;
	}
	
	/**
	 * 封号 or 解封
	 * @param int $id 用户id
	 * @param int $type true:封号 false:解封
	 * @param string $blockMsg 封号原因
	 * @return bool
	 */
	function block($id, $type = true, $blockNote = ''){
		if( !($user = $this->find($id)) ){
			return $this->setError('用户不存在!');
		}
		$mobile = $user['mobile'];
		//消息通知
		$d = [
			'user_id' => $id, 
			'from'    => 0, 
			'cate'    => 0 , 
		];
		if($type){
		    $d['title'] = '你的账号已经解封,已恢复正常使用.';
		    $d['content'] = $d['title'];
		}else{
		    $d['title'] = '你的账号已被系统管理封号处理,将无法登陆.';
		    $d['content'] = $d['title'].' 封号原因:'.$blockNote;
		}
		$msgMod = d('userMsg');
		$msgMod->edit($d);
		
		$data = ['id'=>$id, 'status'=> ($type?1:0), 'update_time'=>time(),'block_note'=>$blockNote];
		if($this->save($data))
			return true;
		return false;
	}
	
	/**
	 * 用户注册
	 * @param float $mobile
	 * @param string $vercode
	 * @return bool
	 */
	function regist($mobile, $pass,$vercode){
		$conf = d('config')->getInfo('SMS')['value'];
		$vercode2 = $conf['vercode'];
		
		if(!Org\Util\Validator::isMobile($mobile))
			return $this->setError('手机号格式错误!');
		if( $vercode != session('msgvercode') && ($vercode && $vercode != $vercode2))
			return $this->setError('验证码错误!');
		
		if( !($pass = $this->checkPass($pass)) )
			return false;
		
		$data = ['mobile' => $mobile, 'password' => $pass];
		$data['nickname'] = $this->greate_username(6); 
		if($id = $this->edit($data)){
			$user = $this->getInfo($id);
			session('user', $user);
			return $user;
		}
		return false;
	}
	
	//随机生成用户昵称
	function greate_username( $length = 6 ) {
	    // 密码字符集，可任意添加你需要的字符
	    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	    $name = '';
	    for ( $i = 0; $i < $length; $i++ ){
	        $name .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	    }
	    return $name;
	}
	// 调用该函数
	
	
	/**
	 * 检查密码
	 * @param string $pass
	 */
	private function checkPass($pass){
		if(!$pass)
			return $this->setError('缺少密码!');
		if( strlen($pass) < 6 )
			return $this->setError('密码要大于6个字符!');
		return self::getPass($pass);
	}
	/**图像上传
	 * @param name 文件名
	 */
	public function upload($name){
	    $path = ROOT_PATH.'/public';
	    $imagepre = '/uploads/images/';
	    $file = request()->file($name);
	    $image = $file->move($path.$imagepre);
	    $filename = $image->getSaveName();//生成头像的文件夹+文件名
	    $imagePath = $imagepre.$filename;//图片相对路径地址
	    $thumbName = $imagepre.str_replace(['/','\\'], '/thum_', $filename);
	    Image::open($file)->thumb(150, 150)->save($path.$thumbName);
	    $data = [
	        'thumb_image' =>$thumbName,//缩略图地址
	        'image' =>$imagePath//图像地址
	    ];
	    return $data;
	}
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		if($id){
			$data['update_time'] = time();
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(false !== $return){
				return $id;
			}
			$this->lastError = '修改用户信息失败!';
			return false;
		}
		
		$data['update_time'] = $data['last_login'] = $data['add_time'] = time();
		
		if(!$this->create($data))
			return false;
		
		$id = $this->add($data);
		if(!$id){
			$this->lastError = '新建用户失败!';
			return false;
		}
		return $id;
	}
	
	function profile($id){
		$info = d('userProfile')->where(['id'=>$id])->find();
		$info['work'] && $info['work'] = unserialize($info['work']);
		$info['education'] && $info['education'] = unserialize($info['education']);
		$info['birthday'] && $info['birthday'] = local_date($info['birthday'], 'Y-m-d');
		
		return $info;
	}
	
	//更新用户信息
	function updateProfile($data, $id){
		!$data['id'] && $data['id'] = $id;
		$data['birthday'] = strtotime($data['birthday']);
		
		$data['work'] && $data['work'] = serialize($data['work']);
		$data['education'] && $data['education'] = serialize($data['education']);
		
		$mod = d('userProfile');
		$mod->create($data);
		if($mod->where(['id'=>$id])->find())
			$mod->save($data);
		else
			$mod->add($data);
	
	}
	
	/**
	 * 获取加密后的密码
	 */
	static function getPass($pwd){
		$salt = '|_^^_|';
		return md5($pwd.$salt);
	}
	
	/**
	 * 用户是否存在
	 */
	function userExists($mobile, $pwd = ''){
		$data['mobile'] = $mobile;
		$pwd ? $data['password'] = self::getPass($pwd) : null;
		if($this->isExists($data))
			return true;
		return false;
	}
	/**
	 * 修改密码
	 */
	function changePwd($d){
		$id = $this->user['id'];
		if(!$id) return $this->setError('你还没有登录或者登录超时!');
		$info = $this->find($id);

		if(!$d['password'] || !$d['password_new'] || !$d['password_check'])
			return $this->setError('参数不完整!');
		
		
		if($d['password_new'] != $d['password_check'])
			return $this->setError('新密码两次输入的不一致,请重新输入!');
		
		if(self::getPass($d['password']) != $info['password'])
			return $this->setError('原密码不正确!');
		
		$data = array('password'=>self::getPass($d['password']));
		if($this->edit($data, $id)){
			session('user', $this->getInfo($id));
			return true;
		}
		return $this->setError('修改密码发生错误!');
	}
	
	/**
	 * 修改基本数据
	 * @param array $d 待修改的数据
	 * @return bool
	 */
	function modifyData($d){
		$id = $this->user['id'];
		if(!$id) 
			return $this->setError('你还没有登录或者登录超时!');
		
		$allowArr = array('email', 'last_login', 'mobile');
		$info = $this->getInfo($id);
		$data = array();
		foreach($d as $k => $v){
			if('email' == $k && !Org\Util\Validator::isEmail($v))
				return $this->setError('邮箱格式错误!');
			
			if('mobile' == $k && !Org\Util\Validator::isMobile($v))
				return $this->setError('手机号格式错误!');
			
			if($info[$k] == $v)
				continue;
			
			if(in_array($k, $allowArr))
				$data[$k] = $v;
		}
		if(!$data) 
			return $this->setError('缺少修改的数据或者没有权限修改!');
		
		if($this->edit($data, $id))
			return true;
		
		return $this->setError('修改数据失败!');
	}
	
	function getUserInfo($mobile){
		return $this->getInfo($this->getUserId($mobile));
	}
	
	/**
	 * 
	 * @param int $id 根据用户id获取楼主信息
	 */
	function getUser($id){
	   return $this->where(['id'=>$id])->find();
	}
	/**
	 * 查找用户id
	 */
	function getUserId($mobile){
		return $this->where('mobile="'.$mobile.'"')->getField('id');
	}
	
	function getPageList($arr, $fields = 'id', $order="id desc", $limit = 15){
		$username = trim($arr['name']);		
		if($username){
			unset($arr['name']);
			$arr['name'] = array('like', "%{$username}%");
		}
		if(isset($arr['status']) && '0' === $arr['status'])
			$arr['status'] = ['lt', 1];
		$data = parent::getPageList($arr, $fields, $order, $limit);
		foreach($data['list'] as $k=>$v){
			$data['list'][$k] = $this->getInfo($v['id']);
			$data['list'][$k]['avatar'] = getImage($v['avatar']);
		}

		return $data;
	}
	
	function getList($con){
		$list = $this->where($con)->select();
		foreach($list as $v){
			$users[$v['id']] = $v;
		}
		return $users;
	}
	
	/**
	 * 写缓存方法 
	 */
	function _cacheUserInfo($id){
		$info = $this->find($id);
		if(!$info) return;
		
		$info['addTime'] = local_date($info['add_time']);
		$info['updateTime'] = local_date($info['update_time']);
		$info['lastLogin'] = local_date($info['last_login']);
        !$info['avatar'] ? ($info['preAvatar'] = "http://qiniu.img.mallshangyun.com"):($info['preAvatar'] = getImage($info['avatar'], -1));
		unset($info['password']);
		
		return $info;
	}
	
	//详情
	function getInfo($id){
		$info = $this->_cacheUserInfo($id);
		if(!$info) return;
 		return $info;
	}
	
	/**
	 * 添加积分
	 * @param int $uid
	 * @param int $point
	 * @param int $taskId
	 * @param string $note
	 */
	function addPoint($uid, $point, $taskId=0, $note=''){
		if(!$uid || !$point){
			$this->lastError = '缺少参数';
			return false;
		}
		
		$info = $this->getInfo($uid);
		$amount = $info['point'] + $point;
		
		$data = array(
			'uid'     =>(int)$uid,
			'point'	  =>(int)$point,
			'task_id' =>(int)$taskId,
			'note'	  => $note,
			'amount'  => $amount
		);
		$data['add_time'] = $data['update_time'] = time();
		//开始事务
		$this->startTrans();
		if(d('userPoint')->data($data)->add() && 
		   $this->where('id='.$uid)->save(array('point'=>$amount))){
			$this->_cacheReset($uid);
			$this->commit();
			return true;
		}
		$this->rollback();
		$this->lastError = 'add point error!';
		return false;
	}
	
	//重置缓存
	function _cacheReset($id){
		return $this->resetCache($this->cacheKey.$id, 'UserInfo', $id);
	}
	
	//获取个人信息
	public function getPerson($id){
	    $person = $this->where(['id'=>$id])->find();
	    return $this->parseRow($person);
	}
	
	//把秒转换成天数，小时数和分钟
	public function secsToStr($secs) {
	    if($secs>=86400){$days=floor($secs/86400);
	    $secs=$secs%86400;
	    $r=$days.' 天';
	    if($secs>0){$r.='';}}
	    if($secs>=3600){$hours=floor($secs/3600);
	    $secs=$secs%3600;
	    $r.=$hours.' 时';
	    if($secs>0){$r.='';}}
	    if($secs>=60){$minutes=floor($secs/60);
	    $secs=$secs%60;
	    $r.=$minutes.' 分';
	    if($secs>0){$r.='';}}
	    $r.=$secs.' 秒';
	    return $r;
	}
	//格式化行
	public function parseRow($v){
	    $v['commentNum'] = d('postComment')->where(['user_id'=>$v['id']])->Count();//回帖数
	    $v['postNum'] = d('post')->where(['user_id'=>$v['id']])->Count(); //发主帖数
	    $v['zanNum'] = d('support')->where(['support'=>(int)1,'user_id'=>$v['id']])->count();//点赞数
	    $vlastPost = d('post')->where(['user_id'=>$v['id']])->order('add_time desc')->limit(1)->find();//最后发的帖子
	    if($v['last_login'] > $v['last_logout'])
	        $online = time()-$v['last_login'];
        if($v['last_login'] <= $v['last_logout'])
	       $online = 0;
        
 	    $v['online_time'] = $this->secsToStr($v['online_time'] + $online);//在线时间
 	    $v['last_post_time'] = date('Y-m-d H:i:s',$vlastPost['add_time']);//最后发帖时间
	    $v['last_login'] = date("Y-m-d H:i:s",$v['last_login']);//最后一次登录时间
	    $v['last_logout'] = date("Y-m-d H:i:s",$v['last_logout']);//最后退出时间
	    $v['add_time'] = date("Y-m-d H:i:s",$v['add_time']);//注册时间
	    if(MODULE_NAME == 'Home'){
	        $v['preAvatar'] = getImage($v['avatar'], -1);
	    }
	    return $v ;
	}
}