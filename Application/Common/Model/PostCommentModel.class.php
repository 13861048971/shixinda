<?php
use Think\Model;
import('Org.Util.Validator');
/**
 * 用户模型
 */
class PostCommentModel extends BaseModel{
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
            if(!($id = $this->add()))
                return $this->setError('评论失败!');
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
        foreach($data['list'] as $k=>$v){
            $data['list'][$k] = $this->parseRow($v);
            //论坛回帖楼层显示     
            $data['list'][$k]['floor'] = ($p-1)*$perNum+$k+1;
            $data['list'][$k]['floorName'] = $data['list'][$k]['floor'].'楼';
            if($p == 1){
                $k == 0 && $data['list'][$k]['floorName'] = '沙发';
                $k == 1 && $data['list'][$k]['floorName'] = '板凳';
            }
            
        }
        //dump($data['list']);exit();
        return $data;
    }
    
     
    /**
     * 获取信息列表
     * @param array $con
     */
    public function getList($con = null, $limit = 10){
        $list = $this->where($con)->order('rank')->limit($limit)->select();
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
        if($v['reply_id']){
            $row = $this->where(['id'=>$v['reply_id']])->find();
            $v['replyUserName'] = d('user')->where(['id'=>$row['user_id']])->getField('nickname');
            $v['replyAddTime'] = date('Y-m-d H:i', $row['add_time']);
            $v['replyContent'] = $row['content'];
        }
        $v['updateTime'] = date('Y-m-d H:i:s', $v['update_time']);
        $v['addTime'] 	= date('Y-m-d H:i:s', $v['add_time']);
        return $v ;
    }
}