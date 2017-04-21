<?php
use Think\Controller;

class PublicController extends Controller {	
	public $admin;
	public $nav;
	public $leftNav;
	public $rightAction;
	public $TDK;
	public $curUrl;
	public $parentUrl; //
	public $mainTitle;
	public $user;
	
	public $adminActions;

	public function _initialize(){	
		$this->curUrl = strtolower(u( CONTROLLER_NAME . '/' . ACTION_NAME ));
		session('[start]');
		$this->admin = session('admin');
		session('[pause]');
		
		//如果不是获取token, 检测是否登录
		if('login' != ACTION_NAME  && !$this->admin){
			$this->display('/login');
			exit;
		}
		
		//初始化数据
		$this->assign('admin', $this->admin);
		$this->adminActions = $this->admin['actions'];
		
		if(!$this->checkPermission()){
			$this->display('/noPermission');
			exit;
		}
	}

	//登录处理
	public function login(){
		$data['name'] 		= $_POST['name'];
		$data['password'] 	= $_POST['password'];
		
		$this->assign('post', $_POST);
		
		if(!$data['name'] || !$data['password']){
			$err = '信息不完整!';
			$this->assign('err', $err);
			return $this->display('/login');
		}	
		
		$adminMod = d('admin');
		$data['password'] = $adminMod->getPass($data['password']);
		$admin = $adminMod->exists($data);
		if(!$admin){
			$err = '账号与密码不匹配!';
			$this->assign('err', $err);
			return $this->display('/login');
		}
		//登录成功
		session('[start]');
		session('admin', $admin);
		session('[pause]');
		$adminMod->lastLogin($admin['id']);
		$this->redirect('index');
		return ;
	}
	
	
	//设置导航
	private function setNav(){
		$this->nav = self::filterNav($this->adminActions);		
		$curUrlPre = u('index');
		$curUrlPre = str_replace( '/'. c('DEFAULT_ACTION').'.'.c('URL_HTML_SUFFIX'), '', $curUrlPre);
		$curUrlPre = strtolower($curUrlPre);
		$n = strlen($curUrlPre);
		foreach($this->nav as $k => &$v){
			$pre = strtolower(substr($v['url'], 0, $n));
			if($pre != $curUrlPre)
				continue;

			$chr = substr($v['url'], $n,  1);
			if(false === $chr || '.' == $chr || '/' == $chr || $chr == ''){
				$this->leftNav = $v['actList'];
				$v['active'] = 1;
			}
		}
		

		$this->assign('nav', $this->nav);
	}
	
	protected function setRightAction($arr){
		$this->assign('rightAction', $arr);
	}
	
	//设置左侧导航 
	private function setLeftNav(){
		foreach($this->leftNav as $k=>$v){
			$this->leftNav[$k] = [
				'name' => $v[1],
				'url'  => strtolower(u(CONTROLLER_NAME . '/' . $v[0])),
				'list' => $v[2],
			];
		}
		
		foreach($this->leftNav as $k=>$v){
			if($v['url'] == $this->curUrl){ 
				$this->leftNav[$k]['active'] = 1 ;
				!$this->mainTitle ? ($this->mainTitle = $v['name']):null;
			}
			
			if($v['url'] == $this->parentUrl){
				$this->leftNav[$k]['active'] = 1;
				$this->mainTitle = $v['name'] . '<i>></i>' . $this->mainTitle;
			}
			
			foreach($v['list'] as $k2=>$v2){
				if($this->curUrl == $v2['url']){
					$v2['active'] = 1;
					$this->leftNav[$k]['active'] = 1;
					!$this->mainTitle && $this->mainTitle = $v['name'];
					$this->leftNav[$k]['list'][$k2] = $v2;
				}
			}
		}
			
		$this->assign('leftNav', $this->leftNav);
	}
	
	//设置TDK
	private function setTDK(){
		if(!is_array($this->arr)){
			$arr['title'] = '管理中心';
			$arr['keywords'] = '核动力驱动';
			$arr['description'] = '银河系最强!';
			$this->arr = $arr;
		}
		
		foreach($arr as $k=>$v){
			$this->assign($k, $v);
		}
	}
	
	/**
	 * 分页
	 * @param $mod
	 * @param $order 排序字段
	 * @param $perNum 每页条数
	 * @param $pageVar 模板的分页视图变量名称
	 * @return $list 数据变量
	 */
	protected function page($mod, $order = 'id', $perNum = 15, $pageVar = 'page'){
		$count   = $mod->count();
		$Page    = new Think\Page($count, $perNum);
		$show    = $Page->show();
		$list 	 = $mod->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign($pageVar, $show);
		return $list;
	}
	
	//页头模板
	public function header(){
		$this->display('header');
	}
	
	public function setMainTitle($mainTitle = ''){
		!$mainTitle ? $mainTitle = $this->mainTitle : null;
		$this->assign('main_title', $this->mainTitle);
	}
	//页尾模板
	public function footer(){
		$this->display('footer');
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
		
		if($modTdk->typeArr){
		    $typeList = [[ 'name'=>'type', 'list' => $modTdk->typeArr]];
		    $tdkRow = d('tdk')->getInfo($_GET['id']);
		    if(isset($tdkRow['type'])){
		        $typeList[0]['checked'] = $tdkRow['type'];
		        $typeList[0]['selected'] = $tdkRow['type'];
		    }
		    if(!isset($tdkRow['status']))
		        $typeList[0]['checked'] = 1;
		        	
		        $this->assign('typeList', $typeList);
		}
		
		ajaxReturn(0, '',	['content'=>$this->fetch($template)]);
	}
	
	//检测权限
	function checkPermission(){
		return true;
		$allAct = self::getAllAct();
		$actName = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
		if(!in_array($actName, $allAct))
			return true;
		
		if(!in_array($actName, $this->adminActions))
			return false;
	}
	
	//所有的控制器方法
	static function getAllAct(){
		$acts = c('actions');
		$arr = [];
		foreach($acts as $k=>$v){
			foreach($v as $v2)
				$arr[] = strtolower($k . '/' . $v2[0]);
		}
		return $arr;
	}
	
	//过滤没有权限的导航
	static function filterNav($acts){
		$allAct = self::getAllAct();
		$nav  = c('nav');
		$ctrs = c('ctrs');
		$arr = [];
		foreach($nav as $k=>$v){
			foreach($v as $k2 => $v2){
				$actName = strtolower($k . '/' . $v2[0]);
				if(!in_array($actName, $allAct)){
					
					continue;
				}
				
				if(!in_array($actName, $acts)){
					unset($nav[$k][$k2]);
				}
			}
			if(!$nav[$k][$k2]){
				unset($nav[$k]);
				continue;
			}
			$arr[$k] = [
				'name' => $ctrs[$k],
				'url'  => u($k . '/' . $v[0][0]),
				'actList' => $nav[$k],
			];
		}
		return $arr;
	}
	
	function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = ''){
		$this->setTDK();
		$this->setNav();
		$this->setLeftNav();
		$this->setMainTitle();
		parent::display($templateFile);
	}
}