<?php
use Think\Controller;

class PublicController extends Controller {

	public $sid;
	public $user;
	
	public function _initialize(){
		$this->session();
		$this->configInfo = $this->config();
		$this->about = $this->aboutOur();
		$navigation = d("navigation")->where(['pid'=>['eq',6]])->order('rank ')->select();
		$childNavigation = d("navigation")->where(['pid'=>['neq',0]])->order('rank desc')->select();
		$uri = $_SERVER['REQUEST_URI'];
		foreach ($navigation as $k=>$v){
		     
		    if(strpos(strtolower($uri), $v['url']) !== false){
		        if(strtolower($uri) != '/' && $v['url'] != '/')
		            $navigation[$k]['current'] = true;
		            if(strtolower($uri) == $v['url'] )
		                $navigation[$k]['current'] = true;
		    }
		
		    foreach ($childNavigation as $k2=>$v2) {
		        if($v['id'] == $v2['pid']){
		            $navigation[$k]['list'][] = $v2;
		
		        }
		    }
		}
		$this->assign('user',session('user'));
		$this->assign('navigation',$navigation);
		$this->assign('aboutOur',$this->about);
		$this->assign('config',$this->configInfo);
		if($this->user = self::isLogin()){
			$this->assign('user', $this->user);
			return;
		}
		// $this->user = d('user')->getInfo(23);return;
		self::checkUrl();
	}
	
	
	//关于我们配置信息
	public function aboutOur(){
	    $mod = d('config');
	    $info = $mod->getList();
	    $info = $info['about']['node'];
	     
	    return $info;
	}
	//网站配置信息
	public  function config(){
	    $mod = d('config');
	    $list = $mod->getList();
	    $list = $list['config']['node'];
	
	    return $list;
	}
	
	
	//设置session
	private function session(){
		$sid = $_REQUEST['sid'];
		
		if($sid && strlen($sid) != 26){
			return ajaxReturn2(1, '错误的会话id!');
		}
		
		$sid && session_id($sid);
		session('[start]');
		$this->sid = session_id();
	}
	
	static function isLogin(){
		if(!($user = session('user')))
			return false;
		
		$user = d('user')->getInfo($user['id']);
		
		if(!$user){
			return false;
		}
		
		return $user;
	}
	
	//检测是否需要登录
	public function checkUrl(){
		$arr = [
			'regist','getvercode','login', 'recvcode','passreset'
		];
		
		$ctr = strtolower(CONTROLLER_NAME);
		
		if( in_array($ctr, ['file','index','post']) )
			return true;
		
		$actName = strtolower(ACTION_NAME);
		
		if(!in_array($actName, $arr)){
			$this->redirect('/user/login');
		}
	}
	
	function _empty(){
		echo '当前页面不存在';
	}
	
	function index(){
		echo '当前页面不存在!';
	}
	
	/**
	 * 
	 * @param string $mode 模型名称
	 * @param int $id  文章在数据库中的id值
	 */
	function click($mode,$id){
	    $mode = d($mode);
	    $info = $mode->where(['id'=>$id])->find(); 
	    $click = (int)$info['click']+1;
	    $mode->data(['click'=>$click])->where(['id'=>$id])->save();
	}
	
	/**
	 * 
	 * @param unknown $type
	 * @param unknown $filename
	 * @return number[]|string[]|NULL[]|unknown[]
	 */
	function upload($type, $filename){
		$typeArr = ['image', 'avatar'];
		!in_array($type, $typeArr) && $type = 'image';

		!$filename && $filename = $type;
        $output = array('err' => 0, 'msg' => '', 'src' => '');
		$upload = new \Think\Upload();							// 实例化上传类
		$upload->maxSize   = 3145728 ;							// 设置附件上传大小
		$upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  = 'Public/upload/'; 						// 设置附件上传根目录
		$upload->savePath  = $type . '/'; 							// 设置附件上传（子）目录
		// 上传文件 
		$info = $upload->upload();
		if(!$info) {
			$output['err'] = 1;
            $output['msg'] = $upload->getError();;
		}else{
			$file = $info[$filename];
			$src = '/'.$upload->rootPath . $file['savepath'] . $file['savename'];
			$output['file'] = $file;
			$output['src'] = $src;
		}
		
		return $output;
	}
	
	/**
	 * 编辑
	 * @param string $modName  模型名称
	 * @param string $template 模板地址
	 */
	protected function ajaxEdit($modName, $template = null, $callback = null,$success=''){
	    $mod = d($modName);
	    $modTdk = d('tdk');
	    if($_POST){
	        $data = $_POST;
	        $id   = (int)$_POST['id'];
	        $act = $id ? '编辑' : '添加';
	        	
	        if($mod->edit($data, $id))
	            return ajaxReturn(0, ($success ? $success : $act) . '成功!');
	            return ajaxReturn(1, $act . '失败,'. $mod->getError());
	    }
	
	    if($id = (int)$_GET['id']){
	        $row = $mod->getInfo($id);
	
	        $this->assign('row',$row);
	    }
	
	    if(is_callable($callback))
	        $callback($row, $mod);
	        if($mod->statusArr){
	            $statusList = [[ 'name' => 'status', 'list' => $mod->statusArr]];
	            if(isset($row['status'])){
	                $statusList[0]['checked'] = $row['status'];
	                $statusList[0]['selected'] = $row['status'];
	            }
	            if(!isset($row['status']))
	                $statusList[0]['checked'] = 1;
	                	
	                $this->assign('statusList', $statusList);
	        }
	
	     
	}
	
	/**
	 * ajax 删除
	 * @param string $modName
	 * @return jsonString
	 */
	protected function ajaxDel($modName){
	    if(!($id = (int)$_REQUEST['id']))
	        return ajaxReturn(1, '缺少ID!');
	        if(!d($modName)->delete($id))
	            return ajaxReturn(1, '删除失败!');
	            return ajaxReturn(0,'删除成功!');
	}
	
	
	/**
	 * 分页
	 * @param $mod
	 * @param $order 排序字段
	 * @param $perNum 每页条数
	 * @param $pageVar 模板的分页视图变量名称
	 * @return $list 数据变量
	 */
	public function page($mod, $order = 'id', $perNum = 15, $pageVar = 'page'){
	    $count   = $mod->count();
	    $Page    = new Think\Page($count, $perNum);
	    $show    = $Page->show();
	    $list 	 = $mod->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
	    $this->assign($pageVar, $show);
	    return $list;
	}
	
	
	/**
	 * $type string 是内容类型名称
	 * 赞或者踩
	 */
	public function support($type){
	    $data['type'] = $this->typeArr[$type];
	    $data['node_id'] = $_GET['id'];
	    $data['user_id'] = $this->user['id'];
	    d('support')->edit($data);
	}
}