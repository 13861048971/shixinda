<?php
use Think\Controller;

/**
 * 管理员
 *
 */
class IndexController extends PublicController {
	public function _initialize(){
		$this->leftNav = [
			['name' => '概况', 'url'=> u('index')],
			['name' => '管理员' , 'url' => u('adminList')],
			['name' => '角色管理', 'url' => u('role')],
			['name' => '服务器信息', 'url' => u('phpInfo')],
		];
		parent::_initialize();
	}
	
    public function index(){
		$id = $this->admin['id'];
		$con = [ 'admin_id' => $id ];
		
		$admin = d('admin')->getInfo($id);
		
		$this->assign('admin', $admin);
		$this->display();
    }
	
	//修改管理员密码
	public function changePass(){
		$arr = [
			'password' => '请填写原密码',
			'passnew'  => '请填写新密码',
			'passnew2' => '请填写密码确认',
		];
		$data = $_POST;
		foreach($arr as $k=>$v){
			if(!$data[$k]){
				return ajaxReturn(1, $v);
			}
		}
		if(strlen($data['passnew'])< 6)
			return ajaxReturn(1, '请输入6位以上的密码!');
		if($data['passnew2'] != $data['passnew'])
			return ajaxReturn(1, '两次输入的新密码不同!');
		$mod = d('admin');
		$id = $this->admin['id'];
		
		
		$d = ['id'=>$id, 'password'=> $mod->getPass($data['password'])];
		if(!$mod->exists($d))
			return ajaxReturn(1, '原密码错误!');
		
		$d = [
			'id' => $id,	
			'password'=> $data['passnew']
		];
		if(!$mod->edit($d, $id))
			return ajaxReturn(1, $mod->getError());
		return ajaxReturn(0, '操作成功,重新登录后生效!');
	}
	
	//更新管理员qrcode image
	public function updateAdminQr(){
		if( ($img = $_POST['image']) && ($id = (int)$this->admin['id']) ){
			$mod = d('admin');
			if(!$mod->edit(['image' => $img], $this->admin['id'])){
				return ajaxReturn(1, $mod->getError());
			}
			session('admin.image', $img);
			return ajaxReturn(0);
		}
		return ajaxReturn(1);
	}
	
	/**
	 * 服务器信息
	 */
	public function phpInfo(){
		import('Common.Common.mdl_serverinfo');
		$ser = new mdl_serverinfo();
		$serInfo = $ser->main();
		$this->assign('serInfo', $serInfo);
		
		$runMode = php_sapi_name();
		ini_get('safe_mode') && $runMode = '安全模式';
		$items = [
			'os' 		=> PHP_OS, 
			'httpSer' 	=> $_SERVER["SERVER_SOFTWARE"], 
			'runMode'	=> $runMode,
			'version'	=> PHP_VERSION,
		];
		
		$items['程序最多允许使用内存量&nbsp;memory_limit']=ini_get("memory_limit");
        $items['POST最大字节数&nbsp;post_max_size']=ini_get("post_max_size");
        $items['允许最大上传文件&nbsp;upload_max_filesize']=ini_get("upload_max_filesize");
        $items['程序最长运行时间&nbsp;max_execution_time']=ini_get("max_execution_time");
        $disableFunc = get_cfg_var("disable_functions");
        $items['被禁用的函数&nbsp;disable_functions']=$disableFunc?$disableFunc:'无';
		
		
		$this->display('serInfo');
	}
	
	public function logout(){
		session('[start]');
		session('admin', null);
		$this->redirect('admin');
		return ;
	}
	
	//管理员列表
	public function adminList(){
		$rightBtn = [
			['name' => '添加管理员','url'=>u('adminEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
		];
		$this->setRightAction($rightBtn);
		$data = d('admin')->getPageList();
		$this->assign($data);
		$this->display('adminList');
	}
	
	//编辑管理员
	public function adminEdit(){
		$this->ajaxEdit('admin', null, function($row, $mod){
			$list = d('adminRole')->getList();
			$roleList = [[
				'name' => 'role_id',
				'selected' => $row['role_id'],
				'list' => $list,
				'valueKey' => 'id',
			]];
			$this->assign('roleList', $roleList);
		});
	}
	
	//删除管理员
	public function adminDel(){
		$this->error();
	}
	
	//角色管理
	public function roleList(){
		$rightBtn = [
			['name' => '添加角色','url'=>u('roleEdit'), 'dialog' => 1, 'dialog-lg' => 1 ]
		];
		$data = d('adminRole')->getPageList();
		$this->assign($data);
		$this->setRightAction($rightBtn);
		$this->display('roleList');
	}
	
	public function roleEdit(){
		$this->ajaxEdit('adminRole', null, function($row, $mod){
			$actions = $mod->getActionList($row['actions']);

			$this->assign('actions', $actions);
		});
	}
	
}