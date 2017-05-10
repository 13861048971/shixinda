<?php
use Think\Controller;
class UserController extends PublicController {
	public $mod;
 	public $cateList = [];
 
	function _initialize(){
		parent::_initialize();
		$this->mod = D('user');
	}
	
	//用户列表
	public function index($field = 'add_time',$showTimeArr = true){
	    $rightAction[]  = [
	        'name'=>'添加用户','dialog-lg'=>true,
	        'url'=>u('edit'), 'dialog' => true
	    ];
	    
	    $this->setRightAction($rightAction);
	    $usermod = D('user');
	    $con = $_GET;
	    $map = $this->setTimeArr($field, $showTimeArr);
	    $map && $con += $map;
	    
	    if($w = trim($con['keywords'])){
	        is_numeric($w) && ($where['mobile'] =  ['like', '%' .$w. '%']);
	        $where['nickname'] = ['like', '%' .$w. '%'];
	        $where['_logic'] = 'or';
	        $con['_complex'] = $where;
	    }
	    
	    $field && $order = $field . ' desc';
	    $data = $usermod->getPageList($con, 'id', $order);
	    $this->assign('userList', 	$data['list']);
	    $this->assign('page', 	  	$data['pageVar']);
	    
	    if($_GET['newwin']){
	        return $this->display('userList');
	    }
	    
	    if(IS_AJAX){
	        return ajaxReturn(0,"", $data['list']);
	    }
	    
	    return $this->display('index');
	}
	
    public function index2(){
        $field = 'add_time';
        $showTimeArr = true;
		$userMod = d('user');
		$con = $_GET;
		$map = $this->setTimeArr($field, $showTimeArr);
		$map && $con += $map;
		
		if($w = trim($con['keywords'])){
			is_numeric($w) && ($where['mobile'] =  ['like', '%' .$w. '%']);
			$where['nickname'] = ['like', '%' .$w. '%'];
			$where['_logic'] = 'or';
			$con['_complex'] = $where;
		}
		
		$rightAction[]  = ['name'=>'添加用户','dialog-lg'=>true,
			'url'=>u('edit'), 'dialog' => true];
	
		$this->setRightAction($rightAction);
		
		$field && $order = $field . ' desc';
		
		$data = $userMod->getPageList($con, 'id', $order);
		
		$this->assign('search', 	$_GET);
		$this->assign('userList', 	$data['list']);
		$this->assign('page', 	  	$data['pageVar']);
		
		if($_GET['newwin']){
			return $this->display('userList');
		}
		
		if(IS_AJAX){
			return ajaxReturn(0,"", $data['list']);
		}
		
		
		$this->display('index');
    }
    //编辑/添加用户
	public function edit(){
		$this->ajaxEdit('user', null, function($row, $mod){
			$genderList = [['list'=> d('user')->genderArr, 'name'=>'gender', 'checked'=>$row['gender']]];
			$this->assign('genderList', $genderList);
		});
	}
	//删除用户
	public function del(){
        $this->ajaxDel('user');
	}
	
	//登陆时间
	public function loginTime(){
		$this->index('last_login');
	}
	//小黑屋
	public function hei(){
		$_GET['status'] = 0;
		$this->index('', false);
	}

	//变更用户状态
	public function userChange(){
		$mod = d('user');
		if(IS_POST){
			$status = (int)$_POST['status'];
			$id 	= (int)$_POST['id'];
			$block_note = $_POST['block_note']; 
			if(!$mod->block($id, $status, $block_note))
				return ajaxReturn(1, $mod->getError().'操作失败!');
			
			return ajaxReturn(0,'操作成功!');
		}
		
		$this->ajaxEdit('user', null, function(&$row, $mod){
			$row['status'] = 0;
			$this->assign('blockArr', $mod->blockArr);
		});
	}
	
	//实名认证
	public function phoList(){
		$con = $_GET;
		$mod = d('pho');
		$data = $mod->getPageList($con);
		$statusArr = [[
			'paddText'=>'状态',
			'name'=>'status', 
			'list'=>$mod->statusArr, 
			'selected' => $_GET['status']
		]];
		
		$typeArr = [[
			'paddText'=>'类型',
			'name'=>'type', 
			'list'=>$mod->typeArr, 
			'selected' => $_GET['type']
		]];
		
		$this->assign($data);
		$this->assign('statusArr', $statusArr);
		$this->assign('typeArr', $typeArr);
		$this->assign('search', $_GET);
		$this->display('phoList');
	}

