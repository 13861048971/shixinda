<?php
use Think\Model;
class ContentModel extends BaseModel {
    public $cateList = [];
    
    public $statusArr = [  //可选的状态
        0 => '禁用',
        1 => '启用'
    ];
    
    protected $_validate;
    
    function __construct(){
        parent::__construct();
    
        $this->_validate = [
            ['cateName', 'require', '缺少分类!'],
           // ['type', [1,2,3], '缺少类型!', 1, 'in'],
        ];
    }
    
    //列表
    public function getList($con=[], $limit=5, $order='id desc'){
        $list = $this->where($con)->order($order)->limit($limit)->select();
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
	    $v['statusName'] = $this->statusArr[$v['status']];
	    $v['publishTime'] = date("Y-m-d H:i:s",$v['publish_time']);
	    $v['updateTime'] = date("Y-m-d H:i:s",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i:s",$v['add_time']);
	    return $v;
	}
	
	//添加或编辑
	function edit($data, $id=null){
	    if($id){
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
	        $data['id'] = $id;
	        d('tdk')->edit($data);
	        return $id;
	}
	
	//分页
	function getPageList($con=[], $fields = '*', $order = '', $perNum = 15){ 
	    $data = parent::getPageList($con, $fields, $order, $perNum);
	    $cateIdArr = getIdArr($data['list'],'cate_id');
	    $cateList = d('contentCate')->where([ 'cate_id'=>[in,$cateIdArr]])->select();
	    foreach($data['list'] as $k=>$v){
	        $data['list'][$k] = $this->parseRow($v);
	        foreach ($cateList as $k1=>$v1){
	            if($v['cate_id'] == $v1['id']){
	                $data['list'][$k]['cateName'] = $v1['name'];
	            }
	        }
	    }
	    return $data;
	}
	
	public function getContentCateList($id,$i = 0){
	    if($id>0){
	        $contentCate = d('contentCate')->getInfo($id);//分类的信息
	        $pid = (int)$contentCate['pid'];//信息的父级id
	         
	        $this->cateList[$i] = $contentCate;
	         
	        $i +=1;
	        // var_dump($postCate);
	        $this->getContentCateList($pid,$i);
	         
	    }
	     
	    return  $this->cateList;
	}
}

