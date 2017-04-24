<?php
use Think\Controller;

/**
 * 系统设置
 */
class SettingController extends PublicController {
	
	//网站设置
	public function index(){
       $this->config();
    }
	
	function config(){
		$mod = d('config');

		if($name = $_POST['name']) {
			$info = $mod->getInfo($name);

			if(!$mod->edit($_POST, $info['name']))
				return ajaxReturn(1, $mod->getError());
			
			return ajaxReturn(0, '修改成功!');
		}
		
		$list = $mod->getList();
		
		$this->assign('tabList', $mod->nameArr);
		$this->assign('list',	 $list);
		$this->display('config');
	}
	
	//微信菜单
	function wxmenu(){
		import('Org.Weixin.Weixin');
		$appId = c('weixin.APPID');
		$appToken = c('weixin.APPTOKEN');
		$appSecr = c('weixin.APPSECRET');
		$wx = new Org\Weixin\Weixin($appId, $appToken, $appSecr);
		$host = c('HOST');
		$menuArr = [
			"button"=>[
				["name"=>"百晓生", 
					"sub_button"=>[
						["type"=>"view","name"=>"资讯专区", "url"=> $host . 'Article/news'],
						["type"=>"view","name"=>"联系客服", "url"=> $host . 'Article/contact'],
						["type"=>"view","name"=>"关于我们", "url"=> $host . 'Article/about'],
					],
				],
				["name"=>"vip专区", 
					"sub_button"=>[
						["type"=>"view","name"=>"口子分类", "url"=> $host . 'Article/gossip']
					]
				],
				
				["name"=>"分销中心","type"=>"view",'url'=> $host . 'User']
			],
		];
		
		//口子分类
		$CateMod = d('Category');
		$list = $CateMod->getList(null,2);
		$cateArr = [];
		foreach($list as $v){
			$cateArr[] = [
				"type"=>"view",			"name"=> $v['name'], 
				"url"=> $host . 'Article/gossip/cid/' .$v['id']
			];
		}		
		$menuArr['button'][1]['sub_button'] = $cateArr;
		
		$this->assign('button', $menuArr['button']);
		if(IS_POST){
			$str = json_encode($menuArr, JSON_UNESCAPED_UNICODE);
			
			if($wx->menuAdd($str))
				return ajaxReturn(0,'更新成功!', ['content' => $this->fetch('wxmenu-button')]);
			
			return ajaxReturn(1, '更新失败!' . $wx->getError());
		}
		
		$str = $wx->menuView($menu);
		$menuArr2 = json_decode($str, true);
		$menuArr2  = $menuArr2['menu'];	
		$this->assign('diff', '');
		$this->assign('button2', $menuArr2['button']);
		$this->display();
	}
	
	function postres(){
		if(IS_POST){
			$str = json_encode($_POST);
			file_put_contents(ROOT_PATH . '/post.txt', $str);
		}
		
		echo '<form action="" method="post" ><input name="hihi" /><button>提交</button></form>';
	}
	
	
	function artistType(){
		$this->category(1, '艺术类型');
	}
	
	function region(){
		$this->category(2, '地区');
	}
	
	/**
	 * 悬赏分类
	 */
	function category($type = 1, $addName = '分类', $maxDeep = 1){
		$this->setRightAction([['name'=>'添加' . $addName, 'dialog'=>true,
			'url'=>u('cateEdit', ['type'=>$type]),]]);
			
		$rows = d('category')->getList(null, $type);
		$this->assign('rows', $rows);
		$this->assign('maxDeep', $maxDeep);
		$this->display('category');
	}
	function cateEdit(){
		//类目类别设置为文章
		$this->ajaxEdit('category', null, function($row, $mod){
			//添加子类
			if($_GET['act'] && ($pid = $_GET['pid']) ){
				$cateList[0]['selected'] = $pid;
				$cate = $mod->getInfo($pid);
				$type = $cate['type'];
			}
			
			if(isset($row['type'])){
				$type = $row['type'];
			}
			if(isset($_GET['type'])){
				$type = (int)$_GET['type'];
			}
			
			$cateList   = [[ 'name'=>'parent_id' ,'padd1' => true,
				'valueKey' => 'id', 'list' => $mod->getList(null, $type)]];
			
			if($_GET['act']){
				$cateList[0]['selected'] = $_GET['pid'];
			}
			if(isset($row['parent_id'])) $cateList[0]['selected'] = $row['parent_id'];
			$this->assign('type', $type);
			$this->assign('cateList',   $cateList);
			$this->assign('addChild',   $_GET['act']);
		});
	}
	
	function cateDel(){
		$id = (int)$_REQUEST['id'];
		if(d('category')->where('parent_id='.$id)->find()){
			ajaxReturn(1, '此类目下面有子类, 不能删除');
		}
		$this->ajaxDel('category');
	}
	

}