<?php
use Think\Model;
/**
 * 赞
 */
class FeedbackModel extends BaseModel {
	public $cacheKey  = 'feedback_';
	
	function __construct(){
		parent::__construct();
	}
	
	function setValidate($data){
		$this->_validate = [
			['user_id', 'require', '缺少用户id!', 1],
			['desc', 'require', '缺少内容!', 1],
		];
		
		return $data;
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data,$id){
		$data = $this->setValidate($data, $id);
		
		if($id){
			$data['update_time'] = time();
			$data['id'] = $id;
			if(!$this->create($data)) 
				return false;
			if(false === $this->where(['id'=>$id])->save($data)){
				$this->lastError = '修改失败!';
				return false;
			}
			return $id;
		}
		$data['add_time'] = $data['update_time'] = time();
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
		$info['addTime'] 	= local_date($info['add_time']);
		$info['updateTime'] = local_date($info['update_time']);
		
		$userMod = d('user');
		$user = $userMod->getInfo($info['user_id']);
		$info['user'] = $user;
		$info['nickname'] = $user['nickname'];
		$info['avatar'] = $user['avatar'];
		$info['mobile'] = $user['mobile'];
		$info['sexName'] = $user['sexName'];
		return $info;
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
		$data = parent::getPageList($con, $fields, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$data['list'][$k] = $v;
		}
	
		return $data;
	}
	
}