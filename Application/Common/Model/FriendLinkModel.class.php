<?php
use Think\Model;
class FriendLinkModel extends BaseModel {
    public $statusArr = [  //可选的状态
        0 => '禁用',
        1 => '启用'
    ];
    
    //列表
    public function getList($con=[], $limit=5){
        $list = $this->where($con)->limit($limit)->select();
        foreach ($list as $k=>$v){
            $list[$k] = $this->parseRow($v);
        }
	    return $list;
	}
	
	//详情
	public function getInfo($id){
	    $info = $this->find($id);
	    if(!$info) return;
	    $info = $this->parseInfoRow($info);
	    return $info;
	}
	
	//格式化Info
	public function  parseInfoRow($v){
	    $v['statusName'] = $this->statusArr[$v['status']];
	    $v['publishTime'] = date("Y-m-d H:i",$v['publish_time']);
	    $v['updateTime'] = date("Y-m-d H:i",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i",$v['add_time']);
	    return $v;
	}
	
	//格式化行
	public function parseRow($v){
	    $v['statusName'] = $this->statusArr[$v['status']];
	    $v['publishTime'] = date("Y-m-d H:i",$v['publish_time']);
	    $v['updateTime'] = date("Y-m-d H:i",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i",$v['add_time']);
	    $v['logo'] = getImage($v['logo'], -1);
	    return $v;
	}
	
	//添加或编辑
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
	
	//分页
	function getPageList($con=[], $fields = 'id', $order = '', $perNum = 15){
	    $data = parent::getPageList($con, $fields, $order, $perNum);
	    
	    foreach($data['list'] as $k=>$v){
	        $data['list'][$k] = $this->parseRow($v);
	    }
	    return $data;
	}
}

