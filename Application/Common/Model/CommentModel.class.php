<?php
use Think\Model;
import('Org.Util.Validator');
/**
 * 用户模型
 */
class CommentModel extends BaseModel{
    public $statusArr = [1=>'显示', 0=>'不显示'];
    public $typeArr = ['news'=>'0', '短信'=>'1'];
    /**
     * 编辑or添加
     */
    function edit($data, $id=null){
        $data = $this->setValidate($data);
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
        $data['type'] = $this->typeArr["data['type']"];
        if(!$this->create($data))
            return false;
            if(!($id = $this->add()))
                return $this->setError('添加失败!');
                return $id;
    }
    
    function setValidate($data){
        $this->_validate = [
            ['content', 'require', '缺少内容!',1],
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
    public function getPageList($con, $fields = '*',$order = 'id desc', $perNum = 2){
        $con['type'] = $this->typeArr[$con['type']];
        $data = parent::getPageList($con, $fields, $order, $perNum);
        foreach($data['list'] as $k=>$v){
            $data['list'][$k] =  $this->parseRow($v);
        return $data;
        }
    } 
    /**
     * 根据条件获取信息
     * @param array $con
     */
    public function getList($con = null, $limit = 10){
        $list = $this->where($con)->order('rank')->limit($limit)->select();
        foreach($list as $k=>&$v){
            $list[$k] = $this->parseRow($v);
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
    
    
        if($info['cate'] == 2 && $info['node_id'])
            $info['artistImage'] = d('album')
            ->where(['type_id'=>$info['node_id']])
            ->order('id desc')->getField('path');
    
    
    
            return $info;
    }
    //格式化行
    public function parseRow($v){
        $v['updateTime'] = date("Y-m-d H:i:s",$v['update_time']);
	    $v['addTime'] = date("Y-m-d H:i:s",$v['add_time']);
        return $v ;
    }
}