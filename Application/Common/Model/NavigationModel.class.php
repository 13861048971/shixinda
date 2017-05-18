<?php
use Think\Model;
class NavigationModel extends BaseModel {
    public $statusArr = [  //可选的状态
        0 => '禁用',
        1 => '启用'
    ];
    
    public $cacheNavigationKey = '_cacheNavigation';
    
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
	
	    $info = $this->parseRow($info);
	    return $info;
	}
	
	//缓存所有导航信息
	protected  function _cacheNavigation(){
	    $navigation = d("navigation")->where(['pid'=>['eq',6]])->order('rank ')->select();
	    $childNavigation = d("navigation")->where(['pid'=>['neq',0]])->order('rank desc')->select();
	    foreach ($navigation as $k=>$v){
	        foreach ($childNavigation as $k2=>$v2) {
	            if($v['id'] == $v2['pid']){
	                $navigation[$k]['list'][] = $v2;
	            }
	        }
	    }
	    return $navigation;
	}
	
	//获取导航数据缓存信息
	public function getNavigation(){
	    
	    $navigation = $this->getCache($this->cacheNavigationKey, 'navigation');
	    $uri = $_SERVER['REQUEST_URI'];
	    foreach ($navigation as $k=>$v){
	        if(strpos(strtolower($uri), $v['url']) !== false){
	            if(strtolower($uri) != '/' && $v['url'] != '/')
	                $navigation[$k]['current'] = true;
	                if(strtolower($uri) == $v['url'] )
	                    $navigation[$k]['current'] = true;
	        }
	    }
	    //dump($navigation);exit();
	    return $navigation;
	}
	
	
	//格式化行
	public function parseRow($v){
	    $v['num'] = $this->where(['pid'=>$v['id']])->Count();
	    $v['statusName'] = $this->statusArr[$v['status']];
	    $v['publishTime'] = date("Y-m-d H:i:s",$v['publish_time']);
	    $v['updateTime'] = date("Y-m-d H:i:s",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i:s",$v['add_time']);
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
	        $this->resetCache($this->cacheNavigationKey, 'navigation');
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
	        $data['list'][$k] = $this->getInfo($v['id']);
	    }
	    return $data;
	}
}

