<?php
use Think\Model;
/**
 * 管理员角色
 */
class AdminRoleModel extends BaseModel {
	public $cacheKey  = 'admin_role_';
	function __construct(){
		parent::__construct();
		$this->_validate = [
			['name', 	 'require', 	'缺少角色名称!', 1],
			['actions',  'require', 	'缺少权限!', 	1]
		];
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
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
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
		$info['actions'] && $info['actions'] = unserialize($info['actions']);
		return $info;
	}
	
	/**
	 * @param array $con
	 * @return array
	 **/
	public function getList($con=[], $limit = 50, $order = 'id asc'){
		$list = $this->where($con)->field('id')->limit($limit)->order($order)->select();
		foreach($list as $k=>$v){
			$list[$k] = $this->getInfo($v['id']);
		}
		return $list;
	}
	
	function getPageList($con = [], $fields = 'id', $order = '', $perNum = 15){
		$data = parent::getPageList($con, $fields, $order, $perNum);

		foreach($data['list'] as $k=>$v){
			$data['list'][$k] = $this->getInfo($v['id']);
		}
		return $data;
	}
	
	/**
	 * 所以动作列表
	 * @param array $actions 选中的动作 
	 * @param bool  $selected 是否值保留选中的
	 */
	function getActionList($actions = [], $selected = false){
		$ctrList = c('ctrs');
		$actionList = c('actions');
		$nav = c('nav');
		
		$i = 0;
		foreach($ctrList as $k=>$v){
			$checked = true;
			$acts = [];
			foreach($actionList[$k] as $k2=>$v2){
				$actName = strtolower($k.'/'. $v2[0]);
				$actionList[$k][$k2][3] = $actName;
				$actionList[$k][$k2][4] = $i;
				if(in_array($actName, $actions)){
					$actionList[$k][$k2][2] = true;
					$acts[] = $v2[0];
				}else{
					$checked = false;
					if($selected)
						unset($actionList[$k][$k2]);
				}
				$i++;
			}
			
			if($selected && empty($actionList[$k])){
				unset($ctrList);
				continue;
			}
			
			$firstAct = $actionList[$k][0];
			$actName = strtolower($k . '/' . $firstAct[0]);
			$ctrList[$k] = [
				'name' 		 => $v,
				'checked'	 => $checked,
				'actName'	 => $actName,
				'acts'		 => $acts,
				'actionList' => $actionList[$k],
				'leftNav'	 => $nav[$k],
			];
		}
		
		return $ctrList;
	}
	
}