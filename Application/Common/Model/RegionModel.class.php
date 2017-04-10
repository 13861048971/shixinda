<?php
use Think\Model;
/**
 * 地区类别
 */
class RegionModel extends BaseModel {
	public $cacheKey  = 'category_';
	public $statusArr = [0 => '关闭',	  1 => '启用' ];
	public $regionTypeArr   = ['国家',"省份","城市","县区"];
	
	protected $_validate;
	
	function __construct(){
		parent::__construct();
		
		$this->_validate = [
			['name', 'require', '缺少名称!'],
			['type', [1,2], '缺少类型!', 1, 'in'],
		];
	}
	
	/**
	 * 取地区名称
	 */
	public function getName($id){
		return $this->find($id)['region_name'];
	}
	
	/**
	 * 编辑or添加类目
	 */
	function edit($data, $id=null){
		!$data['type'] && ($data['type'] =1);
		
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
		return $info;
	}
	
	/**
	 * 取列表
	 * @param int $parent_id
	 * @param int $type      类型,默认是产品分类
	 * @return array
	 **/
	public function getList($con){
		$list = $this->where($con)->order('region_type')->select();
		return $list;
	}
}