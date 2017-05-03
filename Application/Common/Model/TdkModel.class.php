<?php
use Think\Model;
class TdkModel extends BaseModel {
    public $typeArr = [
        'post' => 1,
        'content' => 2,
        'contentCate' => 3,
        'postCate' => 4
    ];
    
    //列表
    public function getList($con=[], $limit=5){
        $list = $this->where($con)->limit($limit)->select();
        foreach ($list as $k=>$v){
            $list[$k] = $this->parseRow($v);
        }
	    return $list;
	}
	
	//格式化行
	public function parseRow($v){
	    $v['publishTime'] = date("Y-m-d H:i:s",$v['publish_time']);
	    $v['updateTime'] = date("Y-m-d H:i:s",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i:s",$v['add_time']);
	    return $v;
	}
	
	//详情
	public function getInfo($id){
	   $info = $this->where(['node_id'=>$id])->find();
	   if(!$info) return; 
	   return $info;
	}
	
	//根据条件获取tdk信息
	public function tdkInfo($con){
	    $info = $this->where($con)->find();
	    if(!$info) return;
	    return $info;
	}
	
	//添加或编辑
	function edit($data,$id=null){
	    $tdkData = [
	        'node_id'      => $data['id'],
	        'type'        => $this->typeArr[$data['type']],
	        'title'       => $data['seo_title'],
	        'description' => $data['seo_description'],
	        'keywords'    => $data['seo_keywords']
	    ];

	    $return = $this->where(['node_id'=>$data['id']])->find();
	    
	    if($return){
	        $tdkData['id'] = $return['id'];
	        $tdkData['update_time'] = time();
	        $return = $this->data($tdkData)->save();
	        if(false === $return){
	            $this->lastError = '修改失败!';
	            return false;
	        }
	        return $tdkData['id'];
	    }
	   
	    $tdkData['update_time'] = $tdkData['add_time'] = time();
	    if(!$this->create($tdkData))
	        return false;
	
	        if(!($id = $this->add())){
	            return $this->setError('添加失败!');
	        }
	        return $id;
	}
	
	//分页
	function getPageList($con=[], $fields = '*', $order = '', $perNum = 15){
	    $data = parent::getPageList($con, $fields, $order, $perNum);
	
	    foreach($data['list'] as $k=>$v){
	        $data['list'][$k] = $this->parseRow($v);
	    }
	    return $data;
	}
	
	
}

