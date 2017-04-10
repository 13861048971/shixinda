<?php
use Think\Model;
class AdminModel extends BaseModel {
	/**
	 * 是否存在符合条件的管理
	 * @param array $arr
	 */
	
	public function exists($arr){
		$id = $this->where($arr)->getField('id');
		if(!$id) return false;
		return $this->getInfo($id);
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		$data['password'] && $data['password'] = $this->getPass($data['password']);
		$data['actions'] && $data['actions'] = serialize($data['actions']);
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
	
	function lastLogin($id){
		$data = ['id'=>$id, 'last_login'=>time()]; 
		$this->save($data);
	}
	
	//生成密码
	public function getPass($passRaw){
		return md5($passRaw);
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
		if($info['role_id']){
			$role = d('adminRole')->getInfo($info['role_id']);
			$info['actions'] = $role['actions'];
			$info['roleName'] = $role['name'];
		}
		$info['updateTime'] = local_date($info['update_time']);
		$info['lastLogin'] = local_date($info['last_login']);
		return $info;
	}
	
	/**
	 * @param array $con
	 * @return array
	 **/
	public function getList($con, $limit = 50, $order = 'id asc'){
		$list = $this->where($con)->field('id')->limit($limit)->order($order)->select();
		foreach($list as $k=>$v){
			$list[$k] = $this->getInfo($v['id']);
		}
		return $list;
	}
	
	function getPageList($con=[], $fields = 'id', $order = '', $perNum = 15){
		$data = parent::getPageList($con, $fields, $order, $perNum);

		foreach($data['list'] as $k=>$v){
			$data['list'][$k] = $this->getInfo($v['id']);
		}
		return $data;
	}
}