	//头部时间选择
	private function setTimeArr($field = 'add_time', $showTimeArr = true){
		if(!$showTimeArr) return;
		$date = $_GET['date'];
		if($date && $ts = explode(' - ', $date) ){
			$arr = explode('.', $ts[0]);
			$arr2 = explode('.', $ts[1]);
			$t = mktime(0,0,0, $arr[1], $arr[2], $arr[0]);
			$t2 = mktime(0,0,0, $arr2[1], $arr2[2], $arr2[0]);
			
			$con['add_time'] = ['between', [$t, $t2 + 3600*24 ] ];
		}
		$selectDate = [['type'=>'daterange', 'name'=> 'date', 'value'=>$date,
			'format'=>'YYYY.MM.DD']];

		!$field && $field = 'add_time';
		$timeArr = [];
		$today = mktime(0,0,0);
		$time = $_GET['time'];
		for($i=0; $i<7;$i++){
			$day = $today - $i*3600*24;
			$timeArr[$day] = [
				'url'  => u().'?time='.$day,
				'name' => $i<1? '今天':date('m月d日', $day), 
				'cur'  => $day == $time ? 1:0, 
			];
			if($time && $day == $time){
				$timeArr[$day]['cur'] = 1;
				$con[$field] = ['between', [$day, $day + 3600*24]];
			}
		}
		$this->assign('selectDate', $selectDate);
		$this->assign('timeArr',	$timeArr);
		return $con;
	}
	
	//认证历史
	public function history(){
		$con = $_GET;
		$con['status'] = ['gt', 0];
		$map = $this->setTimeArr();
		$con['_complex'] = $map;
		$data = d('artist')->getPageList($con);
		$typeList = d('category')->getList(null, 1);
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display('artist');
	}
	
	
	//用户详情页
	public function userDetail(){
		$id = (int)$_GET['id'];
		$userMod = d('user');
		$user = $userMod->find($id);
		$this->assign('user', $user);
		$this->display('userDetail');
	}	
	
    //消息管理列表
	public function message(){
		$this->setRightAction([[ 'name'=>'添加消息', 'dialog'=>true, 
			'dialog-lg'=>true, 'url' => u('messageEdit') ]]);
		$con = $_GET;
		$data = d('userMsg')->getPageList($con,""); 
		$this->assign($data);
		$this->assign('search', $_GET);
		$this->display();
	}
	
	//消息编辑
	public function messageEdit(){
		$this->ajaxEdit('userMsg',null, function(&$row, $mod){
		    $type = d('userMsg')->typeArr;
		    $type = array_flip($type);
            if($type){
                $typeList = [[ 'name' => 'type', 'list' => $type]];
                if(isset($row['type'])){
                    $statusList[0]['checked'] = $row['type'];
                    $statusList[0]['selected'] = $row['type'];
                }
                if(!isset($row['type']))
                    $statusList[0]['checked'] = 1;
                    	
                    $this->assign('typeList', $typeList);
            }
            
			
		});
	}
	
	//消息删除
	public function messageDel(){
		$this->ajaxDel('userMsg');
	}
	//帖子管理列表
	public function post(){
	    $this->setRightAction([[ 'name'=>'添加帖子', 'dialog'=>true,
	        'dialog-lg'=>true, 'url' => u('postEdit') ]]);
	    $con = $_GET;
	    $data = d('post')->getPageList($con,null);
	    $this->assign($data);
	    $this->assign('search', $_GET);
	    $this->display();
	}
	//取帖子所有父级元素集合
	public function getPostCateList($id,$i = 0){
	    if($id>0){
	        $postCate = d('postCate')->getInfo($id);//分类的信息
	        $pid = (int)$postCate['pid'];//信息的父级id
	        $this->cateList[$i] = $postCate; 
	        $i +=1;
	        $this->getPostCateList($pid,$i);
	        
	    }
	    
	    return  $this->cateList;
	}
	//帖子编辑
	public function postEdit(){
	    
	    $this->ajaxEdit('post',null, function($row, $mod){
	        
	        $con = [
	            'node_id'=> $_GET['id'],
	            'type' => d('tdk')->typeArr['post'],
	        ];
	        $tdkRow = d('tdk')->tdkInfo($con); //用模型传值
	        $this->assign('tdkRow',$tdkRow);//用模型
	    });
	    
	}
	
