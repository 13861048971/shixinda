<?php
use Think\Model;
import('Org.Util.Validator');
/**
 * 用户模型
 */
class PostModel extends BaseModel{
    public $statusArr = ['不显示', '显示'];

    public $cateList = [];
    
   
    /**
     * 编辑or添加
     */
 
    function edit($data, $id=null){
        $data = $this->setValidate($data);
        $modtdk = d('tdk');
        $data['type'] = 'post';
        
        $tdkData = [
            'node_id'      => $data['id'],
            'type'        => $modtdk->typeArr['post'],
            'title'       => $data['seo_title'],
            'description' => $data['seo_description'],
            'keywords'    => $data['seo_keywords']
        ];
        
        if($id){
            $data['update_time'] =  time();

            if(!$this->create($data))
                return false;
            
            if(false === $this->save()){
                $this->lastError = '修改失败!';
                return false;
            }
            
            if(false === $modtdk->edit($data)){
               $this->lastError = 'tdk信息修改失败!';
               return false;
           }
           
            return $id;
        }
        
        $data['add_time'] = $data['update_time'] = $tdkData['update_time'] = time();
          
        
        if(!$this->create($data))
            return false;
            if($tdkData['node_id'] = $this->add()){ 
                if(!($id = $modtdk->add($tdkData))){
                    return $this->setError('发送失败!');
                }  
            }
                return $id;
    }
    
    function setValidate($data){
        $this->_validate = [
            ['title', 'require', '缺少标题!'],
            ['content', 'require', '缺少内容!',1],
            ['post_cate_id', 'require', '缺少分类信息!',1],
        ];
    
        //只选了所有人
        if( 1 == $data['type2'] && !$data['type_id'] && $data['cate'] < 1){
            $data['type_id'] = array_merge($this->typeIdArr[2], $this->typeIdArr[3]);
        }
    
        if($data['type_id'] && is_array($data['type_id'])) {
            $arr = array_diff($data['type_id'], $this->typeArr);
            $data['type_id'] = implode(',', $arr);
        }
    
        if(!$data['cate'] && !$data['type_id'] && !$data['user_id'])
            return $this->setError('缺少推送人群!');
    
            return $data;
    }
   /**
    * 获取帖子列表
    * {@inheritDoc}
    * @see BaseModel::getPageList()
    */
    public function getPageList($con, $fields = 'id',$order = 'id desc', $perNum = 10){ 
        $data = parent::getPageList($con, $fields, $order, $perNum);
        $idArr = getIdArr($data['list']);
        $subQuery = d('postComment')->where(['post_id' => ['in', $idArr]])->group('post_id')
            ->field('max(id)')->buildsql();
        $postCommentList = d('postComment')->where("id in $subQuery")->select();
        $userIdArr = getIdArr($postCommentList, 'user_id');
        $userIdArr && $userList = d('user')->where(['id' => ['in', [0]]])->select();
        //仅后台Admin模块执行
        if(MODULE_NAME == 'Admin'){
            $postCateIdArr = getIdArr($data['list'],'post_cate_id');
            $postCateList = d('postCate')->where(['id'=>['in',$postCateIdArr]])->select();
            $postUserIdArr = getIdArr($data['list'],'user_id');
            $postUserList = d('user')->where(['id'=>['in',$postUserIdArr]])->select();
         }
        foreach($data['list'] as $k1=>$v1){
            $data['list'][$k1] =  $this->parseRow($v1);
            //仅后台Admin模块执行
            if(MODULE_NAME == 'Admin'){
                foreach ($postCateList as $kc => $vc){
                    if($v1['post_cate_id'] == $vc['id']){
                    $data['list'][$k1]['cateName'] = $vc['name'];
                    }
                }
                foreach ($postUserList as $ku => $vu){
                    if($v1['user_id'] == $vu['id']){
                        isset($vu['nickname']) && $vu['nickname']?($data['list'][$k1]['userName'] = $vu['nickname']):($data['list'][$k1]['userName'] = $vu['mobile']);  
                    }
                }
            }
            
            foreach($postCommentList as $k2=>$v2){
                foreach ($userList as $k3=>$v3){
                    if($v1['id'] == $v2['post_id']){
                        if($v3['id'] == $v2['user_id']){
                            $data['list'][$k1]['lastReplyUserName'] = $v3['nickname'];
                            $data['list'][$k1]['lastReplyUserId'] = $v3['id'];
                        }      
                    }    
                }      
            }
        }  
        return $data;
    }
    
     /**
     * 冒泡法取当前分类的所有父级分类集合
     * @param int $id 当前分类id（post_cate_id值）
     * @param int $i 当前分类键值为0，冒泡一次+1
     */
    public function getPostCateList($id,$i = 0){
        if($id > 0){
            $postCate = d('postCate')->getInfo($id);//分类的信息
            $pid = (int)$postCate['pid'];//信息的父级id
            
            $this->cateList[$i] = $postCate;
            $i +=1;
            
            $this->getPostCateList($pid,$i);
             
        }
         
        return  $this->cateList;
    }
    /**
     * 获取帖子列表信息
     * @param array $con
     */
    public function getList($con = null, $limit = 10, $order){
        
        $list = $this->where($con)->order($order)->limit($limit)->select();
        foreach($list as $k=>&$v){
            $list[$k] = $this->parseRow($v);
        }
        return $list;
    }
    
    //帖子详情
    public function getInfo($id){
        $info = $this->find($id);
        if(!$info) return;
        $info = $this->parseRow($info);
        return $info;
    }
    /**
     * 获取帖子列表信息
     * @param array $con
     */
    public function getPostList($con = null, $limit = 10, $order){
        $list = $this->where($con)->order($order)->limit($limit)->select();
        foreach($list as $k=>$v){
            $v['statusName'] = $this->statusArr[$v['status']];
            $v['updateTime'] = date("Y-m-d H:i",$v['update_time']);
            $v['addTime'] = date("Y-m-d H:i",$v['add_time']);
            $list[$k] = $v;
        }
        return $list;
    }
    
    
    //格式化行
    public function parseRow($v){
        $v['statusName'] = $this->statusArr[$v['status']];
        $v['updateTime'] = date('Y-m-d H:i',$v['update_time']);
        $v['addTime'] = date("Y-m-d H:i",$v['add_time']);
        return $v;
    }  
    
}