<?php
use Think\Model;
/**
 * 投标
 */
class JoinModel extends BaseModel {
	public $cacheKey  = 'join_';
	public $afterArr  = [1=>'原片','简修','精修'];
	public $statusArr = [0=>'投标中',1=>'已中标',2=>'被拒绝'];
	protected $_validate;
	
	function __construct(){
		parent::__construct();
	}
	
	function setValidate($data, $id){
		$this->_validate = [
			['task_id', 'require', 	'缺少任务id!', 1],
			['user_id', 'require', 	'缺少用户id!', 1],
			['num', 	'require', 	'缺少交片张数!', 1],
			['overdays','require', '缺少交片天数!', 1],
			['price', 	'require', 	'缺少报价!', 1],
			['address', 'require', 	'缺少地址!', 1],
			['note', 	'require', 	'缺少特色!', 1],
		];
		
		if($id){
			$info = $this->find($id);
			if(!$info) 
				return $this->setError('记录不存在!');
			$data = array_merge($info, $data);
		}else{
			$con = ['user_id' => $data['user_id'], 'task_id' => $data['task_id']];
			if($this->where($con)->find())
				return $this->setError('已经投标了!');
		}
		
		if(!$id && $data['user_id'] && ($user = d('user')->getInfo($data['user_id']) ) ){
			$data['nickname'] 	= $user['nickname'];
			$data['realname'] 	= $user['realname'];
			$data['avatar'] 	= $user['avatar'];
		}

		return $data;
	}
	
	/**
	 * 投标个数
	 */
	function getNum($task_id){
		return $this->where(['task_id' => $task_id])->count();
	}
	
	//订单统计
	function getCountArr($con){ 
		$list = $this->where($con)->group('status')
			->getField('status,count(*)',true);
		$arr = [
			'joinInNum'  	 => (int)$list[0],
			'joinSelectNum'  => (int)$list[1],
			'joinRefuseNum'  => (int)$list[2],
			'joinNum' 		 => array_sum($list)
		];
		return $arr;
	}
	/**
	 * 投标婉拒
	 * @param array $post  ['id'=>,]
	 * @return bool
	 */
	function refuse($post){
		$id = (int)$post['id'];
		if(!$id || !($join = $this->getInfo($id)) ){
			return $this->setError('投标不存在!');
		}
		if($join['status'] == 2)
			return $this->setError('已经操作过了!');
		if($this->user['id'] != $join['task']['user_id'])
			return $this->setError('没有权限!');
		$join['status'] = 2;
		if($this->edit($join, $id)){
			$d = [
				'cate'      => 1,
				'node_id'	=> $id, 
				'from'		=> $join['task']['user_id'], 
				'user_id'	=> $join['user_id'],
				'title' 	=> '你的投标已被婉拒',
			];
			$d['content'] = $d['title'];
			d('userMsg')->edit($d);
			
			return true;
		}
		return false;
	}
	
	/**
	 * 接受投标
	 */
	function receive($post){
		$id = (int)$post['id'];
		if(!$id || !($join = $this->getInfo($id)) ){
			return $this->setError('投标不存在!');
		}
		if($join['status'] == 1)
			return $this->setError('已经操作过了!');
		if($this->user['id'] != $join['task']['user_id'])
			return $this->setError('没有权限!');
		$join['status'] = 1;
		
		$mod = d('order');
		$task = $join['task'];
		if(($order = $mod->where(['type'=>2, 'node_id'=>$task['id']])->find()) )
			return $this->setError('不能重复下单!');
		
		$mod = d('task');
		$task['status'] = 1;
		$task['pho_id'] = $join['user_id'];

		$orderMod = d('order');
		$order = [
			'user_id' 	=> $task['user_id'],
			'pho_id' 	=> $join['pho']['id'],
			'begin_time'=> $task['begin_time'],
			'address' 	=> $task['cityName'],
			'contact' 	=> $task['user']['nickname'],
			'mobile' 	=> $task['user']['mobile'],
			'type' 		=> 2,
			'total'     => $join['price'] * $task['num'],
			'num'       => $task['num'],
			'price' 	=> $join['price'],
			'node_id' 	=> $task['id'], 
			'desc'		=> $task['note'],
		];
		$this->startTrans();
		if($this->edit($join, $id) && 
			$mod->edit($task, $task['id']) && 
			($oid = $orderMod->edit($order)) && 
			$this->commit()){
			$d = [
				'cate'      => 2,
				'node_id'	=> $oid, 
				'from'		=> $order['user_id'], 
				'user_id'	=> $order['pho_id'],
				'title' 	=> '发起了一个任务订单 金额: ￥'. $order['total'],
			];
			$d['content'] = $d['title'];
			d('userMsg')->edit($d);	
	
			return $oid; 
		}
		
		$this->rollback();
		$e = $this->getError() ? '投标:' . $this->getError() : '';
		$e .= $mod->getError() ? '任务:' . $mod->getError() : '';
		$e .= $orderMod->getError() ? '订单:' . $orderMod->getError() : '';
		return $this->setError($e);
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
			d('album')->editAlbum($data['image'], $id,0,1);
			return $id;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		if(!$this->create($data))
			return false;

		if(!($id = $this->add())){
			return $this->setError('添加失败!');
		}
		d('album')->editAlbum($data['image'], $id, 0,1);
		$d = ['join_num' => $this->getNum($data['task_id'])];
		d('task')->where(['id'=>$data['task_id']])->data($d)->save();
		return $id;
	}
	
