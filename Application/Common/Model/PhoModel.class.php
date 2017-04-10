<?php
use Think\Model;
/**
 * 摄影师
 */
class PhoModel extends BaseModel {
	public $cacheKey  = 'pho_';
	public $goodAtArr;
	public $statusArr = ['待审核','已认证','认证失败','取消资格'];
	public $sortArr = ['综合排序','销量最多','评价最高'];
	public $orderArr = [1=>'verify_time desc', 'receive_num desc', 'last_login desc'];
	public $unpassArr = ['身份信息不一致','身份照片不清楚','其他原因'];
	public $typeArr = ['个人','机构'];
	public $cityArr;
	
	protected $_isUpdate = false;
	protected $_validate;
	
	function __construct(){
		parent::__construct();
		$this->goodAtArr = d('task')->typeArr;
		$this->cityArr = filter(d('category')->getList(null, 2), 'id,name');
	}
	
	function setValidate($data, $id){
		$this->_isUpdate = $id;
		$this->_validate = [
			['user_id', 'checkUser', '用户不存在!', 1, 'callback'],
			['realname', 'require', '缺少真实名称!', 1],
			['idno', 	'require', 	'缺少身份证号!', 1],
			['idno', 	'checkIdno','身份证号错误!', 1,'callback'],
			['alipay', 	'require', 	'缺少支付宝账号!', 1],
			['image2', 'require', '缺少身份证正面图片!', 1],
			
		];
		if($data['type']){
			$this->_validate[] = ['orgname', 'require', '缺少机构名称!', 1];
			$this->_validate[] = ['orgaddress', 'require', '缺少机构地址!', 1];
		}else{
			$this->_validate[] = ['image3', 'require', '缺少身份证反面图片!', 1];
		}
		
		if($data['region'] && is_array($data['region'])){
			$data['region'] = trim(implode(',', $data['region']));
		}
		
		if(!$id && $data['worklink']){
			if(strpos($data['worklink'], 'http') === false)
				return $this->setError('作品链接缺少http://');
		}
		
		!$data['service_city'] && $data['service_city'] = $data['city'];
		$data['status'] == 1 && $data['verify_time'] = time();
		return $data;
	}
	
	function checkUser($uid, $type){
		if(!$uid) return false;
		if(!d('User')->getInfo($uid)) 
			return false;
		
		//更新不检查
		if($this->_isUpdate) return;
		$con['user_id'] = $uid;
		if($this->where($con)->getField('id'))
			return $this->setError('认证已申请了!');
	}
	
	//更新摄影师的统计信息， 销量,评价, meal_update_time
	function updateCount($id){
		$mod = d('order');
		$con = ['pho_id'=>$id,'status'=>['in', [3,4]]];
		$d['sales'] = $mod->where($con)->count();
		$d['receive_num'] = $d['sales'];
		$d['star'] = $mod->where($con)->getField('avg(star)');
		$d['id'] = $id;
		$this->data($d)->save();
	}
	
	//更新套餐更新时间
	function updateMealUpdate($id, $time){
		$d['meal_update_time'] = $time;
		$d['id'] = $id;
		$this->data($d)->save();
	}
	
	//检测身份证
	function checkIdno($idno){
		$msg = isIdCardNo($idno);
		if($msg['status'])
			return true;
		return $this->setError($msg['msg']);
	}
	
