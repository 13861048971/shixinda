<?php 
/**
 * 可视化编辑器控件
 **/
use Think\Controller;

class EditorWidget extends Controller{
	
	static $editorNum = 0;
	/**
	 * @param array $data
	 * $data: [ editorId:, width:, height:, type:simple|default ]
	 */
	public function index($data){
		$editorNum++;
		$this->assign($data);
		$this->display('Widget:editor:index');
	}
}