	//帖子删除
	public function postDel(){
	    $con = [
	        'noid_id' =>$_GET['id'],
	        'type' =>d('tdk')->typeArr['post']
	    ];
	    d('tdk')->where($con)->delete();
	        
	    $this->ajaxDel('post');
	    
	}
	//帖子评论列表
	public function postComment(){
	    $this->setRightAction([[ 'name'=>'帖子评论', 'dialog'=>true,
	        'dialog-lg'=>true, 'url' => u('postCommentEdit') ]]);
	    $con = $_GET;
	    $data = d('postComment')->getPageList($con,null);
	    $this->assign($data);
	    $this->assign('search', $_GET);
	    $this->display('postComment');
	}
	
	//帖子评论编辑
	public function postCommentEdit(){
	    $this->ajaxEdit('postComment',null, function($row, $mod){
            
	    });
	}
	
	//帖子评论删除
	public function postCommentDel(){
	    $this->ajaxDel('postComment');
	}
	
	//用户评论列表
	public function comment(){
	    $this->setRightAction([[ 'name'=>'用户评论', 'dialog'=>true,
	        'dialog-lg'=>true, 'url' => u('commentEdit') ]]);
	    $con = $_GET;
	    $data = d('comment')->getPageList($con);
	    $this->assign($data);
	    $this->assign('search', $_GET);
	    $this->display();
	}
	
	//用户评论编辑
	public function commentEdit(){
	    $this->ajaxEdit('comment',null, function($row, $mod){
	    });
	}
	
	//用户评论删除
	public function commentDel(){
	    $this->ajaxDel('comment');
	}
	
	//帖子分类列表
	public function postCate(){
	    $this->setRightAction([[ 'name'=>'添加分类', 'dialog'=>true,
	        'dialog-lg'=>true, 'url' => u('postCateEdit') ]]);
	    $con = $_GET;
	    (int)$con['pid'] = '0';
	    $list = d('postCate')->getList($con,'15');
	    $this->assign('list',$list);
	    $this->assign('search', $_GET);
	    $this->display('postCate');
	}
	//子类显示
	public function postCateChildren(){
	    $con = $_GET;
        $list = d('postCate')->getList($con);
	    ajaxReturn(0,'',array('list'=>$list));
	}
  	
	//帖子分类编辑
	public function postCateEdit(){
	    $this->ajaxEdit('postCate',null, function($row, $mod){
	        $con = $_GET;
	        if($con['pid']!=null){
	           $data = d('postCate')->where(['id'=>$con['pid']])->find();
	        }else{
	           $info = d('postCate')->where(['id'=>$con['id']])->find();
	           $data = d('postCate')->where(['id'=>$info['pid']])->find();
	       } 
	       $con = [
	           'node_id'=> $_GET['id'],
	           'type' => d('tdk')->typeArr['postCate'],
	       ];
	       $tdkRow = d('tdk')->tdkInfo($con); 
	       $this->assign('tdkRow',$tdkRow);
	       $this->assign('pname',$data);
	    });
	    
	}
	
	//帖子分类删除
	public function postCateDel(){
	    $con = [
	        'noid_id' =>$_GET['id'],
	        'type' =>d('tdk')->typeArr['postCate']
	    ];
	    d('tdk')->where($con)->delete();
	    $this->ajaxDel('postCate');
	}
	
	//用户举报
	public function report(){
	    $this->setRightAction([[ 'name'=>'用户举报', 'dialog'=>true,
	        'dialog-lg'=>true, 'url' => u('reportEdit') ]]);
	    $con = $_GET;
	    $data = d('report')->getPageList($con);
	    $this->assign($data);
	    $this->assign('search', $_GET);
	    $this->display();
	}
	
    public function reportDel(){
	    $this->ajaxDel('report');
	}
	
	
	public function reportEdit(){
	    $this->ajaxEdit('report',null,function(){
	    
	    });
	}	
}