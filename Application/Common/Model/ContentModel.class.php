<?php
use Think\Model;
class ContentModel extends BaseModel {
    public function getList($con=[], $limit=5){
        $list = $this->where($con)->limit($limit)->select();
	    return $list;
	}
}

