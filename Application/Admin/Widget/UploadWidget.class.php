<?php 
/**
 * 上传文件控件
 **/
use Think\Controller;

class UploadWidget extends Controller{
	//是否已近引用了图片
	static $useImg = false;
	static $imgNum  = 0;
	static $fileNum  = 0;
	static $img = [
		'label'=>'图片', 
		'name' => 'img',
		'path'	=> ''
	];
	
	/**
	 * 上传图片
	 */
	public function img($name, $label=null , $path=null,$req=false, $type = 'image'){
		$img = self::$img;
		if(is_array($name)) extract($name);
		
		$path  ? $img['path']  		= $path  : null;
		!$path &&  ($path = $value);
		$label ? $img['label']  	= $label : null;
		$name  ? $img['name']  		= $name  : null;
		$req   ? $img['required'] 	= true   : null;
		$type   ? $img['type'] 	= $type   : null;
		self::$imgNum++;
		$img['idName']  .= 'img-input-id-' . self::$imgNum;
		$img['preview'] .= 'img-preview-'  . self::$imgNum;
		$this->assign('img', $img);
		if($return){
			return $this->fetch('Widget:upload:img');
		}
		$this->display('Widget:Upload:img');
	}
	
	
	
	public function file(){
		$this->display('Widget:Ppload:file');
	}
}

