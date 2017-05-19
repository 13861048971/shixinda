<?php
use Think\Model;
import('Org.Util.Validator');
/**
 * 用户模型
 */
class PostCommentModel extends BaseModel{
    public $cacheCountKey = '_cacheCount';
    public $statusArr = [1=>'显示', 0=>'不显示'];
    public $typeArr = ['新闻', '短信'];
    /**
     * 编辑or添加
     */
    function edit($data, $id=null){
        if($id){
            
            $data['update_time'] = time();
            $data['id'] = $id;
            if(!$this->create($data))
                return false;
                if(false === $this->save()){
                    $this->lastError = '修改失败!';
                    return false;
                }
                return $id;
        }
    
        $data['add_time'] = $data['update_time'] = time();
        if(!$this->create($data))
            return false;
        if(!($id = $this->add())){
            return $this->setError('评论失败!');
        }else{
            $commentInfo = $this->where(['id' => $id])->find();
            if($commentInfo)
                $this->resetCache($this->cacheCountKey.$commentInfo['post_id'].$commentInfo['id'], 'Count');
        }
            
        //帖子的评论数量
        $postComment = d('postComment')->where(['post_id'=>$data['post_id']])->count();
        d('post')->where(['id'=>$data['post_id']])->setField('comment_num', $postComment);
        return $id;
    }
    
    /**
     * 获取信息列表及分页
     * @param array $arr
     */
    public function getPageList($con, $fields = '*', $order = '', $perNum = 10){
        $p = $_GET['p'];
        !$p && $p = 1;
        $data = parent::getPageList($con, $fields, $order, $perNum);
        //评论用户id数组
        $idArr1 = getIdArr($data['list'], 'user_id');
        if($idArr1)
            $userList1 = d('user')->where(['id' => ['in', $idArr1]])->select();
        //回复帖子id数组
        $idArr2 = getIdArr($data['list'], 'reply_id');
        if($idArr2){
            //回复帖的用户id数组
            $idArr3 = $this->where(['id'=>['in', $idArr2]])->getField('user_id', true);
            //回复帖列表
            $postCommentList = $this->where(['id' => ['in', $idArr2]])->select();
        }      
        if($idArr3)
            $userList2 = d('user')->where(['id' => ['in', $idArr3]])->select();

        //主帖信息
        $postIdArr = getIdArr($data['list'],'post_id');
        if($postIdArr){
            $postList = d('post')->where(['id' => ['in',$postIdArr]])->select();
            $postCateIdArr = getIdArr($postList,'post_cate_id');
            $postCateList = d('postCate')->where(['id' => ['in',$postCateIdArr]])->select();
        }
            
        foreach($data['list'] as $k=>$v){
            $data['list'][$k] = $this->parseRow($v);
            //获取评论的信息
            foreach($userList1 as $k1=>$v1){
                if($v1['id'] == $v['user_id']){
                    $data['list'][$k]['userName'] = $v1['nickname'];    
                }
            }
            //获取主贴标题
            foreach($postList as $kp => $vp){
                if($v['post_id'] == $vp['id']){
                    $data['list'][$k]['title'] = $vp['title'];
                }
                //获取主贴分类
                foreach($postCateList as $kCate => $vCate){
                    if($vp['post_cate_id'] == $vCate['id']){
                        $data['list'][$k]['cateName'] = $vCate['name'];
                    }
                }
            }
  
            //论坛回帖楼层显示
            $data['list'][$k]['floor'] = ($p-1)*$perNum+$k+1;
            $data['list'][$k]['floorName'] = $data['list'][$k]['floor'].'楼';
            if($p == 1){
                $k == 0 && $data['list'][$k]['floorName'] = '沙发';
                $k == 1 && $data['list'][$k]['floorName'] = '板凳';
            }
            //获取回复的信息
            if($postCommentList && $userList2){
                foreach ($postCommentList as $k2=>$v2){
                    if($v2['id'] == $v['reply_id']){
                        foreach ($userList2 as $k3=>$v3){
                            if($v3['id'] == $v2['user_id']){
                                $data['list'][$k]['replyUserName'] = $v3['nickname'];
                                $data['list'][$k]['replyContent'] = $v2['content'];
                                $data['list'][$k]['replyAddTime'] = date('Y-m-d H:i', $v2['add_time']);
                                $data['list'][$k]['replyUpdateTime'] = date('Y-m-d H:i', $v2['update_time']);
                
                            }
                        }
                    }
                }    
            }
            
        }
        return $data;
    }
    //缓存数量
    //d('postComment')->where(['id'=>['lt',$v['node_id']],['post_id'=>$v2['post_id']]])->count();
    protected  function _cacheCount($con){
        $count = $this->where($con)->count();
        return $count;
    }
    
    //获取缓存数目信息
    public function getPostCommentCount($con){
        $count = $this->getCache($this->cacheCountKey.$con['post_id'].$con['node_id'], 'Count',$con);
        return $count;
    }
    
    /**
     * 获取信息列表
     * @param array $con
     */
    public function getList($con = null, $limit = 10, $order = 'rank'){
        $list = $this->where($con)->order($order)->limit($limit)->select();
        foreach($list as $k=>&$v){
            $list[$k] = $this->parseRow($v);
        } 
        return $list;
    }
    
    //获取详情
    public function getInfo($id){
        $info = $this->find($id);
        if(!$info) return;
        $info = $this->parseRow($info);
        return $info;
    }
    
    //格式化行
    public function parseRow($v){
        $v['addTime'] = date('Y-m-d H:i', $v['add_time']);
        $v['updateTime'] = date('Y-m-d H:i', $v['update_time']);
        return $v;
    }
}