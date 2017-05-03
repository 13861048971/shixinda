<?php
use Think\Model;
import('Org.Util.Validator');
/**
 * 用户模型
 */
class PostCateModel extends BaseModel{
    public $cacheKey  = 'category_';
    public $statusArr = [0 => '关闭',	  1 => '启用' ];
    
    protected $_validate;
    
    function __construct(){
        parent::__construct();
    
        $this->_validate = [
            ['name', 'require', '缺少名称!'],
           
        ];
    }
    
    /**
     * 编辑or添加类目
     */
    function edit($data, $id=null){
        $modtdk = d('tdk');
        $data['type'] = 'postCate';
        $tdkData = [
            'node_id'      => $id,
            'type'        => $modtdk->typeArr['postCate'],
            'title'       => $data['seo_title'],
            'description' => $data['seo_description'],
            'keywords'    => $data['seo_keywords']
        ];
            if($id){
                $data['update_time'] = time();
                
                $pdata = $this->where(['name'=>$_POST['pname']])->find();
                $data['pid'] = $pdata['id'];
                $return  = $this->data($data)->where('id=' . (int)$id)->save();
                if(!$return){
                    $this->lastError = '修改类目失败!';
                    return false;
                }
                
                if(false === $modtdk->edit($data)){
                    $this->lastError = 'tdk信息修改失败!';
                    return false;
                }

                return $id;
            }
   
            $data['update_time'] = $data['add_time'] = $tdkData['add_time'] = $tdkData['update_time'] = time();
            if(!$this->create($data))
                return false;
            
                if($tdkData['node_id'] = $this->add()){
                    if(!($id = $modtdk->add($tdkData))){
                        return $this->setError('发送失败!');
                    }
                }
   
                    return $id;
    }
    
    
    /**
     * 添加多个类别,从父类开始
     * @param array $cates
     * @param int $goods_type
     * @param int $deep 要添加类别的深度
     * @return $pid 最后添加的类别id
     */
    function addCates($cates, $goods_type, $deep = 3, $type = 1){
        if(!$cates) return false;
        $mod = d('category');
        $cate_id = 0;
        $pid = 0;
        foreach($cates as $k=>$v){
            if($k > $deep - 1) break;
            	
            $cate = $cates[$k] = trim($v);
            $con = ['name'=>$cate, 'parent_id' => $pid];
            $rows = $this->getList(null, 1, $con);
            if(!$rows){
                $data = [
                    'parent_id' => $pid,
                    'name'		=> $cate,
                    'type'		=> 1,
                    'type_id'	=> $goods_type,
                    'status'	=> 1
                ];
    
                if(!($pid = $this->edit($data)))
                    return false;
                    continue;
            }
            $pid = $rows[0]['id'];
        }
    
        return $pid;
    }
    
    public function getNames($ids){
        $arr = array_filter(explode(',', $ids));
        if(!$arr) return;
    
        $con = ['id' => ['in', $arr]  ];
        $nameArr = $this->where($con)->getField('name', true);
        return implode(',', $nameArr);
    }
    
    /**
     * 更新路径
     **/
    public function updatePath($id){
        $info = $this->getInfo($id);
        $path = $id;
        if($info['parent_id']){
            $pInfo = $this->getInfo($info['parent_id']);
            $path = $pInfo['path'] . '/' . $path;
        }
    
        if($path == $info['path'])
            return;
    
            return $this->data(['path' => $path ,'id'=>$id])->save();
    }
    
    //获取顶级类目信息
    public function getTopCate($path){
        $pid = (int)explode('/',$path)[0];
        if(!$pid) return;
        return $this->getInfo($pid);
    }
    
    public function getParents($id, $type = 1){
        $cate = $this->getInfo($id);
        $paths = explode('/', $cate['path']);
        if($paths[0] == $id)
            return [$cate];
            $con = ['id' => ['in', $paths]];
            return $this->getList(null, $type, $con);
    }
    
    public function getPids($id){
        $cate = $this->getInfo($id);
        $paths = explode('/', $cate['path']);
        return $paths;
    }
    
    public function getInfo($id){
        $v = $this->find($id);
//         if(!$v) return;
//         $v['typeName'] 		= $this->typeArr[$v['type']];
//         $v['addTime']  		= local_date($v['add_time']);
//         $v['updateTime']  	= local_date($v['updateTime']);     
//         $v['deepArr']   = explode('/', $v['path']);
//         $v['deep'] = $n	= count($v['deepArr']);
//         $v['name2'] 		= str_repeat('&emsp;', $n-1). $v['name'];
        $v = $this->parseRow($v);
    
        return $v;
    }
    
    /**
     * 取子类,本地不存在连接远程
     * @param int $parent_id
     * @param int $type      类型,默认是产品分类
     * @return array
     **/
    public function getList($con = [], $limit = '15', $order = 'rank desc'){
		foreach($con as $k=>$v){
		    if(!$v && $v !== '0' )
		        unset($con[$k]);
		}
		
        $list = $this->where($con)->order($order)->select();
        foreach($list as $k=>$v){
            $list[$k] = $this->parseRow($v);
        }
      
        return $list;
    }
    
    //格式化行
    public function parseRow($v){
        $v['num'] = d('postCate')->where(['pid'=>$v['id']])->Count();
        $v['addTime'] = date('Y-m-d H:i:s',$v['add_time']);
        $v['updateTime'] = date('Y-m-d H:i:s',$v['update_time']);
        return $v ;
    }
}