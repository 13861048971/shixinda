<?php
use Think\Model;
class ContentModel extends BaseModel {
    public $statusArr = [  //可选的状态
        0 => '禁用',
        1 => '启用'
    ];
    
    //列表
    public function getList($con=[], $limit=5){
        $list = $this->where($con)->limit($limit)->select();
	    return $list;
	}
	
	//详情
	public function getInfo($id){
	    $info = $this->find($id);
	    if(!$info) return;
	
	    $info['statusName'] = $this->statusArr[$info['status']];
	    return $info;
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
}