	/**
	 * 编辑or添加类目
	 */
	function edit($data, $id=null){	
		$data = $this->setValidate($data, $id);
		
		if($id){
			$data['update_time'] = time();
			$data['id'] = $id;
			if(!$this->create($data)) 
				return false;
			if(false === $this->save()){
				$this->lastError = '修改失败!';
				return false;
			}
			d('album')->editAlbum($data['image'], $id);
			return $id;
		}
		$data['id']  = $data['user_id'];
		$data['add_time'] = $data['update_time'] = time();
		if(!$this->create($data)) 
			return false;
		if(!($id = $this->add()))
			return $this->setError('添加失败!');
		d('album')->editAlbum($data['image'], $id);
		return $id;
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
		
		$cateMod = d('category');
		$info['updateTime'] = local_date($info['update_time'], 'Y-m-d H:i');
		$info['addTime'] 	= local_date($info['add_time'], 'Y-m-d H:i');
		$info['verifyTime'] 	= local_date($info['verify_time'], 'Y-m-d H:i');
		$info['user'] = d('user')->getInfo($info['user_id']);
		$info['nickname'] = $info['user']['nickname'];
		$info['avatar'] = $info['user']['avatar'];
		$info['mobile'] = $info['user']['mobile'];
		$info['statusName'] = $this->statusArr[$info['status']];
		$info['cityName'] = d('region')->getName($info['city']);
		$info['serviceCityName'] = d('region')->getName($info['service_city']);
		$info['typeName'] = $this->typeArr[$info['type']];
		$info['inviteeNum'] = $info['receive_num'] = $info['sales'];
		$info['lastLogin'] = $info['user']['lastLogin'];
		$info['images'] = [];
		$imgList = d('album')->getList(['type_id'=> $id]);
		$info['imageList'] = $imgList;
		$info['price'] = d('meal')->where(['pho_id'=>$id,'status'=>0])->order('price asc')->find()['price'];
		foreach($imgList as $v){
			$info['images'][] = $v['path'];
		}
		$collectMod = d('collect');
		$info['attentionedNum'] = $collectMod->getNum($id); //被关注
		$info['isAttentioned'] = $collectMod->isCollect($id); //当前用户是否关注
		
		if('Home' == MODULE_NAME){
			$info['user'] = filter([$info['user']], 'id,nickname,sex,avatar');
			$info['user'] = $info['user'][0];
			$info = filter([$info], 'imageList,', false);
			$info = $info[0];
		}
		$info['shareUrl'] = '/phoShare/'.$id;
		!$info['pho_bg'] && $info['pho_bg'] = '/Public/images/artist_bg.jpg';
		$info['reportNum'] = (int)d('report')->getNum(['pho_id'=>$id]);
		if(false === strpos($info['worklink'], 'http') )
			$info['worklink'] = 'http://'.$info['worklink'];
		
		return $info;
	}
	
	/**
	 * 艺人状态变更
	 */
	public function change($id, $status, $verify_note = ''){
		$row = $this->find($id);
		$user = d('user')->getInfo($id);
		if(!$row || !$user)
			return $this->setError('记录不存在!');
		if($status < 1 || !$this->statusArr[$status])
			return $this->setError('不可变更的状态!');
		
		$mobile = $user['mobile'];
		
		$mType = $type ? 'userBlock':'userUnblock';
		if($status >0 && $status < 3){
			$arr['type'] = $row['type'] ? '艺人':'实名';
			$mType = 'verPass';
			if($status == 2){ 
				$arr['reason'] = $verify_note;
				$mType = 'verUnpass';
			}
		}
		
		$data = ['id'=>$id, 'status'=> $status,'update_time'=>time(),'verify_note'=>$verify_note];
		in_array(status,[1,2]) && $data['verify_time'] =  time();
		if($this->save($data))
			return true;
		return false;
	}
	
	/**
	 * 
	 * @param array $con      类型,默认是产品分类
	 * @return array
	 **/
	public function getList($con = null, $limit = 10, $field = 'id', $order='id desc'){
		$list = $this->where($con)->limit($limit)
			->field($field)->order($order)->select();

		foreach($list as $k=>&$v){
			$v = $this->getInfo($v['id']);
		}
		return $list;
	}
	
	//用户是不是艺人
	public function isPho($id){
		$row = $this->find($id);
		
		if($row['status'] != 1 || $row['type'] < 1)
			return false;
		return true;
	}
	
	//热门关键字
	static function hotKeywords($words = []){
		$key = 'offer_hot_words';
		$arr = session($key);
		!$arr && $arr = [];
		if(!$words) return $arr;
		$new = array_diff($words, $arr);
		
		$new && $arr = array_slice(array_merge($new, $arr),0, 10);
		session($key, $arr);
		
		return $arr;
	}
	
	public function getPageList($con, $fields = 'id',$order = 'id desc', $perNum = 15){
		//keywords
		if($words = $con['keywords']){
			$con2 = ['mobile' => ['like', '%' . $words . '%']];
			$uid = d('user')->where($con2)->getField('id');
			if($uid){
				$con['user_id'] = $uid;	
			}
			if(!$uid){
				$con2 = ['nickname' => ['like', '%' . $words . '%']];
				$uid = d('user')->where($con2)->getField('id');
				$con['user_id'] = $uid;
			}
		}
		
		if($con['city']){
			$con['service_city'] = $con['city'];
			unset($con['city']);
		} 
		
		$data = parent::getPageList($con, $fields, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$data['list'][$k] = $this->getInfo($v['id']);
			$ids[] = $v['id'];
		}
		
		if(!$ids) return $data;
		return $data;
	}
}