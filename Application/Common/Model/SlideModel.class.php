<?php
use Think\Model;
/**
 * 轮播图
 */
class SlideModel extends BaseModel {
	public $cacheKey  = 'slide_';
	public $typeArr = [1 => '任务','套餐','摄影师','链接'];
	
	protected $_validate;
	
	function __construct(){
		parent::__construct();
		
		$this->_validate = [
			['image', 'require', 	'缺少图片', 1],
		];
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		!$data['type'] && ($data['type'] =1);
		
		if($data['type'] < 4 && !$data['node_id'])
			return $this->setError('缺少节点id');
		
		if($data['type'] == 4 && 
			(!$data['link'] || false === strpos($data['link'], 'http') ))
			return $this->setError('请填写合适的链接地址!');
		
		if($id){
			$data['update_time'] = time();
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(!$return){
				$this->lastError = '修改失败!';
				return false;
			}
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data)) 
			return false;
		if(!($id = $this->add()))
			return $this->setError('添加失败!');

		return $id;
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
	
		$info['typeName'] = $this->typeArr[$info['type']];
		$info['addTime'] = local_date($info['add_time']);
		$info['updateTime'] = local_date($info['update_time']);
		
		if(1 == $info['type']){
			$node = d('task')->getInfo($info['node_id']);
			$info['task'] = $node;
		}
		if(2 == $info['type']){
			$node = d('meal')->getInfo($info['node_id']);
			$info['meal'] = $node;
		}
		if(3 == $info['type']){
			$node = d('pho')->getInfo($info['node_id']);
			$info['pho'] = $node;
		}
		$info['node'] = $node;
		return $info;
	}
	
	/**
	 * @param array $con
	 * @return array
	 **/
	public function getList($con, $limit = 50, $order = 'rank'){
		$list = $this->where($con)->field('id')->limit($limit)->order($order)->select();
		foreach($list as $k=>$v){
			$list[$k] = $this->getInfo($v['id']);
		}
		return $list;
	}
	
	function getPageList($con, $fields = 'id', $order = 'rank', $perNum = 15){
		$data = parent::getPageList($con, $fields, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$data['list'][$k] = $v;
		}
	
		return $data;
	}
}