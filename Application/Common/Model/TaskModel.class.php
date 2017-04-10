<?php
use Think\Model;
/**
 * 任务
 */
class TaskModel extends BaseModel {
	public $cacheKey  = 'task_';
	public $statusArr = ['招标中', '已结束','已取消','已过期'];
	public $typeArr = [1=>'服装','鞋帽','美食','美妆','内衣','箱包','配饰','家居百货','花鸟绿植','数码家电','其它','室外'];
	public $styleArr = [1=>'绝色平拍','静物搭配','模特棚拍','模特外景'];
	public $sortArr   = ["默认排序","出价排序","火热排序"];
	public $cityArr;
	
	function __construct(){
		parent::__construct();
		$this->cityArr = d('category')->getList(null, 1);
	}
	
	function setValidate($data, $id){
		$this->_validate = [
			['user_id', 'require', 	'缺少用户id!', 1],
			['title', 	 'require', 	'缺少标题!', 1],
			['type', 	 'require', 	'缺少拍摄类型!', 1],
			['begin_time', 	 'require', '缺少约拍时间!', 1],
			['over_time', 	 'require', '缺少交片时间!', 1],
			['city', 	 'require', 	'缺少所在城市!', 1],
			['num', 	 'require', 	'缺少件数!', 1],
			['note', 	 'require', 	'缺少拍摄要求!', 1],
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
			
			//接单
			if($data['status'] == 1){
				$data['receive_time'] = time();
			}
			$data['refuse_type'] > 1 && $data['refuse_time'] = time();
			$data['report_type'] == 1 && $data['report_time'] = time();
			$data['status'] == 3 && $data['cancel_time'] = time();
		}
		return $data;
	}
	
	//举报
	function report($id, $type, $note){
		if(!$type || !$this->reportTypeArr[$type])
			return $this->setError('请选择举报类型!');
		
		if(!$note)
			return $this->setError('请填写举报备注说明!');
		
		$data['report_type'] = $type;
		$data['report_note'] = $note;
		$data['status'] = 4;
		return $this->edit($data, $id);
	}
	
	//取消任务
	function cancel($post){
		$id = (int)$post['id'];
		if(!$id || !($task = $this->getInfo($id)))
			return $this->setError('任务不存在!');
		if(MODULE_NAME == 'Home' && $task['user_id'] != $this->user['id'])
			return $this->setError('没有权限!');
		if(1 == $task['status'])
			return $this->setError('任务已结束!');
		
		if(2 == $task['status'])
			return true;
		
		$task['status'] = 2;
		if($this->edit($task, $id))
			return true;
		return false;
	}
	
	/**
	 * 当前用户是否 已投标
	 * @param int $id 任务id
	 * @return bool
	 */
	function isJoin($id){
		if(!($uid = $this->user['id']))
			return false;
		$con = ['user_id' => $uid, 'task_id'=>$id];
		$mod = d('join');
		if($mod->where($con)->getField('id'))
			return true;
		return false;
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
			d('album')->editAlbum([$data['images']], $id);
			//更新接单数
			if($data['status'] == 1){
				
			}
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data))
			return false;

		if(!($id = $this->add())){
			return $this->setError('添加失败!');
		}
		d('album')->editAlbum([$data['images']], $id);
		return $id;
	}
	
	/**
	 * 任务数
	 * @param int $uid;
	 * @param int $status
	 * @param int
	 */
	function getNum($uid, $status=null){
		isset($status) && $con['status'] = $status;
		$con['user_id'] = $uid;
		return (int)$this->where($con)->count();
	}
	//订单统计
	public function countOrder($con = []){
		$list = $this->where($con)->group('status')
			->field('count(*) as num, status')->select();
		$arr = ['status0'=>0, 'status1'=>0, 'status2'=>0,'status3'=>0, 'all'=>0];
		foreach($list as $v){
			$arr['status'.$v['status']] = $v['num'];
			$arr['all'] += $v['num'];
		}
		$arr['refuse'] = (int)$this->where($con)->where('refuse_type > 0')->getField('count(*)');
		$arr['report'] = (int)$this->where($con)->where('report_type > 0')->getField('count(*)');
		return $arr;
	}
	
	public function getInfo($id){
		$info = $this->find($id);
		if(!$info) return;
		!$info['style'] && $info['style'] = 1;
		$info['typeName'] 	= $this->typeArr[$info['type']];
		$info['statusName'] = $this->statusArr[$info['status']];
		$info['styleName']  = $this->styleArr[$info['style']];
		$info['style_img']  = '/Public/images/task-style'. $info['style'].'.jpg';
		$info['addTime'] 	= local_date($info['add_time']);
		$info['updateTime'] = local_date($info['update_time']);
		$info['beginTime']  = local_date($info['begin_time']);
		$info['overTime']   = local_date($info['over_time']);
		$info['cityName']   = d('region')->getName($info['city']);
		$info['joinNum']  = d('join')->getNum($id);
		$img = d('album')->getList(['type'=>0, 'type_id'=>$id]);
		foreach($img as $v){
			$images[] = $v['path'];
		}
		$info['imageList'] = $img;
		$info['images'] = $images;
		$info['taskImg'] = $images[0];
		if( ($img = getImage($images[0])) && is_file(ROOT_PATH . $img) )
			$info['taskImg'] = $img;
		
		$userMod = d('user');
		$user = $userMod->getInfo($info['user_id']);
		$info['user'] = $user;
		$info['shareUrl'] = '/taskShare/'.$id;
		$info = self::filter($info, 'imageList', false);
		
		if($info['status'] < 1 &&  $info['begin_time'] < time()){
			$info['statusName'] = '已过期';
			$info['status'] = 3;
		}
		
		if(ACTION_NAME == 'taskDetail')
			$info['isJoin'] = $this->isJoin($id);
		return $info;
	}
	
	//投标
	public function join($post){
		$id = (int)$post['task_id'];
		$task = $this->getInfo($id);
		if(!$id || !$task)
			return $this->setError('投标任务不存在!');
		
		if($task['status'] > 0)
			return $this->setError('任务已停止招标!');
		
		if( time() > $task['over_time'] )
			return $this->setError('任务已过期!');
		$mod = d('join');
		!$post['user_id'] && $post['user_id'] = $this->user['id'];
		
		if($joinId = $mod->edit($post)){
			$d = [
				'cate'      => 1,
				'node_id'	=> $joinId, 
				'from'		=> $post['user_id'], 
				'user_id'	=> $task['user_id'],
				'title' 	=> '发起了一个投标 金额: ￥'. ($post['price']*$task['num']),
			];
			$d['content'] = $d['title'];
			d('userMsg')->edit($d);
			
			return $joinId;
		}
		
		return $this->setError($mod->getError());
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
		$con['title'] && ($con['title'] = ['like', '%'.$con['title'].'%']);
		
		if(isset($con['status']) && '' !== $con['status']){
			switch($con['status']){
				case 0:
					$con['_complex'] = ['status'=>0, 'begin_time' => ['gt', time()]];
					unset($con['status']);
					break;
				case 3:
					$con['_complex'] = ['status'=>0, 'begin_time' => ['lt', time()]];
					unset($con['status']);
					break;
				default:;
			}
		}
		
		$data = parent::getPageList($con, $fields, $order, $perNum);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$data['list'][$k] = $v;
		}
	
		return $data;
	}
	
}