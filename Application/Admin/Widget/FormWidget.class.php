<?php 
/**
 * 生成表单控件
 **/
use Think\Controller;

class FormWidget extends Controller{
	
	static $useImg = false;
	static $imgNum  = 0;
	static $fileNum  = 0;
	static $dateNodeNum = 0;
	static $daterangeNodeNum = 0;
	static $img = [
		'label'=>'图片', 
		'name' => 'img',
		'path'	=> ''
	];
	
	function index($data){
		extract($data);
		!$method && $method = 'get';
		$enctype && ($enctype = 'enctype="' . $enctype . '"');
		echo '<form '.$enctype.' action="'. $action. '" method="' .$method. '"  class="'. $class .'" target="'. $target .'">';
		$this->elements($eles);
		
		echo '<div class="form-group">
			 <div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary">提交</button>
			 </div>
		  </div>';
		echo '</form>';
	}
	
	//生成表单元素
	function elements($data){
		foreach($data as $v){
			$this->row($v);
		}
	}
	
	/**
	 * @param [] $data ['type'=>'', name="", value=>'', 'checked'=>,'selected'=>]
	 */
	function node($data, $return = false){
		$data['return'] = $return;
		extract($data);
		$required = $require ? 'required':'';
		if('string' == $type){
			$str = $value . '<input type="hidden" name="'.$name.
				'" value="'. $value .'" '. $required .'>';
		}
		
		if('file' == $type){
			$str = '<input type="file" class="form-control" name="'. $name .
				'" value="'. $value .'" ' . $required . '>';
		}
		
		if('text' == $type || !isset($type)){
			$str = '<input type="text" class="form-control" name="'. $name .
				'" value="'. $value .'" ' . $required . '>';
		}
		if('textarea' == $type){
			$str = '<textarea class="form-control" placeholder="'. $placeholder .'" rows="8" name="'. $name .
				'" ' . $required . '>'. $value .'</textarea>';
		}
		//date
		if('date' == $type){
			!isset($data['istime']) && $data['istime'] = true;
			!isset($data['format']) && ($data['format'] = 'YYYY-MM-DD hh:mm:ss');
			
			$this->assign('dateNodeNum', self::$dateNodeNum);
			$this->assign($data);
			$this->display('Widget:Form:date');
			self::$dateNodeNum++;
			return;
		}
		if('daterange' == $type){
			$str = $this->daterange($data, $return);
		}
		
		//百分比
		if('per' == $type){
			$str = '<div class="input-group"><input type="text" class="form-control" name="'. $name .
				'" value="'. $value .'" ' . $required . '><span class="input-group-addon">%</span></div>';
		}
		if('price' == $type){
			$str = '<div class="input-group"><span class="input-group-addon">￥</span><input type="text" class="form-control" name="'. $name .
				'" value="'. $value .'" ' . $required . '></div>';
		}
		//编辑器
		if('editor' == $type){
			$str = '<textarea class="form-control kind-editor" name="'. $name .
				'" ' . $required . '>'. $value .'</textarea>';
		}
		
		if('radio' == $type || 'checkbox' == $type || 'select'== $type)
			$str = w('select/' . $type, [$data]);
		
		if('image' == $type){
			$str = $this->img($data);
		}
		
		if('album' == $type){
			$str = $this->album($data, $return);
		}
		
		if($return){
			return $str;
		}
		echo $str;
	}
	
	public function img($name, $label=null , $path=null,$req=false){
		$img = self::$img;
		if(is_array($name)) extract($name);

		$path  ? $img['path']  		= $path  : null;
		!$path &&  ($img['path'] = $value);
		$label ? $img['label']  	= $label : null;
		$name  ? $img['name']  		= $name  : null;
		$req   ? $img['required'] 	= true   : null;
		!$img['required'] && $img['required'] = $name['required'];
		self::$imgNum++;
		$img['idName']  .= 'img-input-id-' . self::$imgNum;
		$img['preview'] .= 'img-preview-'  . self::$imgNum;
		$this->assign('img', $img);
		$this->assign('imgL', $l);
		$this->assign('imgR', $r);
		if($return){
			return $this->fetch('Widget:Form:img');
		}
		$this->display('Widget:Form:img');
	}
	
	//相册图片
	public function album($data, $return = false){
		$list = $data['list'];
		$str = '<ul class="album" type="image">';
		foreach( $list as $k=>$v ){
			$str .= '<li rank="'. ($k+1) .'"  class="img-thumbnail '. ($v['default']?'default':'') .'">'.
				'<a href="'. $v['path'] .'" target="_blank"><img src="'. $v['path'] .'"></a>
			<input type="hidden" name="'. $data['name'] . '['. $v['id'] .']['. ($k+1) .']  value="' . $v['path'] . '">
			<span class="del"></span><span class="set-default">设为默认</span>
			</li>';
		}
		$str .= '</ul>';
		
		if($return)
			return $str;
		
		echo $str;
	}
	
	public function per(){
		
	}
	
	function row($data){
		$l = $data['l'] ? $data['l'] : 2; 
		$r = $data['r'] ? $data['r'] : 9; 
		$data['l'] = $l;
		$data['r'] = $r;
		if('image' == $data['type']) {
			 echo $this->node($data, 1);
			 return;
		}
		$req = $data['require'];
		!$req && $req = $data['required'];
		$req && $req = '<i class="required-star">*</i>';
		echo '<div class="form-group">
				<div class="col-sm-'.$l.' control-label">'.$req. $data['label'] . '</div>
				<div class="col-sm-'.$r.'">
					'. $this->node($data, 1) .'
				</div>
			  </div>';
	}

	//日期间隔选择
	function daterange($data, $return = false){
		!$data['timePicker'] && $data['timePicker'] = "false";
		!$data['opens'] && $data['opens'] = "left";
		$this->assign($data);
		$this->assign('daterangeNodeNum', self::$daterangeNodeNum);
		self::$daterangeNodeNum++;
		if($return){
			return $this->fetch('Widget:Form:daterange');
		}
		$this->display('Widget:Form:daterange');
	}
}

