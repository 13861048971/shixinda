<?php
use Think\Model;
class ContentCateModel extends BaseModel {
    public $statusArr = [  //可选的状态
        0 => '禁用',
        1 => '启用'
    ];
    public $cacheChildCateKey = '_cacheChildCateArr';
    
    //缓存ChildCate信息
    public  function getChildCateArr($con){
        $cacheData = s($this->cacheChildCateKey.$con['pid']);
        if($cacheData) 
            return $cacheData['value'];
        $cateChildren = $this->getList($con);
        $cateArr = getIdArr($cateChildren);
        $product = d('content')->where(['cate_id' => ['in',$cateArr]])->select();
        foreach ($cateChildren as $k=>$v){
            foreach ($product as $k1=>$v1){
                if($v1['cate_id'] == $v['id']){
                    $cateChildren[$k]['childInfo'][] = $v1;
                }
            }
        }
        $cacheData = ['key' => $this->cacheChildCateKey.$con['pid'], 'value' => $cateChildren];
        s($this->cacheChildCateKey.$con['pid'], $cacheData,86400);
        return $cateChildren;
    }
    
    //列表
    public function getList($con=[], $limit=50){
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
	
	    $info = $this->parseRow($info);
	    $tdkInfo = d('tdk')->getInfo($id);
	    $info['tdk'] = $tdkInfo;
	    return $info;
	}
	
	//格式化行
	public function parseRow($v){
	    if(MODULE_NAME == 'Admin')
 	      $v['number'] = $this->where(['pid'=>$v['id']])->Count();
	    $v['statusName'] = $this->statusArr[$v['status']];
	    $v['updateTime'] = date("Y-m-d H:i:s",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i:s",$v['add_time']);
	    return $v;
	}
	
	//添加或编辑
	function edit($data, $id=null){
	    $data['actions'] && $data['actions'] = serialize($data['actions']);
	    if($id){
	        $cateInfo = $this->where(['id' => $id])->find();
	        $this->resetCache($this->cacheTdkKey.$cateInfo['pid'], 'ChildCateArr');
	        $data['update_time'] = time();
	        $return  = $this->data($data)->where('id=' . (int)$id)->save();
	        if(false === $return){
	            $this->lastError = '修改失败!';
	            return false;
	        }
	        d('tdk')->edit($data);
	        return $id;
	    }
	
	    $data['update_time'] = $data['add_time'] = time();
	    if(!$this->create($data))
	        return false;
	
	        if(!($id = $this->add())){
	            return $this->setError('添加失败!');
	        }
	        $cateInfo = $this->where(['id' => $id])->find();
	        $this->resetCache($this->cacheTdkKey.$cateInfo['pid'], 'ChildCateArr');
	        $data['id'] = $id;
	        d('tdk')->edit($data);
	        return $id;
	}
	
	//分页
	function getPageList($con=[], $fields = '*', $order = 'id desc', $perNum = 15){
	    $data = parent::getPageList($con, $fields, $order, $perNum);
	    //$first = $this->where(['pid'=>0])->select();
	    //dump($con);exit();
	    
	    foreach($data['list'] as $k=>$v){
	        $data['list'][$k] = $this->getInfo($v['id']);
	    }
	    return $data;
	}
}

