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
     * 获取信息列表
     * @param array $arr
     */
    public function getPageList($con, $fields = 'id',$order = 'id desc', $perNum = 10){
        if($con['title']){
            $con['title'] = ['like', '%' . $con['title'] . '%'];
        }
    
        $mod = d('user_msg_read');
        ($uid = $this->user['id']) && ($map['user_id'] = $uid);
        if(isset($con['isRead']) && $map){
            $is = $con['isRead'];
            $subQuery = $mod->field('msg_id as id')->where($map)->buildSql();
            $is === '0' && $con['_string'] = 'id not in '.$subQuery;
            $is === '1' && $con['_string'] = 'id in '.$subQuery;
        }
        if( MODULE_NAME == 'Home' && $map){
            $subQuery = $mod->field('msg_id as id')->where($map)->buildSql();
            $fields = 'id, if((id in ' . $subQuery . '), 1,0) as readed';
            $order = 'readed asc,id desc';
        }
    
        isset($con['from']) && $con['from'] === '0' && $con['from'] = ['lt', 1];
        isset($con['cate']) && $con['cate'] === '0' && $con['cate'] = ['lt', 1];
        $data = parent::getPageList($con, $fields, $order, $perNum);
        foreach($data['list'] as $k=>$v){
            $v = $this->getInfo($v['id']);
            $data['list'][$k] =  $this->parseRow($v);
             
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
     * 根据条件获取信息
     * @param array $con
     */
    public function getList($con = null, $limit = 10, $order){
        
        $list = $this->where($con)->order($order)->limit($limit)->select();
        foreach($list as $k=>&$v){
            $list[$k] = $this->parseRow($v);
            //     			$v = $this->getInfo($v['id']);
        }
        return $list;
    }
    
    public function getInfo($id){
        $info = $this->find($id);
        if(!$info) return;
    
        $info['updateTime'] = local_date($info['update_time'], 'Y-m-d H:i');
        $info['addTime'] 	= local_date($info['add_time'], 'Y-m-d H:i');
        $info['userName'] = d('user')->getInfo($info['user_id'])['username'];
        !$info['title'] && $info['title'] = $info['content'];
    
        $type = $info['type'];
        $info['typeName'] = $info['type_id'];
        $info['cateName'] = $this->cateArr[$info['cate']];
    
        if($info['from']){
            $from = d('user')->getInfo($info['from']);
            $info['from'] = filter([$from], 'id,nickname,realname,mobile,avatar')[0];
        }else{
            $info['from'] = [
                'realname' => '管理员',
                'nickname' => '管理员',
                'avatar' => '/Public/images/admin.jpg',
                'isAdmin' => true,
            ];
            if($nickname = $this->config['nickname']){
                $info['from']['nickname'] = $info['from']['realname'] = $nickname;
            }
            if($avatar = $this->config['avatar'])
                $info['from']['avatar'] = $avatar;
        }
    
       // $info['isRead'] = $this->read($id, $this->user['id'], false);
    
        if($info['cate'] == 2 && $info['node_id'])
            $info['artistImage'] = d('album')
            ->where(['type_id'=>$info['node_id']])
            ->order('id desc')->getField('path');
    
    
    
            return $info;
    }
    
    
    //格式化行
    public function parseRow($v){
        $v['num'] = d('postComment')->where(['post_id'=>$v['id']])->Count();//回复数量
        $v['cateName'] = d('postCate')->where(['id'=>(int)$v['post_cate_id']])->getField('name');//帖子分类名
        $v['statusName'] = $this->statusArr[$v['status']];
        $v['publishTime'] = date("Y-m-d H:i:s",$v['publish_time']);
        $v['update_time'] = date("Y-m-d H:i:s",$v['update_time']);
        $v['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
        
        $data = D('user')->where(['id'=>$v['user_id']])->select();
        foreach ($data as $vo){
            $v['username']=$vo['nickname'];
        }
        return $v ;
    }
}