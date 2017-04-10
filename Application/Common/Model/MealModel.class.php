<?php
use Think\Model;
/**
 * 套餐
 */
class MealModel extends BaseModel {
	public $cacheKey  = 'meal_';
	public $statusArr = ['发布中','被屏蔽','被删除'];
	public $typeArr;
	public $sceneArr = [1=>'棚拍','外景'];
	public $afterArr = [1=>'原片','简修','精修'];
	public $makeupArr = ['不提供','提供'];
	public $cityArr;
	
	function __construct(){
		parent::__construct();
		$this->cityArr = d('category')->getList(null, 2);
		$this->typeArr = d('task')->typeArr;
	}
	
	function setValidate($data, $id){
		$this->_validate = [
			['pho_id', 'require', 		'缺少摄影师id!', 1],
			['title', 	 'require', 	'缺少标题!', 1],
			['type', 	 'require', 	'缺少拍摄类型!', 1],
			['scene', 	 'require', 	'缺少场景!', 1],
			['after', 	 'require', 	'缺少后期!', 1],
			['num', 	 'require', 	'缺少起拍个数!', 1],
			['note', 	 'require', 	'缺少特色说明!', 1],
			['price', 	 'require', 	'缺少出价!', 1],
		];
		
		if(!$data['images']){
			return $this->setError('缺少实物照片!');
		}
		if($id){
			$info = $this->find($id);
			
			if(!$info) 
				return $this->setError('订单不存在!');
			$data = array_merge($info, $data);
		}
		return $data;
	}
	
	/**
	 * 编辑or添加
	 */
	function edit($data, $id=null){
		$data = $this->setValidate($data, $id);
		if($id){
			$data['update_time'] = time();
			$return  = $this->data($data)->where('id=' . (int)$id)->save();
			if(false === $return){
				$this->lastError = '修改失败!';
				return false;
			}
			d('album')->editAlbum([$data['images']], $id,0,1);
			//更新接单数
			if($data['status'] == 1){
				
			}
			d('pho')->updateMealUpdate($data['pho_id'], $data['update_time']);
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data))
			return false;

		if(!($id = $this->add())){
			return $this->setError('添加失败!');
		}
		d('pho')->updateMealUpdate($data['pho_id'], $data['update_time']);
		d('album')->editAlbum([$data['images']], $id, 0,1);
		return $id;
	}
	
	/**
	 * 套餐数
	 * @param int $phoId;
	 * @param int $status
	 * @param int
	 */
	function getNum($phoId, $status=null){
		isset($status) && $con['status'] = $status;
		$con['pho_id'] = $phoId;
		return (int)$this->where($con)->count();
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
	
		$info['typeName'] 	= $this->typeArr[$info['type']];
		$info['sceneName'] 	= $this->sceneArr[$info['scene']];
		$info['afterName'] 	= $this->afterArr[$info['after']];
		$info['statusName'] = $this->statusArr[$info['status']];
		$info['addTime'] 	= local_date($info['add_time']);
		$info['updateTime'] = local_date($info['update_time']);
		$info['cityName']   = d('region')->getName($info['city']);
		$info['tagArr']   = explode(',', $info['tag']);
		$info['pho'] = d('pho')->getInfo($info['pho_id']);
		$img = d('album')->getList(['type'=>1, 'type_id'=>$id]);
		foreach($img as $v){
			$images[] = $v['path'];
		}
		$info['imageList'] = $img;
		$info['images'] = $images;
		$collectMod  = d('collect');
		$info['likeNum'] = $collectMod->getNum($id, 1); 		//被关注数
		$info['isLike'] = $collectMod->isCollect($id, null, 1); //当前用户是否关注
		$info['shareUrl'] = '/mealShare/'.$id;
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
		if($nickname = trim($con['nickname'])){
			$mod = d('user');
			$subSql = $mod->where(['nickname'=> ['like', '%'.$nickname.'%']])
				->field('id')->select(false);
			$con['_string'] = ' pho_id in'. $subSql;
		}
		
		$data = parent::getPageList($con, $fields, $order, $perNum);

		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$data['list'][$k] = $v;
		}
	
		return $data;
	}
	
}