	public function getInfo($id){
		$i = $this->find($id);
		if(!$i) return;
		$i['addTime'] 	= local_date($i['add_time']);
		$i['updateTime'] = local_date($i['update_time']);
		$i['user'] = d('user')->getInfo($i['user_id']);
		$i['pho'] = d('pho')->getInfo($i['user_id']);
		$i['task'] = d('task')->getInfo($i['task_id']);
		$i['statusName'] = $this->statusArr[$i['status']];
		$i['afterName'] = $this->afterArr[$i['after']];
		return $i;
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
	
	function getPageList($con, $fields = 'id', $order = 'add_time desc', $perNum = 15){	
		if($title = $con['title']){
			$mod = d('task');
			$subSql = $mod->where(['title'=> ['like', '%'.$title.'%']])
				->field('id')->buildSql();
			$con['_string'] = ' task_id in'. $subSql;
		}
	
		$data = parent::getPageList($con, $fields, $order, $perNum);

		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$_GET['comment'] && $v['comments'] = $this->getComment($v['id']);
			$data['list'][$k] = $v;
		}
		return $data;
	}
	
	/**
	 * 联合查询的分页函数 
	 * @param array $con
	 * @param string $order 排序方式
	 * 
	 *
	 */
	function getJoinPageList($con, $userId, $perNum = 15){
		!$_GET['p'] && $_GET['p'] = $_GET['page'];
		$_REQUEST['perNum'] > 0 && $perNum = (int)$_REQUEST['perNum'];
		
		$join[] = 'collect as a on c.user_id = a.node_id ';
		$con = filter([$con], 'id,node_id,hide,delete')[0];
		foreach($con as $k=>$v){
			if(false === strpos($k, '.')){
				$con['c.'.$k] = $v;
				unset($con[$k]);
			}
		}
		
		//我的
		$con['_complex'] = ['c.user_id' => $userId, 'a.user_id'=> $userId, '_logic' => 'or'];
		if($_GET['my']){
			$con['_complex'] = ['c.user_id' => $userId];
			$con['c.hide'] = ['lt', 2];
		}
		
		$count   = $this->alias('c')->where($con)->join($join, 'left')->getField('count(distinct(c.id))');
		$Page    = new Think\Page($count, $perNum);
		$pageVar = $Page->show();
		$page = [
			'page'	=> (int)$Page->totalPages, 
			'cur'	=> (int)$Page->cur_page, 
			'pre'	=> (int)$Page->up_page, 
			'next'	=> (int)$Page->down_page,
			'total' => (int)$count,
		];
		$this->field('distinct(c.id)');
		$list = $this->alias('c')->where($con)->join($join, 'left')->order('id desc')
			->limit($Page->firstRow.','.$Page->listRows)->select();
		$data['list'] = $list;
		if('Home' != MODULE_NAME){
			$data['pageVar'] = $pageVar;
		}
		$data = array_merge($data, $page);
		foreach($data['list'] as $k=>$v){
			$v = $this->getInfo($v['id']);
			$v['comments'] = $this->getComment($v['id']);
			$data['list'][$k] = $v;
		}
		return $data;
	}
	
}