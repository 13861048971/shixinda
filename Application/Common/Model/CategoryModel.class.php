<?php
use Think\Model;
/**
 * 类别
 */
class CategoryModel extends BaseModel {
	public $cacheKey  = 'category_';
	public $statusArr = [0 => '关闭',	  1 => '启用' ];
	public $typeArr   = [1 => '艺术类型', 2 => '地区'];
	
	protected $_validate;
	
	function __construct(){
		parent::__construct();
		
		$this->_validate = [
			['name', 'require', '缺少名称!'],
			['type', [1,2,3], '缺少类型!', 1, 'in'],
		];
	}
	
	/**
	 * 编辑or添加类目
	 */
	function edit($data, $id=null){
		!$data['type'] && ($data['type'] =1);
		
		if(1 == $type && $data['type_id'])
			return $this->setError('缺少类型id');
		
		if($id){
			$data['update_time'] = time();
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(!$return){
				$this->lastError = '修改类目失败!';
				return false;
			}
			$this->updatePath($id);
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data)) 
			return false;
		if(!($id = $this->add()))
			return $this->setError('添加类目失败!');
		$this->updatePath($id);
		return $id;
	}
	
	/**
	 * 添加多个类别,从父类开始
	 * @param array $cates
	 * @param int $goods_type
	 * @param int $deep 要添加类别的深度 
	 * @return $pid 最后添加的类别id 
	 */
	function addCates($cates, $goods_type, $deep = 3, $type = 1){
		if(!$cates) return false;
		$mod = d('category');
		$cate_id = 0;
		$pid = 0;
		foreach($cates as $k=>$v){
			if($k > $deep - 1) break;
			
			$cate = $cates[$k] = trim($v);
			$con = ['name'=>$cate, 'parent_id' => $pid];
			$rows = $this->getList(null, 1, $con);
			if(!$rows){
				$data = [
					'parent_id' => $pid,
					'name'		=> $cate,
					'type'		=> 1,
					'type_id'	=> $goods_type,
					'status'	=> 1
				];
				
				if(!($pid = $this->edit($data)))
					return false;
				continue;
			}
			$pid = $rows[0]['id'];
		}
		
		return $pid;
	}
	
	public function getNames($ids){
		$arr = array_filter(explode(',', $ids));
		if(!$arr) return;
		
		$con = ['id' => ['in', $arr]  ];
		$nameArr = $this->where($con)->getField('name', true);
		return implode(',', $nameArr);
	}
	
	/** 
	 * 更新路径
	 **/
	public function updatePath($id){
		$info = $this->getInfo($id);
		$path = $id;
		if($info['parent_id']){
			$pInfo = $this->getInfo($info['parent_id']);
			$path = $pInfo['path'] . '/' . $path;
		}
		
		if($path == $info['path'])
			return;
		
		return $this->data(['path' => $path ,'id'=>$id])->save();
	}
	
	//获取顶级类目信息
	public function getTopCate($path){
		$pid = (int)explode('/',$path)[0];
		if(!$pid) return;
		return $this->getInfo($pid);
	}
	
	public function getParents($id, $type = 1){
		$cate = $this->getInfo($id);
		$paths = explode('/', $cate['path']);
		if($paths[0] == $id) 
			return [$cate];
		$con = ['id' => ['in', $paths]];
		return $this->getList(null, $type, $con);
	}
	
	public function getPids($id){
		$cate = $this->getInfo($id);
		$paths = explode('/', $cate['path']);
		return $paths;
	}
	
	public function getInfo($id){
		$v = $this->find($id);
		if(!$v) return;
		$v['typeName'] 		= $this->typeArr[$v['type']];
		$v['addTime']  		= local_date($v['add_time']);
		$v['updateTime']  	= local_date($v['updateTime']);
		$v['deepArr']   = explode('/', $v['path']);
		$v['deep'] = $n	= count($v['deepArr']);
		$v['name2'] 		= str_repeat('&emsp;', $n-1). $v['name'];
		
		return $v;
	}
	
	/**
	 * 取子类,本地不存在连接远程
	 * @param int $parent_id
	 * @param int $type      类型,默认是产品分类
	 * @return array
	 **/
	public function getList($parent_id = null, $type = 1, $con = []){
		isset($parent_id) && $con['parent_id'] = $parent_id;
		$con['type']	  = $type;
		$list = $this->where($con)->field('id')->order('rank,id')->select();
		foreach($list as $k=>$v){
			$info = $this->getInfo($v['id']);
			$arr[$info['parent_id']][] = $info;
		}
		ksort($arr);
		$n = count($arr);
		$arr2 = $arr[0];
		unset($arr[0]);
		foreach($arr as $k=>$v){
			$key = arr2Search($arr2, 'id', $k);
			if(false === $k){
				break;
			}
			array_splice($arr2, $key+1, 0, $v);
		}
		
		return $arr2;
	}
}