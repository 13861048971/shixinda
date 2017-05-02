<?php
use Think\Model;

/**
 * 任务模型
 */
class ConfigModel extends BaseModel{
	private $cacheKey = 'config_';
	public $nameArr = [
		'SMS' 		=> '短信设置(云片)',
		'message' 	=> '消息推送(友盟)',
		'config' 		=> '网站配置',
		'image' 	=> '切图配置',
		'about' 	=> '关于我们',
		'agreement' => '用户使用协议',
		'trade_note' => '交易担保说明', 
	];
	public $form = [
		'SMS' => [
			'apikey' => ['label' => 'apikey', 'r' => 3, 'type'=>'text'],
			'param'  => ['label'=>'验证码参数名', 'type'=>'text', 'r'=>4 ,'value'=>'code' ],
			'code'   => ['label' => '验证码模板', 'type'=> 'text', 'r' => 4],
			'vercode'=> ['label' => '专用验证码', 'type'=>'text', 'r'=>4],
		],
		'message' =>[
			'IOSappkey' => ['label' => '(IOS)appkey', 	  'r' => 5, 'type'=>'text'],
			'IOSappsecret' 	=> ['label' => '(IOS)appsecret', 'r' => 5, 'type'=>'text'],
			'ANDappkey' 	=> ['label' => '(Android)appkey', 	  'r' => 5, 'type'=>'text'],
			'ANDappsecret' 	=> ['label' => '(Android)appsecret', 'r' => 5, 'type'=>'text'],
			'nickname' 	=> ['label' => '推送管理员昵称', 'r' => 4, 'type'=>'text'],
			'avatar' 	=> ['label' => '头像', 'r' => 4, 'type'=>'image'],
		],
		'config'=>[
			'appName'	=> ['label'=>'网站名称','r'=>5],
			'email'		=> ['label'=>'电子邮箱','r'=>5],
			'cellphone'	=> ['label'=>'手机','r'=>5],
			'telephone'	=> ['label'=>'电话','r'=>5],
			'weixin'	=> ['label'=>'微信号','r'=>5],
			'right'		=> ['label'=>'权限申明','r'=>5],
			'icpNo'		=> ['label'=>'ICP备案号','r'=>5],
			'down'		=> ['label'=>'公司地址','r'=>5],
			'workday'	=> ['label'=>'工作时间','r'=>5,'value'=>'周一至周六 9:00－18:00'],
		    'seoTitle'  =>['label'=>'标题','r'=>5],
		    'seoKeywords' =>['label'=>'关键词','r'=>5],
		    'seoDescription' => ['label'=>'描述', 'type'=>'textarea']
		],
		'image'=>[
			'thumb'	=>['label'=>'缩略图尺寸', 	'value'=>'150*150'],
			// 'medium'=>['label'=>'中型图尺寸', 	'value'=>'450*600'],
			// 'water'	=>['label'=>'水印图片', 	'type' => 'image'],
			// 'waterText'=>['label'=>'水印文字', 	'value'=> '']
		],
		'about'=>[
			'content' => ['label'=>'内容', 'type'=>'textarea'],
		],
		'agreement'=>[
			'content' => ['label'=>'内容', 'type'=>'textarea'],
		],
		'trade_note'=>[
			'content' => ['label'=>'内容', 'type'=>'textarea'],
		]
	];
	/**
	 * 编辑or添加
	 */
	function edit($data, $name=null){
		$data['value'] = serialize($data['config']);
		
		if($name){
			$data['update_time'] = time();
			$return  = $this->data($data)->where(['name'=>$name])->save();

			if(!$return){
				$this->lastError = '修改任务失败!';
				return false;
			}	
			$this->_cacheReset($name);
			return $name;
		}
		
		$data['update_time'] = $data['add_time'] = time();
		
		if(!$this->create($data)) 
			return false;
		if(!($name = $this->add()))
			return $this->setError('添加失败!');
		$this->_cacheReset($name);
		return $name;
	}
	
	function getPageList($con, $fields = 'id'){
		$data = parent::getPageList($con, $fields);
		foreach($data['list'] as $k=>$v){
			$data['list'][$k] = $this->getInfo($v['id']);
		}
		
		return $data;
	}
	
	function getList(){
		$list = [];
		foreach($this->nameArr as $k=>$v){
			$info  = $this->getInfo($k);
			$nodes = $this->form[$k];
			$value = $info['value'];
			foreach($nodes as $k2=>$v2){
				$nodes[$k2]['name'] = 'config['.$k2.']';
				$value[$k2] && ($nodes[$k2]['value'] = $value[$k2]);
			}
			$list[$k]['text'] = $v;
			$list[$k]['node'] = $nodes;
		}
		
		//dump($list); exit;
		return $list;
	}
	
	//详情
	function getInfo($name){
		$info = $this->getCache($this->cacheKey.$name, 'config', $name);
		$info['value'] = unserialize($info['value']);
		if($val = $this->form[$name]){
			foreach($val as $k => $v){
				!$info['value'][$k] && $info['value'][$k] = $v['value'];
			}
		}
		
 		return $info;
	}
	
	function _cacheConfig($name){
		return $this->find($name);
	}
	
	/**
	 * 获取or设置默认模板
	 * @param string $themeName 模板名称,如果有参数,为设置默认模板
	 */
	function getTheme($themeName = null){
		$info = $this->getInfo('theme');
		$theme = $info['value']['theme'];
		$data = ['name'=>'theme', 'config'=>['theme'=>'default']];
		if($themeName){
			$name = 'theme';
			$data['config']['theme'] = $themeName;
			$this->edit($data, $name);
			return $themeName;
		}
		if(!$theme && $this->edit($data, $name))
			return $theme;
		return $theme;
	}
	
	//重置缓存
	function _cacheReset($name){
		return $this->resetCache($this->cacheKey.$name, 'config', $name);
	}
	
}